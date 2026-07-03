/**
 * buhoFunctions.js
 * Lógica pura de integración con el agente BuhoPrinter (localhost HTTP).
 * Sin dependencias de Vue — testeable en aislamiento.
 */
import { publicIpv4 } from 'public-ip';

// Puertos en los que puede estar escuchando BuhoPrinter
const BUHO_PORTS = [8181, 8282, 8383, 8484];

// Estado interno del módulo
let _buhoPort        = null;
let _buhoActive      = false;
let _printerName     = null;
let _clientPublicIp  = null;

// --------------------------------------------------------------------------
// Utilitaria interna: construye la URL base según el puerto activo
// --------------------------------------------------------------------------
function _baseUrl() {
    return `https://localhost:${_buhoPort}`;
}

// --------------------------------------------------------------------------
// Utilitaria interna: fetch con timeout corto para el ping de detección
// --------------------------------------------------------------------------
async function _pingPort(port) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), 1500);
    try {
        const res = await fetch(`https://localhost:${port}/status`, {
            signal: controller.signal,
        });
        return res.ok;
    } catch {
        return false;
    } finally {
        clearTimeout(timer);
    }
}

// --------------------------------------------------------------------------
// fetchClientPublicIp
// Consulta la IP pública del cliente usando el paquete public-ip.
// Almacena el resultado en caché para evitar llamadas repetidas.
// --------------------------------------------------------------------------
export async function fetchClientPublicIp() {
    if (_clientPublicIp) return _clientPublicIp;
    try {
        _clientPublicIp = await publicIpv4();
    } catch (err) {
        console.warn('[BuhoPrinter] No se pudo obtener la IP pública del cliente:', err);
        _clientPublicIp = null;
    }
    return _clientPublicIp;
}

// --------------------------------------------------------------------------
// getClientPublicIp
// Retorna la IP pública del cliente previamente obtenida (puede ser null
// si fetchClientPublicIp() aún no fue llamada o falló).
// --------------------------------------------------------------------------
export function getClientPublicIp() {
    return _clientPublicIp;
}

// --------------------------------------------------------------------------
// startConnection
// Recorre los puertos hasta encontrar un agente activo.
// Actualiza _buhoPort, _buhoActive y carga la impresora por defecto.
// --------------------------------------------------------------------------
export async function startConnection() {
    _buhoActive = false;
    _buhoPort   = null;

    for (const port of BUHO_PORTS) {
        const alive = await _pingPort(port);
        if (alive) {
            _buhoPort   = port;
            _buhoActive = true;
            // Carga la impresora por defecto al conectar
            await setDefaultPrinter();
            return;
        }
    }
    console.warn('[BuhoPrinter] No se encontró agente en ningún puerto:', BUHO_PORTS);
}

// --------------------------------------------------------------------------
// isBuhoConnected
// Retorna el estado actual de conexión (booleano).
// --------------------------------------------------------------------------
export function isBuhoConnected() {
    return _buhoActive;
}

// --------------------------------------------------------------------------
// getBuhoBaseUrl
// Retorna la URL base del agente BuhoPrinter activo (ej: https://localhost:8181).
// Retorna null si no hay conexión activa.
// --------------------------------------------------------------------------
export function getBuhoBaseUrl() {
    return _buhoActive ? _baseUrl() : null;
}

// --------------------------------------------------------------------------
// setDefaultPrinter
// Carga la impresora por defecto del sistema desde BuhoPrinter.
// --------------------------------------------------------------------------
export async function setDefaultPrinter() {
    if (!_buhoActive) return;
    try {
        const res  = await fetch(`${_baseUrl()}/printers`);
        const data = await res.json();
        const def  = data.find(p => p.is_default);
        if (def) _printerName = def.name;
    } catch (err) {
        console.warn('[BuhoPrinter] No se pudo obtener la impresora por defecto:', err);
    }
}

// --------------------------------------------------------------------------
// getUpdatedConfig
// Retorna el objeto de configuración con el nombre de impresora activo.
// Mantiene el mismo contrato que getUpdatedConfig() de function-qztray.js.
// --------------------------------------------------------------------------
export function getUpdatedConfig() {
    return { printer: _printerName };
}

