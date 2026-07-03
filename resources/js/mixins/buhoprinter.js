/**
 * buhoprinter.js
 * Mixin Vue 2 que expone la integración con BuhoPrinter a los componentes.
 * Delega toda la lógica al utils puro (buhoFunctions.js) y añade
 * reactividad de instancia mediante data.buhoActive.
 */
import {
    startConnection,
    isBuhoConnected,
    getUpdatedConfig,
    buhoPrint,
    buhoFetchAndPrint,
    getBuhoPrinters,
    getBuhoPrintersWithDefaults,
    getBuhoBaseUrl,
    fetchClientPublicIp,
    getClientPublicIp,
    displayError,
} from '../utils/buhoFunctions';

// Cache módulo-nivel para print_destination. Se carga una vez por sesión de página.
// null = no cargado aún | true = Centralizado (backend) | false = Directo (BuhoPrinter local)
let _printDestinationCache = null;

export const buhoprinter = {
    data() {
        return {
            // Estado reactivo de conexión — equivalente a qz.websocket.isActive()
            buhoActive: false,
        };
    },

    computed: {
        isBuhoActive() {
            return this.buhoActive;
        },
    },

    methods: {
        /**
         * Inicia la conexión con el agente BuhoPrinter y actualiza el estado reactivo.
         * Reemplaza: startConnection() + qz.websocket.connect()
         */
        async startConnectionBuho() {
            await startConnection();
            this.buhoActive = isBuhoConnected();
        },

        /**
         * Retorna la configuración de impresión activa (printer name).
         * Mantiene el mismo contrato que getUpdatedConfig() global de qztray.
         */
        getUpdatedConfig,

        /**
         * Envía un trabajo de impresión PDF base64 a BuhoPrinter.
         * Reemplaza: qz.print(config, printData)
         *
         * @param {object} config     - { printer: string }
         * @param {Array}  printData  - array de items con { type, format, data }
         */
        async buhoPrint(config, printData) {
            try {
                await buhoPrint(config, printData);
                this.$notify({ title: '', message: 'Impresión en proceso...', type: 'success' });
            } catch (err) {
                displayError(err);
            }
        },

        /**
         * Descarga un PDF desde una URL y lo imprime directamente.
         * Reemplaza: el patrón printPdfFromUrl + window.qz.print() de los componentes.
         *
         * @param {string} url - URL completa del PDF (usa credentials: 'include')
         */
        async printPdfFromUrl(url) {
            try {
                await buhoFetchAndPrint(url, this.getUpdatedConfig());
                this.$notify({ title: '', message: 'Impresión en proceso...', type: 'success' });
            } catch (err) {
                displayError(err);
            }
        },

        /**
         * Retorna el listado de impresoras disponibles del sistema.
         * Reemplaza: qz.printers.find()
         *
         * @returns {Promise<string[]>}
         */
        async getBuhoPrinters() {
            return getBuhoPrinters();
        },

        /**
         * Retorna el listado de impresoras con nombre e is_default.
         * Usado para sincronizar con el backend preservando la impresora predeterminada.
         *
         * @returns {Promise<Array<{name: string, is_default: boolean}>>}
         */
        async getBuhoPrintersWithDefaults() {
            return getBuhoPrintersWithDefaults();
        },

        /**
         * Retorna la URL base del agente BuhoPrinter activo (ej: https://localhost:8181).
         * Disponible tras llamar a startConnectionBuho().
         *
         * @returns {string|null}
         */
        getBuhoBaseUrl() {
            return getBuhoBaseUrl();
        },

        /**
         * Consulta la IP pública del cliente mediante el paquete public-ip.
         * El resultado se cachea en módulo; llamadas subsiguientes retornan el valor almacenado.
         *
         * @returns {Promise<string|null>}
         */
        async fetchClientPublicIp() {
            return fetchClientPublicIp();
        },

        /**
         * Retorna la IP pública del cliente previamente obtenida.
         * Retorna null si fetchClientPublicIp() aún no fue llamada o falló.
         *
         * @returns {string|null}
         */
        getClientPublicIp() {
            return getClientPublicIp();
        },

        /**
         * Imprime un documento PDF según el destino configurado en restaurant_configurations.
         * - print_destination = false (Directo):   envía directo al agente BuhoPrinter local.
         * - print_destination = true  (Centralizado): envía vía backend → Redis → BuhoPrinter.
         *
         * Reemplaza las llamadas directas a printViaBackend en los componentes.
         *
         * @param {string}      url         - URL completa del PDF
         * @param {string|null} printerName - Nombre de la impresora destino (null = predeterminada)
         */
        async printDocument(url, printerName = null) {
            const isCentralized = await this._resolvePrintDestination();

            if (isCentralized) {
                await this.printViaBackend(url, printerName);
            } else {
                // Modo directo: conectar al agente local si aún no está activo
                if (!this.isBuhoActive) {
                    await this.startConnectionBuho();
                }
                try {
                    // Impresora: localStorage del equipo → parámetro → predeterminada del agente
                    const localPrinter = localStorage.getItem('buho_direct_printer_name');
                    const config = localPrinter
                        ? { printer: localPrinter }
                        : (printerName ? { printer: printerName } : this.getUpdatedConfig());
                    await buhoFetchAndPrint(url, config);
                    this.$notify({ title: '', message: 'Impresión en proceso...', type: 'success' });
                } catch (err) {
                    displayError(err);
                }
            }
        },

        /**
         * Obtiene el valor de print_destination desde el backend (cacheado por sesión).
         * @returns {Promise<boolean>}
         */
        async _resolvePrintDestination() {
            if (_printDestinationCache !== null) return _printDestinationCache;
            try {
                const { data } = await this.$http.get('/restaurant/printers/config');
                _printDestinationCache = data.data?.print_destination ?? false;
            } catch {
                _printDestinationCache = false;
            }
            return _printDestinationCache;
        },

        /**
         * Manejo de errores centralizado.
         * Reemplaza: displayError() global de qztray.
         */
        displayError,

        /**
         * Descarga un PDF desde una URL, lo convierte a base64 y registra una PrintOrder
         * en el backend para que sea publicada en Redis y consumida por BuhoPrinter.
         * Reemplaza la llamada directa al agente BuhoPrinter desde el frontend.
         *
         * @param {string}      url         - URL completa del PDF (usa credentials: 'include')
         * @param {string|null} printerName - Nombre de la impresora destino. Si es null,
         *                                    el backend asignará la impresora predeterminada.
         */
        async printViaBackend(url, printerName = null) {
            try {
                // Descarga el PDF respetando la sesión del tenant
                const response = await fetch(url, { credentials: 'include' });
                if (!response.ok) {
                    throw new Error(`Error al descargar PDF: ${response.status}`);
                }

                // Convierte el blob a base64
                const blob = await response.blob();
                const base64 = await new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onloadend = () => resolve(reader.result.split(',')[1]);
                    reader.onerror  = reject;
                    reader.readAsDataURL(blob);
                });

                // Obtiene la IP pública del cliente (cacheada tras la primera llamada)
                const clientPublicIp = await fetchClientPublicIp();

                // Registra la orden de impresión — el Observer la publica en Redis automáticamente
                const orderResponse = await this.$http.post('/restaurant/print-orders', {
                    pdf_b64:          base64,
                    name_printer:     printerName || null,
                    client_public_ip: clientPublicIp || null,
                });

                if (orderResponse.status === 201) {
                    this.$notify({ title: '', message: 'Impresión en proceso...', type: 'success' });
                }
            } catch (err) {
                // Mostrar el mensaje del backend si es un error de validación (impresoras no configuradas)
                const backendMessage = err?.response?.data?.message;
                if (backendMessage) {
                    this.$message({ message: backendMessage, type: 'warning' });
                } else {
                    displayError(err);
                }
            }
        },
    },
};
