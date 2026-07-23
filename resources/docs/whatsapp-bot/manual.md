---
title: "Manual del Bot de WhatsApp"
sidebar_label: "Bot WhatsApp"
sidebar_position: 1
---

# Manual del Bot de WhatsApp

El **bot de WhatsApp** permite a los vendedores autorizados conversar con el sistema desde su WhatsApp para consultar productos, registrar clientes y emitir comprobantes en lenguaje natural.

---

## Cómo se usa

El vendedor conversa con el bot por WhatsApp desde el número conectado. Para iniciar una sesión escribe `/bot`. A partir de ese momento puede pedir productos, clientes, emitir comprobantes y reenviar PDFs en lenguaje natural.

### Comandos disponibles

- `/bot` — inicia o retoma la sesión.
- `/fin` — cierra la sesión actual.
- `/p` — pausa al bot (útil cuando el dueño quiere intervenir manualmente). Se retoma con `/bot`.

Cada sesión expira automáticamente tras **30 minutos sin actividad**. Los comandos se pueden personalizar en **Comandos del bot**.

---

## Flujo para emitir un comprobante

1. El vendedor escribe `/bot` y luego solicita el comprobante en lenguaje natural, por ejemplo: *"genera una boleta con 5 galletas para Piero León"*.
2. El bot busca el producto en el catálogo, valida el stock, busca/registra al cliente y prepara un **borrador** con el resumen.
3. El vendedor responde **sí** para emitir o **no** para cancelar. Si quiere modificar el comprobante, puede decir *"agrégale 3 yogures"*, *"ponle descuento de 5"*, *"cambia el cliente a Brigitte"*, etc.
4. Al confirmar, el sistema emite el comprobante y envía el PDF al vendedor.

---

## Qué puede hacer

- Buscar productos por nombre, código interno o código de barras.
- Buscar clientes registrados por nombre o documento.
- Registrar clientes nuevos consultando RENIEC/SUNAT.
- Preparar y emitir boletas y facturas.
- Aplicar descuentos globales en soles.
- Listar comprobantes recientes y reenviar sus PDFs.
- Consultar el estado SUNAT de un comprobante.

---

## Qué no puede hacer

- Crear o modificar productos del catálogo (eso se hace desde Pro8 web).
- Modificar el catálogo de clientes después de registrarlos.
- Emitir notas de crédito o débito.
- Abrir caja (debe estar abierta antes de iniciar el flujo de emisión).

---

## Botones de la pantalla de configuración

- **Comprobar estado**: consulta el estado de la conexión con el WhatsApp del bot.
- **Reiniciar conexión**: reinicia la sesión de WhatsApp sin perder la instancia.
- **Renovar instancia**: borra y recrea la instancia. Requiere escanear el QR de nuevo.
- **Conectar nuevo número**: desconecta el número actual y permite vincular uno nuevo.

---

## Buenas prácticas

- Activa el bot por usuario desde **Usuarios → editar → tab Datos personales**, completando el campo *N° Celular personal* y el switch *Bot de WhatsApp*.
- Si suspendes a un usuario (`locked`), el bot lo rechaza aunque tenga el bot habilitado.
- El número conectado (dueño) puede hablar consigo mismo y el bot le responde como cualquier vendedor.
- Para que el bot procese mensajes el toggle **Bot activo** del tab *Bot conversacional* debe estar en `Sí`.
