<?php

// Modules/ClaimsBook/Http/Controllers/ClaimController.php

namespace Modules\ClaimsBook\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Company;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\Catalogs\IdentityDocumentType;
use App\Models\Tenant\Catalogs\Department;
use App\Models\Tenant\User;
use Hyn\Tenancy\Contracts\CurrentHostname;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Environment;
use Modules\ClaimsBook\Models\Tenant\Claim;
use Modules\ClaimsBook\Models\Tenant\ClaimSetting;
use Modules\ClaimsBook\Models\Tenant\StatusClaim;
use Modules\ClaimsBook\Models\Tenant\ClaimChannel;
use Modules\ClaimsBook\Http\Resources\ClaimCollection;
use Modules\ClaimsBook\Jobs\SendClaimAssignedEmail;
use Modules\ClaimsBook\Jobs\SendClaimStatusEmail;
use Modules\ClaimsBook\Services\ClaimPdfService;

class ClaimController extends Controller
{
    protected ClaimPdfService $pdfService;

    public function __construct(ClaimPdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    // ──────────────────────────────────────────────────────────────
    // Vistas
    // ──────────────────────────────────────────────────────────────

    /**
     * Vista principal del libro def reclamaciones (panel de administración).
     */
    public function index()
    {
        return view('claimsbook::index');
    }

    /**
     * Make a filesystem-safe filename from the original uploaded name.
     */
    private function makeSafeFilename($original)
    {
        $original = trim($original);
        $info = pathinfo($original);
        $name = $info['filename'] ?? 'file';
        $ext = isset($info['extension']) ? '.' . $info['extension'] : '';

        // Replace any non safe chars with underscore (keep unicode letters if needed)
        $safeName = preg_replace('/[^A-Za-z0-9\-_\.]/', '_', $name);
        // Limit length
        $safeName = substr($safeName, 0, 120);

        return $safeName . $ext;
    }

    /**
     * Widget público embebible a través de iframe.
     * Renderiza el formulario en una vista standalone sin el layout del tenant.
     * Agrega el header X-Frame-Options: ALLOWALL para permitir embedding.
     */
    public function widget($slug)
    {
        // Leer color en formato OKLCH (nuevo) o hex (backward compat)
        $colorL = is_numeric(request()->query('color_l')) ? (float) request()->query('color_l') : null;
        $colorC = is_numeric(request()->query('color_c')) ? (float) request()->query('color_c') : null;
        $colorH = is_numeric(request()->query('color_h')) ? (float) request()->query('color_h') : null;

        $colorHex = request()->query('color', '#18181b');
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $colorHex)) {
            $colorHex = '#18181b';
        }

        $showCompany = request()->query('show_company', '1') !== '0';

