<template>
    <div class="whatsapp-bot-manual">
        <div class="page-header pe-0">
            <h2><a href="#"><i class="fas fa-cogs"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Configuración</span></li>
                <li><span class="text-muted">Manual y alcances</span></li>
            </ol>
        </div>

        <div class="card card-dashboard border tab-content-default row-new row-mx-0">
            <div class="card-body">
                <el-tabs v-model="activeName">
                    <el-tab-pane class="mb-3" name="first">
                        <span slot="label"><h3 class="m-0 mt-2">Manual y alcances</h3></span>

                        <section class="mt-3">
                            <h4 class="manual-title">Cómo se usa</h4>
                            <p>
                                El vendedor conversa con el bot por WhatsApp desde el número conectado.
                                Para iniciar una sesión escribe <code>{{ triggerCmd }}</code>. A partir de ese momento puede pedir
                                productos, clientes, emitir comprobantes y reenviar PDFs en lenguaje natural.
                            </p>
                            <p><strong>Comandos disponibles:</strong></p>
                            <ul>
                                <li><code>{{ triggerCmd }}</code> — inicia o retoma la sesión.</li>
                                <li><code>{{ exitCmd }}</code> — cierra la sesión actual.</li>
                                <li><code>{{ pauseCmd }}</code> — pausa al bot (útil cuando el dueño quiere intervenir manualmente). Se retoma con <code>{{ triggerCmd }}</code>.</li>
                            </ul>
                            <p class="text-muted">
                                Cada sesión expira automáticamente tras 30 minutos sin actividad.
                                Los comandos se pueden personalizar en <strong>Comandos del bot</strong>.
                            </p>
                        </section>

                        <el-collapse v-model="activeCollapses" class="mt-4">
                            <el-collapse-item title="Flujo para emitir un comprobante" name="flow">
                                <ol>
                                    <li>El vendedor describe el pedido (ej. <em>"hazme una boleta de 3 yogures para Juan Pérez"</em>).</li>
                                    <li>El bot busca los productos en el catálogo real y arma un resumen con total e IGV.</li>
                                    <li>Si el cliente no existe pero hay DNI, lo registra automáticamente vía RENIEC.</li>
                                    <li>Pregunta si confirma. Solo emite si el vendedor responde afirmativamente.</li>
                                    <li>Al confirmar, emite contra SUNAT y manda el PDF por el mismo chat.</li>
                                </ol>
                            </el-collapse-item>

                            <el-collapse-item name="can">
                                <template slot="title">
                                    <span class="text-success font-weight-bold">Qué puede hacer</span>
                                </template>
                                <ul>
                                    <li>Consultar productos del catálogo con precio (IGV incluido) y stock.</li>
                                    <li>Buscar clientes por nombre, DNI o RUC.</li>
                                    <li>Registrar nuevos clientes desde RENIEC con solo el DNI.</li>
                                    <li>Emitir boletas y facturas reales contra SUNAT.</li>
                                    <li>Enviar el PDF del comprobante por WhatsApp después de emitir.</li>
                                    <li>Listar comprobantes recientes y reenviar sus PDFs.</li>
                                    <li>Consultar el estado SUNAT de cualquier comprobante.</li>
                                    <li>Devolver datos del negocio (RUC, razón social).</li>
                                </ul>
                            </el-collapse-item>

                            <el-collapse-item name="cannot">
                                <template slot="title">
                                    <span class="text-danger font-weight-bold">Qué no puede hacer</span>
                                </template>
                                <ul>
                                    <li>Atender a números que no estén en la lista de usuarios autorizados.</li>
                                    <li>Emitir un comprobante sin que el vendedor confirme con un mensaje afirmativo.</li>
                                    <li>Emitir si el vendedor no tiene una caja abierta (debe aperturarla primero).</li>
                                    <li>Emitir factura si el cliente no tiene RUC.</li>
                                    <li>Emitir boleta a consumidor final si el monto supera S/ 700 sin DNI del cliente.</li>
                                    <li>Procesar mensajes en ráfaga del mismo usuario: solo atiende uno a la vez por número.</li>
                                    <li>Detectar automáticamente cuando el dueño responde manualmente desde otra app: el vendedor debe escribir <code>{{ pauseCmd }}</code> para pausar.</li>
                                    <li>Modificar un comprobante después de emitido.</li>
                                </ul>
                            </el-collapse-item>

                            <el-collapse-item title="Botones de la pantalla de configuración" name="buttons">
                                <p class="text-muted">
                                    Cuando entras a <strong>Configuración del Bot</strong> y la instancia ya está conectada
                                    (o desconectada), aparecen los siguientes botones de acción:
                                </p>
                                <ul>
                                    <li>
                                        <strong>Comprobar estado</strong> — consulta a la plataforma de mensajería el estado
                                        actual de la sesión y lo refresca en pantalla. No cambia nada, solo informa.
                                        Útil para verificar manualmente sin esperar el polling automático.
                                    </li>
                                    <li>
                                        <strong>Reiniciar conexión</strong> <em>(solo si está conectado)</em> — reinicia
                                        la sesión activa de la instancia sin perder la vinculación con WhatsApp.
                                        Útil cuando el bot está abierto pero deja de responder. <strong>No requiere escanear QR.</strong>
                                    </li>
                                    <li>
                                        <strong>Reconectar instancia</strong> <em>(solo si está desconectado)</em> —
                                        vuelve a generar el código QR de la misma instancia para que escanees y recuperes
                                        la conexión con WhatsApp sin perder ni eliminar nada.
                                    </li>
                                    <li>
                                        <strong>Renovar instancia</strong> — borra la instancia y la crea de nuevo con
                                        el mismo nombre y la misma configuración de webhook. Útil cuando la instancia
                                        está corrupta (por ejemplo, no manda ni recibe aunque esté en estado <code>open</code>).
                                        <strong>Requiere escanear el QR otra vez</strong> al final.
                                    </li>
                                    <li>
                                        <strong>Conectar nuevo número</strong> — desconecta y elimina la instancia
                                        actual definitivamente. Te lleva al formulario inicial para que ingreses un
                                        nombre nuevo y escanees el QR de otro número. Úsalo cuando quieres cambiar el
                                        WhatsApp del bot a uno distinto.
                                    </li>
                                </ul>
                                <p class="text-muted small">
                                    Diferencia rápida: <strong>Reiniciar</strong> no toca la sesión, <strong>Reconectar</strong>
                                    solo regenera el QR, <strong>Renovar</strong> recrea la instancia (mismo número),
                                    <strong>Conectar nuevo número</strong> empieza de cero (otro número).
                                </p>
                            </el-collapse-item>

                            <el-collapse-item title="Buenas prácticas" name="practices">
                                <ul>
                                    <li>El vendedor debe aperturar su caja antes de empezar a emitir.</li>
                                    <li>Al terminar el turno, cerrar la sesión con <code>{{ exitCmd }}</code> para liberar recursos.</li>
                                    <li>Si el bot responde "contacta con el administrador", revisa los logs del servidor.</li>
                                    <li>Mantén la lista de usuarios autorizados sincronizada cuando cambien los teléfonos o se den de baja vendedores.</li>
                                </ul>
                            </el-collapse-item>
                        </el-collapse>
                    </el-tab-pane>
                </el-tabs>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['configuration'],
    data() {
        return {
            activeName: 'first',
            activeCollapses: [],
        };
    },
    computed: {
        triggerCmd() {
            return this.configuration?.bot_trigger_command || '/bot';
        },
        pauseCmd() {
            return this.configuration?.bot_pause_command || '/p';
        },
        exitCmd() {
            return this.configuration?.bot_exit_command || '/fin';
        },
    },
};
</script>

<style>
.whatsapp-bot-manual .manual-title {
    margin-bottom: 0.5rem;
    font-size: 1.05rem;
    font-weight: 600;
}
.whatsapp-bot-manual code {
    background: #f4f4f5;
    color: #c7254e;
    padding: 1px 6px;
    border-radius: 3px;
    font-size: 0.9em;
}
.whatsapp-bot-manual ul,
.whatsapp-bot-manual ol {
    padding-left: 1.4rem;
}
.whatsapp-bot-manual li {
    margin-bottom: 4px;
}
.whatsapp-bot-manual .el-collapse-item__header {
    font-weight: 600;
    font-size: 1rem;
}
</style>
