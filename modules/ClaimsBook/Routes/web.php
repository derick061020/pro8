<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Módulo ClaimsBook (Libro de Reclamaciones)
|--------------------------------------------------------------------------
|
| Rutas públicas: widget embebible, lookup de código, store público y canales.
| Rutas privadas: panel admin con auth + locked.tenant.
|
*/

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if ($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {

        // ──────────────────────────────────────────────────────────────────────────
        // Rutas PÚBLICAS — sin autenticación, sin middleware de tenant de sesión
        // ──────────────────────────────────────────────────────────────────────────

        // Widget embebible en iframe para registro público de reclamos (acceso externo, requiere slug)
        Route::get('/claims/widget/{slug}', 'ClaimController@widget');

        // Widget sin slug: acceso interno desde el propio tenant (slug resuelto automáticamente)
        Route::get('/claims/widget', 'ClaimController@widgetInternal');

        // Script loader para integrar el widget en sitios externos con una sola línea de código
        Route::get('/claims/embed.js', 'ClaimController@embedScript');

        // Registro de reclamo desde el widget (rate limiting: 60 peticiones/minuto por IP)
        Route::middleware('throttle:60,1')
            ->withoutMiddleware(['auth', 'VerifyCsrfToken'])
            ->post('/claims/remote/store', 'ClaimController@publicStore');

        // Canales públicos del tenant (para el selector del widget)
        Route::get('/claims/remote/channels/{slug}', 'ClaimChannelController@publicRecords');

        // Tablas de referencia pública para el widget (canales, tipos de documento, ubicaciones)
        Route::get('/claims/remote/tables/{slug}', 'ClaimController@publicTables');

        // Lookup público de código de reclamo (para precargar datos de reclamo previo)
        Route::get('/claims/lookup/{code}', 'ClaimController@lookupCode');

        // Servir archivos del disco tenant relacionados con reclamos
        // Ruta pública relacionada a `claims` y manejada por `ClaimController::file`
        // Ej: /claims/file/responses/imagen.png
        Route::get('/claims/file/{folder}/{filename}', 'ClaimController@file')
            ->name('tenant.claims.file')
            ->where('filename', '.*');

        // ──────────────────────────────────────────────────────────────────────────
        // Rutas PRIVADAS — requieren auth y tenant activo
        // ──────────────────────────────────────────────────────────────────────────

        Route::middleware(['auth', 'locked.tenant', 'check.email.verified'])
            ->prefix('claims')
            ->group(function () {
                // Vista principal del libro de reclamaciones
                Route::get('/', 'ClaimController@index')->name('tenant.claims.index');

                // Datos de tablas auxiliares (estados, canales, tipos doc, ubicaciones)
                Route::get('/tables', 'ClaimController@tables');

                // Configuración del widget embed
                Route::get('/widget-settings', 'ClaimController@getWidgetSettings');
                Route::put('/widget-settings', 'ClaimController@updateWidgetSettings');

                // PDF aviso del libro de reclamaciones con QR
                Route::get('/complaints-book-notice', 'ClaimController@avisoPdf');

                // Métricas del panel de control
                Route::get('/metrics', 'ClaimController@metrics');

                // Listado paginado con filtros
                Route::get('/records', 'ClaimController@records');

                // Registro de nuevo reclamo desde el panel interno
                Route::post('/store', 'ClaimController@store');

                // Detalle completo de un reclamo (para el modal de detalle)
                Route::get('/{id}', 'ClaimController@show');

                // Cambio de estado de un reclamo
                Route::put('/{id}/status', 'ClaimController@updateStatus');
                // Actualización unificada: estado, resolución, responsable y adjuntos de respuesta
                Route::post('/{id}/update', 'ClaimController@updateRecord');
                // Asignar responsable a un reclamo
                Route::put('/{id}/assign', 'ClaimController@updateAssign');
            });

        Route::middleware(['auth', 'locked.tenant', 'check.email.verified'])
            ->prefix('statusClaim')
            ->group(function () {
                Route::get('/records',          'StatusClaimController@records');
                Route::post('/store',           'StatusClaimController@store');
                Route::put('/update/{id}',      'StatusClaimController@update');
                Route::delete('/destroy/{id}',  'StatusClaimController@destroy');
                Route::post('/reorder',         'StatusClaimController@reorder');
            });

        Route::middleware(['auth', 'locked.tenant', 'check.email.verified'])
            ->prefix('claimChannels')
            ->group(function () {
                Route::get('/records',         'ClaimChannelController@records');
                Route::post('/store',          'ClaimChannelController@store');
                Route::put('/update/{id}',     'ClaimChannelController@update');
                Route::delete('/destroy/{id}', 'ClaimChannelController@destroy');
                Route::post('/sync',           'ClaimChannelController@sync');
            });
    });
}