        return response()
            ->view('claimsbook::widget', [
                'tenant_slug'   => $slug,
                'color_l'       => $colorL,
                'color_c'       => $colorC,
                'color_h'       => $colorH,
                'primary_color' => $colorHex,
                'show_company'  => $showCompany,
            ])
            ->header('X-Frame-Options', 'ALLOWALL');
    }

    /**
     * Widget interno: ruta limpia sin slug para uso dentro del propio tenant.
     * Resuelve el slug a partir del hostname activo de tenancy.
     */
    public function widgetInternal()
    {
        $hostname = app(CurrentHostname::class);
        $slug     = $hostname ? $hostname->fqdn : request()->getHost();

        $primaryColor = ClaimSetting::getValue('primary_color', '#18181b');
        $showCompany  = ClaimSetting::getValue('show_company', '1') !== '0';

        // Convertir hex a OKLCH para que el widget lo use con prioridad sobre localStorage
        $colorL = null; $colorC = null; $colorH = null;
        if ($primaryColor && preg_match('/^#[0-9A-Fa-f]{6}$/', $primaryColor)) {
            [$colorL, $colorC, $colorH] = $this->hexToOklch($primaryColor);
        }

        return response()
            ->view('claimsbook::widget', [
                'tenant_slug'   => $slug,
                'color_l'       => $colorL,
                'color_c'       => $colorC,
                'color_h'       => $colorH,
                'primary_color' => $primaryColor,
                'show_company'  => $showCompany,
            ])
            ->header('X-Frame-Options', 'ALLOWALL');
    }

    /**
     * Sirve el script loader JavaScript para integrar el widget en sitios externos.
     * El script crea e inserta el iframe automáticamente usando el hostname del tenant
     * derivado del propio origen de la URL del script.
     */
    public function embedScript()
    {
        $js = <<<'JS'
(function () {
    var script = document.currentScript || (function () {
        var tags = document.getElementsByTagName('script');
        return tags[tags.length - 1];
    }());

    // Derivar el origin y el slug del propio src del script
    var src    = script.src;
    var origin = src.substring(0, src.indexOf('/claims/embed.js'));
    var slug   = (new URL(src)).hostname;

    // Leer atributos de configuración del script (opcionales)
    var colorL      = script.getAttribute('data-color-l') || '';
    var colorC      = script.getAttribute('data-color-c') || '';
    var colorH      = script.getAttribute('data-color-h') || '';
    var colorHex    = script.getAttribute('data-color') || '';  // backward compat
    var showCompany = script.getAttribute('data-show-company');

    // Crear el contenedor
    var wrap = document.createElement('div');
    wrap.style.cssText = 'width:100%;';

    // Construir URL del iframe con los parámetros activos
    var params = [];
    if (colorL && colorC && colorH) {
        params.push('color_l=' + encodeURIComponent(colorL));
        params.push('color_c=' + encodeURIComponent(colorC));
        params.push('color_h=' + encodeURIComponent(colorH));
    } else if (colorHex) {
        params.push('color=' + encodeURIComponent(colorHex));
    }
    if (showCompany === 'false') { params.push('show_company=0'); }
    var iframeSrc = origin + '/claims/widget/' + slug + (params.length ? '?' + params.join('&') : '');

    var iframe = document.createElement('iframe');
    iframe.src         = iframeSrc;
    iframe.width       = '100%';
    iframe.frameBorder = '0';
    iframe.scrolling   = 'no';
    iframe.setAttribute('allow', 'fullscreen');
    iframe.style.cssText = 'border:none;width:100%;display:block;overflow:hidden;height:0;';

    wrap.appendChild(iframe);
    script.parentNode.insertBefore(wrap, script.nextSibling);

    // Ajustar altura dinámicamente via postMessage (funciona cross-origin)
    window.addEventListener('message', function (e) {
        if (
            e.data &&
            e.data.type === 'claims-resize' &&
            typeof e.data.height === 'number' &&
            e.data.height > 0
        ) {
            iframe.style.height = e.data.height + 'px';
        }
    });
}());
JS;

        return response($js, 200)
            ->header('Content-Type', 'application/javascript; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Serve a file stored on the tenant disk for claims.
     * The path is passed base64-encoded to preserve slashes.
     * Public route under /claims/file/{encoded}.
     */
    public function file($folder, $filename)
    {
        // Sanitize filename to avoid directory traversal
        $filename = ltrim($filename, '/');
        $filename = basename($filename);

        $path = "claims/{$folder}/{$filename}";

        if (empty($path) || ! Storage::disk('tenant')->exists($path)) {
            abort(404, 'File not found');
        }

        try {
            $mime = Storage::disk('tenant')->mimeType($path);
        } catch (\Exception $e) {
            $mime = 'application/octet-stream';
        }

        $content = Storage::disk('tenant')->get($path);

        return response($content, 200)->header('Content-Type', $mime);
    }

    /**
     * Endpoint público para que el widget obtenga sus datos de referencia
     * (canales, tipos de documento de identidad y cascada de ubicaciones)
     * sin requerir autenticación del tenant.
     */
    public function publicTables($slug)
    {
        // Resolver contexto de tenant por slug para acceder a sus tablas
        $hostname = Hostname::where('fqdn', 'like', "%{$slug}%")->first();

        if (! $hostname) {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }

        app(Environment::class)->tenant($hostname->website);

        $claim_channels          = $this->buildEnrichedChannels();
        $identity_document_types = IdentityDocumentType::where('active', true)
            ->orderBy('description')
            ->get(['id', 'description']);
        $locations               = $this->buildLocationCascade();
        $company                 = $this->getCompanyData();

        return response()->json(compact(
            'claim_channels',
            'identity_document_types',
            'locations',
            'company'
        ));
    }

    // ──────────────────────────────────────────────────────────────
    // API — Datos de tablas auxiliares
    // ──────────────────────────────────────────────────────────────

    /**
     * Retorna los datos de referencia necesarios para cargar la vista index y el formulario:
     * estados, canales, tipos de documento de identidad y ubicaciones en cascada.
     */
    public function tables()
    {
        $status_claims       = StatusClaim::orderBy('sort_order')->get()->map->getCollectionData();
        $claim_channels      = $this->buildEnrichedChannels();
        $identity_document_types = IdentityDocumentType::where('active', true)
            ->orderBy('description')
            ->get(['id', 'description']);
        $locations = $this->buildLocationCascade();
        $company   = $this->getCompanyData();

        $companyRow              = Company::withOut(['identity_document_type'])->first();
        $widget_custom_url       = $companyRow ? $companyRow->claims_widget_custom_url : null;

        return response()->json(compact(
            'status_claims',
            'claim_channels',
            'identity_document_types',
            'locations',
            'company',
            'widget_custom_url'
        ));
    }

    public function avisoPdf()
    {
        $company = Company::withOut(['identity_document_type'])->first();

        $useCustom  = $company && $company->claims_widget_custom_url_active && $company->claims_widget_custom_url;
        $widgetUrl  = $useCustom
            ? $company->claims_widget_custom_url
            : $this->buildDefaultWidgetUrl();

        $companyName = $company ? $company->name : config('app.name');

        $bookPath    = public_path('porto-ecommerce/assets/images/book-claim.png');
        $bookImageSrc = file_exists($bookPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($bookPath))
            : '';

        $html = view('claimsbook::pdf.complaints-book-notice', compact('widgetUrl', 'companyName', 'bookImageSrc'))->render();

        $mpdf = new \Mpdf\Mpdf([
            'mode'         => 'utf-8',
            'format'       => 'A4',
            'margin_top'   => 20,
            'margin_bottom'=> 20,
            'margin_left'  => 20,
            'margin_right' => 20,
            'tempDir'      => sys_get_temp_dir(),
        ]);

        $mpdf->SetTitle('Aviso — Libro de Reclamaciones');
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('aviso_libro_reclamaciones.pdf', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="aviso_libro_reclamaciones.pdf"');
    }

    private function buildDefaultWidgetUrl(): string
    {
        $hostname = app(CurrentHostname::class);
        $slug     = $hostname ? $hostname->fqdn : request()->getHost();
        $protocol = config('tenant.force_https', false) ? 'https' : request()->getScheme();

        return "{$protocol}://{$slug}/claims/widget/{$slug}";
    }

    public function getWidgetSettings()
    {
        $company = Company::withOut(['identity_document_type'])->first();

        return response()->json([
            'widget_custom_url'        => $company ? $company->claims_widget_custom_url : null,
            'widget_custom_url_active' => $company ? (bool) $company->claims_widget_custom_url_active : false,
            'primary_color'            => ClaimSetting::getValue('primary_color', '#18181b'),
            'show_company'             => ClaimSetting::getValue('show_company', '1') !== '0',
        ]);
    }

    public function updateWidgetSettings(Request $request)
    {
        $request->validate([
            'widget_custom_url'        => 'nullable|string|max:255',
            'widget_custom_url_active' => 'boolean',
            'primary_color'            => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'show_company'             => 'boolean',
        ]);

        $company = Company::withOut(['identity_document_type'])->first();

        if (! $company) {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }

        if ($request->has('widget_custom_url')) {
            $company->claims_widget_custom_url = $request->widget_custom_url ?: null;
        }
        if ($request->has('widget_custom_url_active')) {
            $company->claims_widget_custom_url_active = $request->widget_custom_url_active;
        }
        $company->save();

        if ($request->has('primary_color')) {
            ClaimSetting::setValue('primary_color', $request->primary_color ?: '#18181b');
        }
        if ($request->has('show_company')) {
            ClaimSetting::setValue('show_company', $request->boolean('show_company') ? '1' : '0');
        }

        return response()->json(['message' => 'Configuración guardada correctamente']);
    }

    // ──────────────────────────────────────────────────────────────
    // API — Listado paginado con filtros
    // ──────────────────────────────────────────────────────────────

    /**
     * Métricas clave del libro de reclamaciones.
     */
    public function metrics()
    {
        $total    = Claim::count();
        $closed   = Claim::where('is_closed', true)->count();
        $open     = $total - $closed;
        $reclamos = Claim::where('claim_type', 'reclamo')->count();
        $quejas   = Claim::where('claim_type', 'queja')->count();
        $overdue  = Claim::where('is_closed', false)
                        ->whereNotNull('due_date')
                        ->whereDate('due_date', '<', now())
                        ->count();

        $avgDays = Claim::where('is_closed', true)
                        ->whereNotNull('closed_at')
                        ->selectRaw('AVG(DATEDIFF(closed_at, created_at)) as avg_days')
                        ->value('avg_days');

        return response()->json([
            'total'               => $total,
            'open'                => $open,
            'closed'              => $closed,
            'reclamos'            => $reclamos,
            'quejas'              => $quejas,
            'overdue'             => $overdue,
            'avg_resolution_days' => $avgDays ? round($avgDays, 1) : null,
            'resolution_rate'     => $total > 0 ? round(($closed / $total) * 100, 1) : 0,
        ]);
    }

    /**
     * Retorna los reclamos con paginación y filtros opcionales:
     * code, name/document, date_from, date_to, status_claim_id, claim_type.
     */
    public function records(Request $request)
    {
        $query = Claim::with('statusClaim', 'identityDocumentType')
            ->orderBy('created_at', 'desc');

        // Búsqueda por `code` — puede contener `code` interno o `public_code`.
        // Si hay coincidencia exacta en cualquiera, traer registros relacionados
        if ($request->filled('code')) {
            $input = $request->code;

            // Intentar coincidencia exacta por public_code
            $found = Claim::where('public_code', $input)->first();
            if ($found) {
                $parent = $found->parent_code ?? $this->extractParentCode($found->code);
                $query->where(function ($q) use ($parent) {
                    $q->where('parent_code', $parent)
                      ->orWhere('code', 'like', "{$parent}-%");
                });
            } else {
                // Intentar coincidencia exacta por code
                $found = Claim::where('code', $input)->first();
                if ($found) {
                    // agrupar por correlativo (quitar los últimos 3 caracteres)
                    $base = substr($input, 0, -3);
                    $query->where(function ($q) use ($base) {
                        $q->where('parent_code', $base)
                          ->orWhere('code', 'like', "{$base}-%");
                    });
                } else {
                    // Búsqueda parcial: comparar en ambos campos
                    $query->where(function ($q) use ($input) {
                        $q->where('code', 'like', "%{$input}%")
                          ->orWhere('public_code', 'like', "%{$input}%");
                    });
                }
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('identity_document_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('status_claim_id')) {
            $query->where('status_claim_id', $request->status_claim_id);
        }

        if ($request->filled('claim_type')) {
            $query->where('claim_type', $request->claim_type);
        }

        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        // Ocultar cerrados por defecto; enviar show_closed=1 para incluirlos
        if (!$request->boolean('show_closed')) {
            $query->where('is_closed', false);
        }

        $records = $query->paginate(config('tenant.items_per_page', 20));

        return new ClaimCollection($records);
    }

    // ──────────────────────────────────────────────────────────────
    // API — Registro de nuevos reclamos
    // ──────────────────────────────────────────────────────────────

    /**
     * Almacena un nuevo reclamo enviado desde el panel del tenant (requiere auth).
     */
    public function store(Request $request)
    {
        return $this->processSave($request);
    }

    /**
     * Endpoint público para registrar reclamos desde el widget embebido.
     * Resuelve el tenant por el campo tenant_slug del payload.
     * Rate limiting aplicado en la definición de la ruta (10/min por IP).
     */
    public function publicStore(Request $request)
    {
        $request->validate([
            'tenant_slug' => 'required|string',
        ]);

        // Resolver contexto de tenant por slug
        $hostname = Hostname::where('fqdn', 'like', "%{$request->tenant_slug}%")->first();

        if (! $hostname) {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }

        app(Environment::class)->tenant($hostname->website);

        return $this->processSave($request);
    }

    /**
     * Procesa la validación, generación de código y persistencia de un reclamo.
     * Reutilizado tanto por store() (tenant auth) como publicStore() (widget público).
     */
    protected function processSave(Request $request)
    {
        $request->validate([
            // Paso 1
            'identity_document_type'   => 'required|string|max:10',
            'identity_document_number' => 'required|string|max:20',
            'name'                     => 'required|string|max:200',
            'district_id'              => 'nullable|string|max:6',
            'address'                  => 'nullable|string|max:300',
            'email'                    => 'required|email|max:150',
            'phone'                    => 'nullable|string|max:20',

            // Paso 2
            'asset_type'               => 'required|in:producto,servicio',
            'asset_description'        => 'required|string|max:500',
            'asset_date'               => 'required|date',
            'has_receipt'              => 'boolean',
            'receipt_series'           => 'nullable|string|max:10',
            'receipt_number'           => 'nullable|string|max:20',
            'receipt_amount'           => 'nullable|numeric|min:0',
            'receipt_currency'         => 'nullable|in:PEN,USD',

            // Paso 3
            'claim_type'               => 'required|in:queja,reclamo',
            'detail'                   => 'required|string',
            'expected_result'          => 'required|string',
            'channel'                  => 'nullable|string|max:100',
            'attachment'               => 'nullable|file|max:1024|mimes:jpg,jpeg,png,pdf',
            'terms_accepted'           => 'required|accepted',

            // Reclamo previo (opcional)
            'previous_code'            => 'nullable|string|max:20',
        ]);

        // Resolver el estado inicial para el nuevo reclamo
        $initialStatus = StatusClaim::where('is_initial', true)->first();

        // Verificar si hay un reclamo previo para encadenar.
        // El usuario ingresa su public_code (código de seguimiento), no el code interno.
        $previousClaim = null;
        if ($request->filled('previous_code')) {
            $previousClaim = Claim::where('public_code', $request->previous_code)
                ->orWhere('code', $request->previous_code)
                ->first();
        }

        // Generar código y persistir dentro de una transacción sobre la conexión del tenant.
        // El lockForUpdate() en generateCode requiere una transacción activa para ser efectivo
        // y garantizar que el SELECT y el INSERT sean atómicos (evita correlativos duplicados).
        $connectionName = (new Claim())->getConnectionName();

        $claim = DB::connection($connectionName)->transaction(function () use ($request, $initialStatus, $previousClaim) {
            $code           = $this->generateCode($request->claim_type, $previousClaim);
            $trackingNumber = $previousClaim ? ($previousClaim->tracking_number + 1) : 0;
            $parentCode     = $previousClaim
                ? ($previousClaim->parent_code ?? $this->extractParentCode($previousClaim->code))
                : $this->extractParentCode($code);

            $assignedUserId = null;
            if ($initialStatus && !empty($initialStatus->assigned_user_id)) {
                $assignedUserId = $initialStatus->assigned_user_id;
            } else {
                $firstUser = User::orderBy('id')->first();
                $assignedUserId = $firstUser ? $firstUser->id : null;
            }

            // Store initial attachment (if any) preserving original filename.
            $attachments = [];
            if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
                $orig = $request->file('attachment')->getClientOriginalName();
                $safe = $this->makeSafeFilename($orig);
                $filename = "{$code}_{$safe}";
                $stored = $request->file('attachment')->storeAs('claims/attachments', $filename, 'tenant');
                if ($stored) $attachments[] = $stored;
            }

            return Claim::create([
                'code'                     => $code,
                'tracking_number'          => $trackingNumber,
                'parent_code'              => $parentCode,

                'identity_document_type'   => $request->identity_document_type,
                'identity_document_number' => $request->identity_document_number,
                'name'                     => $request->name,
                'district_id'              => $request->district_id,
                'address'                  => $request->address,
                'email'                    => $request->email,
                'phone'                    => $request->phone,

                'asset_type'               => $request->asset_type,
                'asset_description'        => $request->asset_description,
                'asset_date'               => $request->asset_date,
                'has_receipt'              => $request->boolean('has_receipt'),
                'receipt_series'           => $request->receipt_series,
                'receipt_number'           => $request->receipt_number,
                'receipt_amount'           => $request->receipt_amount,
                'receipt_currency'         => $request->receipt_currency,

                'claim_type'               => $request->claim_type,
                'detail'                   => $request->detail,
                'expected_result'          => $request->expected_result,
                'channel'                  => $request->channel,
                'attachments'              => $attachments,
                'terms_accepted'           => true,

                'status_claim_id'          => $initialStatus ? $initialStatus->id : null,
                'assigned_user_id'         => $assignedUserId,
                'is_closed'                => false,
            ]);
        });

        // Generar PDF de constancia (fuera de la transacción para no bloquearla)
        try {
            $this->pdfService->generate($claim->fresh()->load('statusClaim', 'assignedUser', 'district', 'identityDocumentType'));
        } catch (\Throwable $e) {
            Log::error('ClaimPdfService generate error on store: ' . $e->getMessage());
        }

        // Notificar al cliente si el estado inicial tiene acción de correo configurada
        if ($initialStatus && $initialStatus->action_send_email) {
            try {
                dispatch(new SendClaimStatusEmail(
                    $claim->id,
                    $initialStatus->id,
                    $this->buildClaimListUrl()
                ));
            } catch (\Throwable $e) {
                Log::error("SendClaimStatusEmail dispatch error on store: {$e->getMessage()}");
            }
        }

        // Notificar al usuario asignado sobre el nuevo reclamo
        if ($claim->assigned_user_id) {
            try {
                dispatch(new SendClaimAssignedEmail($claim->id));
            } catch (\Throwable $e) {
                Log::error("SendClaimAssignedEmail dispatch error on store: {$e->getMessage()}");
            }
        }

        $claim->refresh();
        return response()->json([
            'success'  => true,
            'message'  => 'Reclamo registrado correctamente',
            'code'     => $claim->public_code,
            'pdf_url'  => $claim->getDetailData()['pdf_url'] ?? null,
            'email'    => $claim->email,
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // API — Detalle
    // ──────────────────────────────────────────────────────────────

    /**
     * Retorna todos los campos de un reclamo para el modal de detalle.
     */
    public function show($id)
    {
        $claim = Claim::with('statusClaim', 'district', 'identityDocumentType')->findOrFail($id);

        $channelEstablishment = null;
        if (!empty($claim->channel)) {
            $channelEstablishment = Establishment::with(['district', 'province', 'department'])
                ->whereRaw('LOWER(TRIM(description)) = ?', [mb_strtolower(trim($claim->channel))])
                ->first();
        }

        $channelDescription = null;
        if (!empty($claim->channel) && !$channelEstablishment) {
            $claimChannel = ClaimChannel::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($claim->channel))])->first();
            $channelDescription = $claimChannel ? $claimChannel->description : null;
        }

        $data = $claim->getDetailData();
        $data['channel_establishment'] = $channelEstablishment;
        $data['channel_description']   = $channelDescription;

        return response()->json($data);
    }

    // ──────────────────────────────────────────────────────────────
    // API — Cambio de estado
    // ──────────────────────────────────────────────────────────────

    /**
     * Actualiza el estado de un reclamo.
     * Si el estado es is_final, exige campo 'resolution' y cierra el reclamo.
     * Encola el job de email si action_send_email está activo en el estado.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_claim_id' => 'required|integer',
            'resolution'      => 'nullable|string',
        ]);

        $claim  = Claim::findOrFail($id);
        // Validar existencia en el tenant con consulta directa (exists: apunta al sistema, no al tenant)
        $status = StatusClaim::find($request->status_claim_id);
        if (! $status) {
            return response()->json(['message' => 'Estado no válido'], 422);
        }

        // Estado final requiere una resolución obligatoria
        if ($status->is_final) {
            $request->validate([
                'resolution' => 'required|string|min:10',
            ]);
        }

        $claim->status_claim_id = $status->id;
        $claim->is_closed       = $status->is_final;
        if ($status->is_final && !$claim->closed_at) {
            $claim->closed_at = now();
        } elseif (!$status->is_final) {
            $claim->closed_at = null;
        }

        if ($status->is_final && $request->filled('resolution')) {
            $claim->resolution = $request->resolution;
        }

        $claim->save();

        // Regenerar PDF PRIMERO para poder adjuntarlo al correo
        try {
            $this->pdfService->generate($claim->fresh()->load('statusClaim', 'assignedUser', 'district', 'identityDocumentType'));
        } catch (\Throwable $e) {
            Log::error('ClaimPdfService generate error on updateStatus: ' . $e->getMessage());
        }

        // Notificación por correo DESPUÉS del PDF (ya actualizado)
        if ($status->action_send_email) {
            try {
                dispatch(new SendClaimStatusEmail(
                    $claim->id,
                    $status->id,
                    $this->buildClaimListUrl()
                ));
            } catch (\Throwable $e) {
                Log::error("SendClaimStatusEmail dispatch error: {$e->getMessage()}");
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
        ]);
    }

    /**
     * Actualiza de forma unificada el estado, resolución, responsable
     * y archivos adjuntos de respuesta de un reclamo.
     * Acepta multipart/form-data para soportar la carga de archivos.
     */
    public function updateRecord(Request $request, $id)
    {
        $request->validate([
            'status_claim_id'          => 'nullable|integer',
            'resolution'               => 'nullable|string',
            'assigned_user_id'         => 'nullable|integer',
            'response_attachments'     => 'nullable|array|max:5',
            'response_attachments.*'   => 'file|max:32768|mimes:pdf,png,jpg,jpeg,mp4',
        ]);

        $claim = Claim::findOrFail($id);

        // Flag para despachar email después de regenerar el PDF
        $shouldSendEmail = false;
        $emailStatusId   = null;

        // Actualizar estado si viene en el payload
        if ($request->filled('status_claim_id')) {
            $status = StatusClaim::find($request->status_claim_id);
            if (! $status) {
                return response()->json(['message' => 'Estado no válido'], 422);
            }

            // Estado final requiere resolución obligatoria
            if ($status->is_final) {
                $request->validate(['resolution' => 'required|string|min:5']);
            }

            $claim->status_claim_id = $status->id;
            $claim->is_closed       = $status->is_final;
            if ($status->is_final && !$claim->closed_at) {
                $claim->closed_at = now();
            } elseif (!$status->is_final) {
                $claim->closed_at = null;
            }

            // Capturar si hay que enviar email (se despachará después del PDF)
            if ($status->action_send_email) {
                $shouldSendEmail = true;
                $emailStatusId   = $status->id;
            }
        }

        // Actualizar resolución
        if ($request->filled('resolution')) {
            $claim->resolution = $request->resolution;
        }

        // Actualizar responsable (acepta null para quitar asignación)
        if ($request->has('assigned_user_id')) {
            $userId = $request->input('assigned_user_id');
            if ($userId) {
                $user = User::find($userId);
                if (! $user) {
                    return response()->json(['message' => 'Usuario no encontrado'], 422);
                }
            }
            $claim->assigned_user_id = $userId;
        }

        // Agregar nuevos archivos adjuntos de respuesta (sin reemplazar los existentes)
        if ($request->hasFile('response_attachments')) {
            $existing = $claim->response_attachments ?? [];
            foreach ($request->file('response_attachments') as $file) {
                if ($file->isValid()) {
                    $orig = $file->getClientOriginalName();
                    $safe = $this->makeSafeFilename($orig);
                    $filename = "{$claim->code}_{$safe}";
                    $stored = $file->storeAs('claims/responses', $filename, 'tenant');
                    $existing[] = $stored;
                }
            }
            $claim->response_attachments = $existing;
        }

        $claim->save();

        // Regenerar PDF PRIMERO para poder adjuntarlo al correo
        try {
            $this->pdfService->generate($claim->fresh()->load('statusClaim', 'assignedUser', 'district', 'identityDocumentType'));
        } catch (\Throwable $e) {
            Log::error('ClaimPdfService generate error on updateRecord: ' . $e->getMessage());
        }

        // Notificación por correo DESPUÉS del PDF (ya actualizado)
        if ($shouldSendEmail) {
            try {
                dispatch(new SendClaimStatusEmail(
                    $claim->id,
                    $emailStatusId,
                    $this->buildClaimListUrl()
                ));
            } catch (\Throwable $e) {
                Log::error("SendClaimStatusEmail dispatch error: {$e->getMessage()}");
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Reclamo actualizado correctamente',
            'data'    => $claim->fresh()
                ->load('statusClaim', 'district', 'identityDocumentType', 'assignedUser')
                ->getDetailData(),
        ]);
    }

    /**
     * Asigna o actualiza el usuario responsable del reclamo.
     * Espera payload: { assigned_user_id }
     */
    public function updateAssign(Request $request, $id)
    {
        $request->validate([
            'assigned_user_id' => 'nullable|integer',
        ]);

        $claim = Claim::findOrFail($id);

        $userId = $request->input('assigned_user_id');
        if ($userId) {
            $user = User::find($userId);
            if (! $user) {
                return response()->json(['message' => 'Usuario no encontrado'], 422);
            }
        }

        $claim->assigned_user_id = $userId;
        $claim->save();

        return response()->json([
            'success' => true,
            'message' => 'Responsable actualizado correctamente',
            'assigned_user_id' => $claim->assigned_user_id,
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // API Pública — Lookup de código
    // ──────────────────────────────────────────────────────────────

    /**
     * Búsqueda pública de un reclamo por código (sin autenticación).
     * Retorna estado y resolución, o 404 si no existe.
     * Usado por el formulario de widget para precargar datos de un reclamo previo.
     */
    public function lookupCode($code)
    {
        $claim = Claim::with('statusClaim', 'identityDocumentType')
            ->where('public_code', $code)
            ->first();

        if (! $claim) {
            return response()->json(['message' => 'Código no encontrado'], 404);
        }
        // Reutilizar el helper del modelo para obtener URLs públicas de adjuntos
        $detail = $claim->getDetailData();

        return response()->json([
            'found'                    => true,
            'code'                     => $claim->public_code,
            // Paso 1 — Datos del reclamante
            'identity_document_type'   => $detail['identity_document_type'] ?? $claim->identity_document_type,
            'identity_document_number' => $detail['identity_document_number'] ?? $claim->identity_document_number,
            'name'                     => $detail['name'] ?? $claim->name,
            'email'                    => $detail['email'] ?? $claim->email,
            'phone'                    => $detail['phone'] ?? $claim->phone,
            'district_id'              => $detail['district_id'] ?? $claim->district_id,
            'address'                  => $detail['address'] ?? $claim->address,
            // Paso 2 — Bien o servicio (necesario para auto-rellenar cuando el reclamo fue cerrado)
            'asset_type'               => $detail['asset_type'] ?? $claim->asset_type,
            'asset_description'        => $detail['asset_description'] ?? $claim->asset_description,
            'asset_date'               => $detail['asset_date'] ?? null,
            'has_receipt'              => $detail['has_receipt'] ?? $claim->has_receipt,
            'receipt_series'           => $detail['receipt_series'] ?? $claim->receipt_series,
            'receipt_number'           => $detail['receipt_number'] ?? $claim->receipt_number,
            'receipt_amount'           => $detail['receipt_amount'] ?? $claim->receipt_amount,
            'receipt_currency'         => $detail['receipt_currency'] ?? $claim->receipt_currency,
            // Paso 3 — Detalle (para mostrar en paso 0 y auto-rellenar en cerrado)
            'detail'                   => $detail['detail'] ?? $claim->detail,
            'expected_result'          => $detail['expected_result'] ?? $claim->expected_result,
            // Estado, resolución y canal
            'claim_type'               => $detail['claim_type'] ?? $claim->claim_type,
            'channel'                  => $detail['channel'] ?? $claim->channel,
            'status'                   => $detail['status_claim'] ?? ($claim->statusClaim ? $claim->statusClaim->getCollectionData() : null),
            'resolution'               => $detail['resolution'] ?? $claim->resolution,
            'is_closed'                => $detail['is_closed'] ?? $claim->is_closed,
            // Adjuntos: ya convertidos a URLs públicas por getDetailData()
            'attachments'              => $detail['attachments'] ?? [],
            'response_attachments'     => $detail['response_attachments'] ?? [],
            'pdf_url'                  => $detail['pdf_url'] ?? null,
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // Helpers privados
    // ──────────────────────────────────────────────────────────────

    /**
     * Genera el código único para un reclamo siguiendo el formato:
     * {tipo}{MM}{YY}-{correlativo 3d}-{tracking 1d}
     *
     * Si se recibe un $previousClaim, el código se construye a partir de su parent_code:
     * {parent_code}-{tracking_number + 1 con padding 1d}
     */
    private function generateCode(string $claimType, ?Claim $previousClaim): string
    {
        $prefix  = $claimType === 'queja' ? 'Q' : 'R';
        $month   = now()->format('m');
        $year    = now()->format('y');

        if ($previousClaim) {
            // Reclamo relacionado: reutiliza el parent_code y aumenta el tracking
            $parentCode  = $previousClaim->parent_code
                ?? $this->extractParentCode($previousClaim->code);

            $nextTracking = $previousClaim->tracking_number + 1;

            return $parentCode . '-' . str_pad($nextTracking, 1, '0', STR_PAD_LEFT);
        }

        // Nuevo reclamo: calcular el siguiente correlativo para este mes/año/tipo
        $pattern     = "{$prefix}{$month}{$year}-%";
        $lastClaim   = Claim::where('code', 'like', $pattern)
            ->where('tracking_number', 0)
            ->orderBy('code', 'desc')
            ->lockForUpdate()
            ->first();

        $nextCorrelative = 1;
        if ($lastClaim) {
            // Extraer la parte del correlativo: {tipo}{MM}{YY}-{correlativo 4d}-{tracking 2d}
            $parts           = explode('-', $lastClaim->code);
            $nextCorrelative = ((int) ($parts[1] ?? 0)) + 1;
        }

        $correlative = str_pad($nextCorrelative, 3, '0', STR_PAD_LEFT);
        $baseCode    = "{$prefix}{$month}{$year}-{$correlative}";

        return $baseCode . '-0';
    }

    /**
     * Extrae el parent_code a partir del code completo.
     * Ej: "Q0426-008-0" → "Q0426-008"
     */
    private function extractParentCode(string $code): string
    {
        $parts = explode('-', $code);
        // El code tiene exactamente 3 segmentos: tipo+mes+año, correlativo, tracking
        return implode('-', array_slice($parts, 0, 2));
    }

    /**
     * Construye la URL absoluta al listado de reclamos para incluir en el email de notificación.
     */
    private function buildClaimListUrl(): string
    {
        $hostname = app(CurrentHostname::class);
        $fqdn     = $hostname ? $hostname->fqdn : config('app.url');
        $protocol = config('tenant.force_https', false) ? 'https' : request()->getScheme();

        return "{$protocol}://{$fqdn}/claims";
    }

    /**
     * Construye la cascada departamento > provincia > distrito para el cascader de Vue.
     * Mismo formato que EcommerceController::getLocationCascade().
     */
    private function getCompanyData(): array
    {
        $company       = Company::withOut(['identity_document_type'])->first();
        $establishment = Establishment::first();

        if (! $company) {
            return [];
        }

        return [
            'name'       => $company->name,
            'trade_name' => $company->trade_name,
            'ruc'        => $company->number,
            'logo'       => $company->logo ? asset('storage/uploads/logos/' . $company->logo) : null,
            'address'    => $establishment ? $establishment->address : null,
        ];
    }

    private function buildLocationCascade(): array
    {
        $locations   = [];
        $departments = Department::where('active', true)->get();

        foreach ($departments as $department) {
            $provinces = [];
            foreach ($department->provinces as $province) {
                $districts = [];
                foreach ($province->districts as $district) {
                    $districts[] = [
                        'value' => $district->id,
                        'label' => $district->description,
                    ];
                }
                $provinces[] = [
                    'value'    => $province->id,
                    'label'    => $province->description,
                    'children' => $districts,
                ];
            }
            $locations[] = [
                'value'    => $department->id,
                'label'    => $department->description,
                'children' => $provinces,
            ];
        }

        return $locations;
    }

    /**
     * Convierte un color hex (#rrggbb) a OKLCH.
     * Devuelve [l, c, h] redondeados.
     */
    private function hexToOklch(string $hex): array
    {
        $r = hexdec(substr($hex, 1, 2)) / 255;
        $g = hexdec(substr($hex, 3, 2)) / 255;
        $b = hexdec(substr($hex, 5, 2)) / 255;

        $toLinear = fn($c) => $c <= 0.04045 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
        $lr = $toLinear($r); $lg = $toLinear($g); $lb = $toLinear($b);

        $l = 0.4122214708*$lr + 0.5363325363*$lg + 0.0514459929*$lb;
        $m = 0.2119034982*$lr + 0.6806995451*$lg + 0.1073969566*$lb;
        $s = 0.0883024619*$lr + 0.2817188376*$lg + 0.6299787005*$lb;

        $l_ = $l ** (1/3); $m_ = $m ** (1/3); $s_ = $s ** (1/3);

        $L  = 0.2104542553*$l_ + 0.7936177850*$m_ - 0.0040720468*$s_;
        $a  = 1.9779984951*$l_ - 2.4285922050*$m_ + 0.4505937099*$s_;
        $bv = 0.0259040371*$l_ + 0.7827717662*$m_ - 0.8086757660*$s_;

        $C = sqrt($a*$a + $bv*$bv);
        $H = fmod((rad2deg(atan2($bv, $a)) + 360), 360);

        return [round($L, 4), round($C, 4), round($H, 2)];
    }

    private function buildEnrichedChannels(): \Illuminate\Support\Collection
    {
        $estMap = [];
        foreach (Establishment::all() as $est) {
            $parts = array_filter([
                ($est->address && $est->address !== '-') ? $est->address : null,
                optional($est->department)->description,
                optional($est->province)->description,
                optional($est->district)->description,
            ]);
            $estMap[strtolower(trim($est->description))] = [
                'address'   => $parts ? implode(', ', $parts) : null,
                'telephone' => ($est->telephone && $est->telephone !== '-') ? $est->telephone : null,
                'email'     => ($est->email && $est->email !== '-') ? $est->email : null,
            ];
        }

        return ClaimChannel::orderBy('name')->get()->map(function ($ch) use ($estMap) {
            return $ch->getCollectionData($estMap);
        });
    }
}