// --------------------------------------------------------------------------
// getBuhoPrinters
// Retorna el listado de impresoras disponibles del sistema como array de strings.
// Equivalente a qz.printers.find().
// --------------------------------------------------------------------------
export async function getBuhoPrinters() {
    if (!_buhoActive) return [];
    try {
        const res  = await fetch(`${_baseUrl()}/printers`);
        const data = await res.json();
        // BuhoPrinter retorna [{ name, is_default }] → extraemos solo el nombre
        return data.map(p => (typeof p === 'object' ? p.name : p));
    } catch (err) {
        displayError(err);
        return [];
    }
}

// --------------------------------------------------------------------------
// getBuhoPrintersWithDefaults
// Retorna el listado completo de impresoras con nombre e is_default.
// Usada para sincronizar con el backend preservando la impresora predeterminada.
// --------------------------------------------------------------------------
export async function getBuhoPrintersWithDefaults() {
    if (!_buhoActive) return [];
    try {
        const res  = await fetch(`${_baseUrl()}/printers`);
        const data = await res.json();
        // Normaliza por si BuhoPrinter devuelve strings simples
        return data.map(p => typeof p === 'object' ? p : { name: p, is_default: false });
    } catch (err) {
        displayError(err);
        return [];
    }
}

// --------------------------------------------------------------------------
// buhoPrint
// Envía un trabajo de impresión PDF base64 a BuhoPrinter.
// config: { printer: string }
// printData: array de items con { type, format, data } — compatible con qz.print()
//   Solo se procesan items de tipo PDF base64.
// --------------------------------------------------------------------------
export async function buhoPrint(config, printData) {
    if (!_buhoActive) {
        console.warn('[BuhoPrinter] No hay conexión activa. Impresión cancelada.');
        return;
    }

    for (const item of printData) {
        // Solo se soporta PDF base64
        if (item.format !== 'base64' && item.format !== 'pdf') {
            console.warn('[BuhoPrinter] Formato no soportado, se omite:', item.format);
            continue;
        }

        const payload = {
            printer : config?.printer ?? _printerName,
            data    : item.data,
            format  : 'pdf',
        };

        const res = await fetch(`${_baseUrl()}/print`, {
            method : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body   : JSON.stringify(payload),
        });

        if (!res.ok) {
            const text = await res.text();
            throw new Error(`[BuhoPrinter] Error en /print: ${res.status} — ${text}`);
        }
    }
}

// --------------------------------------------------------------------------
// buhoFetchAndPrint
// Obtiene un PDF desde una URL, lo convierte a base64 y lo imprime.
// Reemplaza el patrón printPdfFromUrl + qz.print usado en los componentes.
// --------------------------------------------------------------------------
export async function buhoFetchAndPrint(url, config = null) {
    if (!url) {
        console.warn('[BuhoPrinter] buhoFetchAndPrint: URL vacía, se omite.');
        return;
    }
    if (!_buhoActive) {
        console.warn('[BuhoPrinter] No hay conexión activa. Impresión cancelada.');
        return;
    }

    // Descarga el PDF como ArrayBuffer
    const res = await fetch(url, { credentials: 'include' });
    if (!res.ok) {
        throw new Error(`[BuhoPrinter] Error al descargar PDF: ${res.status} ${res.statusText}`);
    }

    const arrayBuffer = await res.arrayBuffer();
    const bytes       = new Uint8Array(arrayBuffer);

    // Convierte a base64 en trozos para evitar límites de pila en chrome
    let binary = '';
    const chunkSize = 0x8000;
    for (let i = 0; i < bytes.length; i += chunkSize) {
        binary += String.fromCharCode.apply(null, bytes.subarray(i, i + chunkSize));
    }
    const base64Data = btoa(binary);

    const cfg = config ?? getUpdatedConfig();
    await buhoPrint(cfg, [{ type: 'pdf', format: 'base64', data: base64Data }]);
}

// --------------------------------------------------------------------------
// displayError
// Manejo de errores centralizado — equivalente a displayError de qztray.
// --------------------------------------------------------------------------
export function displayError(err) {
    console.error('[BuhoPrinter]', err);
}
