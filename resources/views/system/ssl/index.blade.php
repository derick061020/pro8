@extends('system.layouts.app')

@php
    $fmt = fn ($d) => $d ? $d->copy()->setTimezone(config('app.timezone'))->locale('es')->isoFormat('ddd D MMM YYYY, HH:mm') : '—';
    $rel = fn ($d) => $d ? $d->copy()->locale('es')->diffForHumans() : '';
    $tone = $daysLeft === null ? 'secondary' : ($daysLeft < 7 ? 'danger' : ($daysLeft < 30 ? 'warning' : 'success'));
    $okCount = collect($live['domains'])->where('ok', true)->count();
@endphp

@section('content')
<style>
    .ssl-kpi { font-size: 1.6rem; font-weight: 700; line-height: 1.2; }
    .ssl-muted { color: #6c757d; font-size: .85rem; }
    .ssl-log { background: #0f172a; color: #e2e8f0; font-size: .78rem; max-height: 420px; overflow: auto; padding: 12px; border-radius: 6px; white-space: pre-wrap; word-break: break-word; }
    .ssl-steps li { margin-bottom: .35rem; }
</style>

<div class="page-header">
    <h2>SSL</h2>
</div>

<div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
    <div class="ssl-muted">Certificado HTTPS de {{ config('configuration.app_url_base') ?: 'hotelbuendia.net' }} y su renovación automática.</div>
    <a href="{{ route('system.ssl', ['refresh' => 1]) }}" class="btn btn-default btn-sm"><i class="fas fa-sync-alt mr-1"></i> Revisar ahora</a>
</div>

@if (!$installed)
    <div class="alert alert-warning">
        <strong>La renovación automática no está instalada.</strong> El panel muestra el certificado que hoy sirve el servidor,
        pero todavía no hay ningún registro del script. Instálalo una vez desde tu computadora:
        <pre class="mt-2 mb-0 p-2 bg-white border rounded" style="white-space: pre-wrap">scp deploy/renovar-ssl.sh pro8:/root/renovar-ssl.sh
ssh pro8 'chmod +x /root/renovar-ssl.sh && (crontab -l 2>/dev/null | grep -v renovar-ssl; echo "15 4 * * * /root/renovar-ssl.sh") | crontab - && /root/renovar-ssl.sh'</pre>
    </div>
@elseif ($stale)
    <div class="alert alert-danger">
        <strong>El cron no se ejecutó.</strong> La última ejecución fue {{ $rel($lastRun) }} ({{ $fmt($lastRun) }}) y debería correr todos los días a las 04:15.
        Revisa en el servidor que siga en <code>crontab -l</code>.
    </div>
@elseif (!($status['ok'] ?? true))
    <div class="alert alert-danger">
        <strong>La última renovación falló</strong> (código {{ $status['codigo'] }}). Revisa el log de abajo; mientras tanto el certificado anterior sigue activo.
    </div>
@endif

@if ($daysLeft !== null && $daysLeft < 15)
    <div class="alert alert-danger">
        <strong>El certificado vence en {{ $daysLeft }} días.</strong> Ya debería haberse renovado: revisa el log o ejecuta <code>/root/renovar-ssl.sh</code> en el servidor.
    </div>
@endif

<div class="row">
    <div class="col-md-6 col-xl-3 mb-3">
        <div class="card h-100"><div class="card-body">
            <div class="ssl-muted">Vence</div>
            <div class="ssl-kpi text-{{ $tone }}">{{ $daysLeft !== null ? $daysLeft.' días' : '—' }}</div>
            <div class="ssl-muted">{{ $fmt($expiresAt) }}</div>
        </div></div>
    </div>
    <div class="col-md-6 col-xl-3 mb-3">
        <div class="card h-100"><div class="card-body">
            <div class="ssl-muted">Última ejecución del cron</div>
            <div class="ssl-kpi">
                @if ($lastRun)
                    {!! ($status['ok'] ?? false) ? '<span class="text-success">Correcta</span>' : '<span class="text-danger">Con error</span>' !!}
                @else
                    —
                @endif
            </div>
            <div class="ssl-muted">
                {{ $lastRun ? $fmt($lastRun).' · '.$rel($lastRun) : 'Sin registros todavía' }}
                @if ($lastRun)<br>{{ ($status['renovado'] ?? false) ? 'Renovó el certificado' : 'No hizo falta renovar' }}@endif
            </div>
        </div></div>
    </div>
    <div class="col-md-6 col-xl-3 mb-3">
        <div class="card h-100"><div class="card-body">
            <div class="ssl-muted">Próxima ejecución</div>
            <div class="ssl-kpi">{{ $installed ? $nextRun->copy()->locale('es')->isoFormat('D MMM, HH:mm') : '—' }}</div>
            <div class="ssl-muted">{{ $installed ? 'Todos los días a las 04:15 · '.$rel($nextRun) : 'Cron no instalado' }}</div>
        </div></div>
    </div>
    <div class="col-md-6 col-xl-3 mb-3">
        <div class="card h-100"><div class="card-body">
            <div class="ssl-muted">Próxima renovación</div>
            <div class="ssl-kpi">{{ $renewAt ? $renewAt->copy()->locale('es')->isoFormat('D MMM YYYY') : '—' }}</div>
            <div class="ssl-muted">Se renueva cuando faltan menos de 30 días{{ $renewAt ? ' · '.$rel($renewAt) : '' }}</div>
        </div></div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
        <strong>Dominios ({{ $okCount }}/{{ count($live['domains']) }} correctos)</strong>
        <span class="ssl-muted">Revisado {{ \Carbon\Carbon::parse($live['checked_at'])->locale('es')->diffForHumans() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th class="pl-3">Dominio</th><th>Estado</th><th>Vence</th><th>Emisor</th></tr></thead>
                <tbody>
                @foreach ($live['domains'] as $d)
                    <tr>
                        <td class="pl-3"><a href="https://{{ $d['host'] }}" target="_blank" rel="noopener">{{ $d['host'] }}</a></td>
                        <td>
                            @if ($d['ok'])
                                <span class="badge badge-success">Seguro</span>
                            @else
                                <span class="badge badge-danger">{{ $d['error'] }}</span>
                                @if (!$d['covered'] && $d['expires_at'])
                                    <div class="ssl-muted">Se incluirá en la próxima ejecución del cron si apunta a este servidor.</div>
                                @endif
                            @endif
                        </td>
                        <td class="text-nowrap">{{ $d['expires_at'] ? $d['expires_at']->copy()->locale('es')->isoFormat('D MMM YYYY') : '—' }}</td>
                        <td class="ssl-muted">{{ $d['issuer'] ?: '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if (!empty($status['omitidos']))
            <div class="px-3 py-2 border-top ssl-muted">
                Omitidos en la última renovación porque no apuntan a {{ $status['ip_servidor'] ?? 'este servidor' }}: {{ implode(', ', $status['omitidos']) }}
            </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-lg-5 mb-3">
        <div class="card h-100">
            <div class="card-header"><strong>Cómo funciona</strong></div>
            <div class="card-body">
                <ol class="pl-3 mb-3 ssl-steps">
                    <li>Todos los días a las <b>04:15</b> el servidor ejecuta <code>/root/renovar-ssl.sh</code>.</li>
                    <li>Junta los dominios del sistema (incluidos los de clientes nuevos) que apuntan al servidor.</li>
                    <li>Si al certificado le quedan <b>menos de 30 días</b> o hay un dominio nuevo, pide uno nuevo a Let's Encrypt. Si no, no hace nada.</li>
                    <li>Al renovar, el proxy se detiene unos segundos y vuelve a levantarse solo.</li>
                </ol>
                <div class="ssl-muted">
                    Para renovar a mano: <code>ssh pro8 /root/renovar-ssl.sh</code><br>
                    Log completo en el servidor: <code>/var/log/renovar-ssl.log</code>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7 mb-3">
        <div class="card h-100">
            <div class="card-header"><strong>Log de las últimas ejecuciones</strong></div>
            <div class="card-body">
                @if ($log !== '')
                    <div class="ssl-log" id="ssl-log">{{ $log }}</div>
                    <script>document.addEventListener('DOMContentLoaded', function () { var el = document.getElementById('ssl-log'); if (el) el.scrollTop = el.scrollHeight; });</script>
                @else
                    <p class="ssl-muted mb-0">Todavía no hay ejecuciones registradas.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
