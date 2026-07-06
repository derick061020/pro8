<?php

namespace Modules\Hotel\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\Person;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Models\HotelRent;
use Modules\Hotel\Models\HotelRentOrder;
use Modules\Hotel\Models\HotelRentItem;
use Modules\Hotel\Models\HotelBlogPost;
use Modules\Hotel\Models\HotelLandingSetting;
use Modules\Services\Data\DocumentApiResolver;

/**
 * Landing pública de reservas de hotel.
 *
 * Sirve el template (tema Starhotel) y lo conecta con la base de datos del
 * tenant actual: las habitaciones se listan dinámicamente con sus imágenes,
 * amenidades, capacidad y tarifas reales; el cliente busca disponibilidad por
 * fechas, ve el detalle de la habitación y crea una reserva real (HotelRent)
 * que aparece en recepción/calendario.
 *
 * Las rutas NO requieren autenticación. Cada tenant resuelve su propia base de
 * datos por el dominio, así que la misma landing sirve a todos los tenants.
 */
class HotelLandingController extends Controller
{
    /** Hora estándar de entrada/salida si la reserva no especifica otra. */
    const DEFAULT_INPUT_TIME  = '14:00';
    const DEFAULT_OUTPUT_TIME = '12:00';

    /**
     * Index (/) del tenant.
     *
     * La web de reservas es la portada pública del tenant. Los usuarios
     * autenticados (administradores/recepción) se redirigen al panel para no
     * perder el flujo de trabajo; los visitantes anónimos ven la landing.
     */
    public function home(Request $request)
    {
        if (auth()->check()) {
            return redirect('/dashboard');
        }

        return $this->index($request);
    }

    /**
     * Render de la landing con las habitaciones reales del tenant.
     */
    public function index(Request $request)
    {
        $establishment   = $this->resolveEstablishment($request);
        $establishmentId = $establishment->id ?? null;
        $establishments  = $this->branches();
        $configuration   = Configuration::first();
        $rooms           = $this->roomsCollection($establishmentId);
        $featured        = $rooms->where('featured', true)->values();
        $blogPosts       = $this->publishedPosts($establishmentId)->take(3);
        $settings        = $this->landingConfig($establishment);

        return view('hotel::landing.index', compact('establishment', 'establishments', 'establishmentId', 'configuration', 'rooms', 'featured', 'blogPosts', 'settings'));
    }

    /**
     * Sucursal (establecimiento) activa de la web pública.
     *
     * Prioridad: parámetro `sucursal` (o `establishment_id`) de la petición ->
     * sesión -> primer establecimiento. La elección se recuerda en sesión para
     * que la navegación (blog, detalle) no mezcle sucursales. Un enlace con
     * `?sucursal=ID` es compartible y fija esa sucursal.
     */
    private function resolveEstablishment(Request $request = null)
    {
        $request = $request ?: request();
        $enabled = $this->enabledEstablishmentIds();
        $id      = $request->query('sucursal', $request->input('establishment_id'));

        $isEnabled = fn ($value) => $value && $enabled->contains((int) $value);

        if ($isEnabled($id)) {
            session(['hotel_landing_establishment' => (int) $id]);
        } else {
            $id = session('hotel_landing_establishment');
            if (!$isEnabled($id)) {
                session()->forget('hotel_landing_establishment');
                $id = null;
            }
        }

        $establishment = $id ? Establishment::find($id) : null;

        // Sin sucursal válida: primera habilitada; si todas están deshabilitadas
        // (caso límite) se usa la primera existente para no romper la web.
        if (!$establishment) {
            $firstEnabled  = $enabled->first();
            $establishment = $firstEnabled ? Establishment::find($firstEnabled) : Establishment::first();
        }

        if ($establishment) {
            session(['hotel_landing_establishment' => $establishment->id]);
        }

        return $establishment;
    }

    /**
     * IDs de sucursales visibles en la web (todas menos las que se hayan
     * desactivado en su configuración con `web_enabled = false`).
     */
    private function enabledEstablishmentIds()
    {
        $all = Establishment::pluck('id')->map(fn ($v) => (int) $v);

        try {
            $disabled = HotelLandingSetting::all(['establishment_id', 'data'])
                ->filter(function ($s) {
                    $data = is_array($s->data) ? $s->data : [];
                    return array_key_exists('web_enabled', $data) && $data['web_enabled'] === false;
                })
                ->pluck('establishment_id')
                ->filter()
                ->map(fn ($v) => (int) $v)
                ->all();
        } catch (\Throwable $th) {
            $disabled = [];
        }

        return $all->reject(fn ($id) => in_array($id, $disabled, true))->values();
    }

    /**
     * Lista de sucursales visibles en la web para el selector.
     */
    private function branches()
    {
        return Establishment::select('id', 'description', 'address')
            ->whereIn('id', $this->enabledEstablishmentIds()->all() ?: [0])
            ->orderBy('description')
            ->get();
    }

    /**
     * Configuración de personalización de la landing (fusionada con defaults),
     * lista para la vista. Nunca falla: si la tabla no existe todavía devuelve
     * los valores por defecto.
     */
    private function landingConfig($establishment = null)
    {
        try {
            $setting = HotelLandingSetting::forEstablishment($establishment->id ?? null);
            return HotelLandingSetting::mergeDefaults($setting && is_array($setting->data) ? $setting->data : []);
        } catch (\Throwable $th) {
            return HotelLandingSetting::mergeDefaults([]);
        }
    }

    /**
     * Página pública del blog: listado de todas las entradas publicadas.
     */
    public function blog(Request $request)
    {
        $establishment  = $this->resolveEstablishment($request);
        $establishments = $this->branches();
        $posts          = $this->publishedPosts($establishment->id ?? null);
        $settings       = $this->landingConfig($establishment);

        return view('hotel::landing.blog', compact('establishment', 'establishments', 'posts', 'settings'));
    }

    /**
     * Detalle público de una entrada del blog por su slug.
     */
    public function blogPost(Request $request, $slug)
    {
        $establishment  = $this->resolveEstablishment($request);
        $establishments = $this->branches();

        $post = HotelBlogPost::where('slug', $slug)
            ->where('published', true)
            ->first();

        if (!$post) {
            abort(404);
        }

        // Si la nota pertenece a otra sucursal, ajustar la sucursal activa a la
        // suya para que el resto de la web quede coherente.
        if ($post->establishment_id && $post->establishment_id !== ($establishment->id ?? null)) {
            $branch = Establishment::find($post->establishment_id);
            if ($branch) {
                $establishment = $branch;
                session(['hotel_landing_establishment' => $branch->id]);
            }
        }

        $recent = $this->publishedPosts($establishment->id ?? null)
            ->where('id', '!=', $post->id)
            ->take(4);

        $settings = $this->landingConfig($establishment);

        return view('hotel::landing.blog-post', compact('establishment', 'establishments', 'post', 'recent', 'settings'));
    }

    /**
     * Colección de entradas publicadas, ordenadas de la más reciente.
     */
    private function publishedPosts($establishmentId = null)
    {
        return HotelBlogPost::where('published', true)
            ->when($establishmentId, fn ($q) => $q->where('establishment_id', $establishmentId))
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', Carbon::now());
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Buscar habitaciones disponibles para un rango de fechas.
     *
     * Devuelve JSON con las habitaciones libres (sin solapamiento con otras
     * reservas) e incluye el precio total estimado para el rango solicitado.
     */
    public function searchAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'checkin'  => 'required',
            'checkout' => 'required',
            'adults'   => 'nullable|numeric|min:1',
            'children' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Indica las fechas de entrada y salida.',
            ], 422);
        }

        $inDate  = $this->parseDate($request->checkin);
        $outDate = $this->parseDate($request->checkout);

        if (!$inDate || !$outDate) {
            return response()->json([
                'success' => false,
                'message' => 'Las fechas no tienen un formato válido.',
            ], 422);
        }
        if ($outDate->lte($inDate)) {
            return response()->json([
                'success' => false,
                'message' => 'La fecha de salida debe ser posterior a la de entrada.',
            ], 422);
        }

        $adults   = (int) ($request->adults ?? 1);
        $children = (int) ($request->children ?? 0);
        $guests   = max(1, $adults + $children);
        $nights   = max(1, $inDate->copy()->startOfDay()->diffInDays($outDate->copy()->startOfDay()));

        $newStart = Carbon::parse($inDate->format('Y-m-d') . ' ' . self::DEFAULT_INPUT_TIME);
        $newEnd   = Carbon::parse($outDate->format('Y-m-d') . ' ' . self::DEFAULT_OUTPUT_TIME);

        $establishment = $this->resolveEstablishment($request);

        $available = $this->roomsCollection($establishment->id ?? null)
            ->filter(function ($room) use ($newStart, $newEnd, $guests) {
                // Mantenimiento -> no reservable.
                if ($room['status'] === 'MANTENIMIENTO') {
                    return false;
                }
                // Capacidad insuficiente (si está definida).
                if ($room['capacity'] && $guests > $room['capacity']) {
                    return false;
                }
                // Sin solapamiento con otras reservas.
                $conflict = HotelRent::findOverlappingRent($room['id'], $newStart, $newEnd);
                return $conflict === null;
            })
            ->map(function ($room) use ($nights) {
                $room['nights'] = $nights;
                $room['total']  = round(($room['min_price'] ?? 0) * $nights, 2);
                return $room;
            })
            ->values();

        return response()->json([
            'success'   => true,
            'checkin'   => $inDate->format('d/m/Y'),
            'checkout'  => $outDate->format('d/m/Y'),
            'nights'    => $nights,
            'adults'    => $adults,
            'children'  => $children,
            'count'     => $available->count(),
            'rooms'     => $available,
        ], 200);
    }

    /**
     * Detalle de una habitación para el modal de la landing. Si se envían
     * fechas (checkin/checkout) calcula también el total estimado y si está
     * libre en ese rango.
     */
    public function roomDetail(Request $request, $id)
    {
        $establishment = $this->resolveEstablishment($request);
        $room = $this->roomsCollection($establishment->id ?? null)->firstWhere('id', (int) $id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Habitación no encontrada.',
            ], 404);
        }

        $inDate  = $this->parseDate($request->query('checkin'));
        $outDate = $this->parseDate($request->query('checkout'));

        if ($inDate && $outDate && $outDate->gt($inDate)) {
            $nights = max(1, $inDate->copy()->startOfDay()->diffInDays($outDate->copy()->startOfDay()));
            $newStart = Carbon::parse($inDate->format('Y-m-d') . ' ' . self::DEFAULT_INPUT_TIME);
            $newEnd   = Carbon::parse($outDate->format('Y-m-d') . ' ' . self::DEFAULT_OUTPUT_TIME);

            $room['nights']    = $nights;
            $room['total']     = round(($room['min_price'] ?? 0) * $nights, 2);
            $room['available'] = HotelRent::findOverlappingRent($room['id'], $newStart, $newEnd) === null;
        }

        return response()->json([
            'success' => true,
            'room'    => $room,
        ], 200);
    }

    /**
     * Consulta pública de DNI/RUC para autocompletar los datos del huésped.
     * Reutiliza el proveedor configurado por el tenant (apiperu / sunat).
     */
    public function documentLookup($type, $number)
    {
        $type   = strtolower($type) === 'ruc' ? 'ruc' : 'dni';
        $number = preg_replace('/\D/', '', $number);

        if (($type === 'dni' && strlen($number) !== 8) || ($type === 'ruc' && strlen($number) !== 11)) {
            return response()->json([
                'success' => false,
                'message' => $type === 'dni' ? 'El DNI debe tener 8 dígitos.' : 'El RUC debe tener 11 dígitos.',
            ], 422);
        }

        try {
            $result = (new DocumentApiResolver())->service($type, $number);

            if (!empty($result['success']) && !empty($result['data'])) {
                return response()->json([
                    'success' => true,
                    'data'    => $result['data'],
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se encontraron datos para el documento indicado.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo consultar el documento en este momento.',
            ], 200);
        }
    }

    /**
     * Registrar una reserva enviada desde la landing.
     *
     * Crea (o reutiliza) la ficha de cliente cuando se envía documento, y genera
     * un HotelRent real con su orden e ítem de habitación.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:191',
            'email'           => 'nullable|email',
            'telephone'       => 'nullable|string|max:30',
            'document_type'   => 'nullable|in:dni,ruc',
            'document_number' => 'nullable|string|max:15',
            'room'            => 'required|numeric',
            'checkin'         => 'required',
            'checkout'        => 'required',
            'adults'          => 'nullable|numeric|min:1',
            'children'        => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->alert('danger', 'Completa tu nombre, la habitación y las fechas de entrada/salida.');
        }

        $inDate  = $this->parseDate($request->checkin);
        $outDate = $this->parseDate($request->checkout);

        if (!$inDate || !$outDate) {
            return $this->alert('danger', 'Las fechas no tienen un formato válido.');
        }
        if ($outDate->lte($inDate)) {
            return $this->alert('danger', 'La fecha de salida debe ser posterior a la de entrada.');
        }

        DB::connection('tenant')->beginTransaction();
        try {
            $room = HotelRoom::with('category', 'rates.rate')->find($request->room);

            if (!$room || !$room->active) {
                DB::connection('tenant')->rollBack();
                return $this->alert('danger', 'La habitación seleccionada no está disponible.');
            }
            if ($room->status === 'MANTENIMIENTO') {
                DB::connection('tenant')->rollBack();
                return $this->alert('danger', 'La habitación está en mantenimiento y no puede reservarse.');
            }

            $newStart = Carbon::parse($inDate->format('Y-m-d') . ' ' . self::DEFAULT_INPUT_TIME);
            $newEnd   = Carbon::parse($outDate->format('Y-m-d') . ' ' . self::DEFAULT_OUTPUT_TIME);

            $conflict = HotelRent::findOverlappingRent($room->id, $newStart, $newEnd);
            if ($conflict) {
                DB::connection('tenant')->rollBack();
                $cStart = Carbon::parse($conflict->input_date . ' ' . ($conflict->input_time ?: self::DEFAULT_INPUT_TIME))->format('d/m/Y');
                $cEnd   = Carbon::parse($conflict->output_date . ' ' . ($conflict->output_time ?: self::DEFAULT_OUTPUT_TIME))->format('d/m/Y');
                return $this->alert('danger', "La habitación ya está reservada del {$cStart} al {$cEnd}. Elige otras fechas.");
            }

            $adults   = (int) ($request->adults ?? 1);
            $children = (int) ($request->children ?? 0);

            if ($room->capacity && ($adults + $children) > $room->capacity) {
                DB::connection('tenant')->rollBack();
                return $this->alert('danger', "Esta habitación admite hasta {$room->capacity} huésped(es).");
            }

            $roomRate  = $room->rates->first();
            $nights    = max(1, $inDate->copy()->startOfDay()->diffInDays($outDate->copy()->startOfDay()));
            // El precio personalizado de la web tiene prioridad sobre la tarifa,
            // para que el cobro coincida con el precio mostrado en la landing.
            $unitPrice = $room->web_price > 0
                ? (float) $room->web_price
                : (float) ($roomRate->price ?? 0);
            $total     = round($unitPrice * $nights, 2);

            // Cliente: si hay documento, crea/reutiliza la ficha real; si no,
            // queda como huésped externo (customer_id = 0).
            [$customerId, $customerData] = $this->resolveCustomer($request);

            $rent = HotelRent::create([
                'customer_id'      => $customerId,
                'customer'         => $customerData,
                'notes'            => $request->notes ? ('Reserva web: ' . $request->notes) : 'Reserva web (landing)',
                'adults'           => $adults,
                'children'         => $children,
                'towels'           => 1,
                'hotel_room_id'    => $room->id,
                'hotel_rate_id'    => $roomRate->hotel_rate_id ?? null,
                'duration'         => $nights,
                'quantity_persons' => $adults + $children,
                'data_persons'     => null,
                'payment_status'   => 'DEBT',
                'input_date'       => $inDate->format('Y-m-d'),
                'input_time'       => self::DEFAULT_INPUT_TIME,
                'output_date'      => $outDate->format('Y-m-d'),
                'output_time'      => self::DEFAULT_OUTPUT_TIME,
                'is_reserve'       => true,
                'status'           => 'ACTIVE',
                'establishment_id' => $room->establishment_id,
            ]);

            $order = new HotelRentOrder();
            $order->hotel_rent_id    = $rent->id;
            $order->order_number     = 1;
            $order->order_status     = 'DEBT';
            $order->establishment_id = $room->establishment_id;
            $order->save();

            $description = trim(($room->category->description ?? 'Habitación') . ' ' . $room->name);

            $item = new HotelRentItem();
            $item->type                = 'HAB';
            $item->hotel_rent_id       = $rent->id;
            $item->item_id             = $room->item_id;
            $item->item                = [
                'item_id'     => $room->item_id,
                'description' => $description,
                'quantity'    => $nights,
                'unit_price'  => $unitPrice,
                'total'       => $total,
                'item'        => [
                    'description'      => $description,
                    'name_product_pdf' => $description,
                    'unit_price'       => $unitPrice,
                ],
            ];
            $item->quantity            = $nights;
            $item->unit_price          = $unitPrice;
            $item->total               = $total;
            $item->payment_status      = 'DEBT';
            $item->hotel_rent_order_id = $order->id;
            $item->save();

            DB::connection('tenant')->commit();

            $msg = '¡Reserva registrada para ' . e($description) . ' del '
                . $inDate->format('d/m/Y') . ' al ' . $outDate->format('d/m/Y')
                . ($total > 0 ? ' (estimado: S/ ' . number_format($total, 2) . ')' : '')
                . '. El hotel se pondrá en contacto contigo para confirmar.';

            return $this->alert('success', $msg);
        } catch (\Throwable $th) {
            DB::connection('tenant')->rollBack();
            return $this->alert('danger', 'No se pudo registrar la reserva. Inténtalo de nuevo.');
        }
    }

    /**
     * Resuelve el cliente de la reserva.
     *
     * @return array{0:int,1:array} [customer_id, customer_json]
     */
    private function resolveCustomer(Request $request)
    {
        $name      = trim($request->name);
        $email     = $request->email;
        $telephone = $request->telephone;
        $docType   = strtolower((string) $request->document_type);
        $number    = preg_replace('/\D/', '', (string) $request->document_number);

        $customerData = [
            'name'      => $name,
            'email'     => $email,
            'telephone' => $telephone,
            'number'    => $number ?: null,
            'address'   => null,
        ];

        // Sin documento válido -> huésped externo sin ficha de cliente.
        $validDoc = ($docType === 'dni' && strlen($number) === 8)
            || ($docType === 'ruc' && strlen($number) === 11);

        if (!$validDoc) {
            return [0, $customerData];
        }

        $identityTypeId = $docType === 'ruc' ? 6 : 1;

        try {
            $person = Person::where('type', 'customers')
                ->where('number', $number)
                ->first();

            if (!$person) {
                $person = new Person();
                $person->type                      = 'customers';
                $person->identity_document_type_id = $identityTypeId;
                $person->number                    = $number;
                $person->name                      = $name;
                $person->country_id                = 'PE';
                $person->email                     = $email;
                $person->telephone                 = $telephone;
                $person->save();
            }

            $customerData['number']  = $person->number;
            $customerData['name']    = $person->name ?: $name;
            $customerData['address'] = $person->address;

            return [$person->id, $customerData];
        } catch (\Throwable $th) {
            // Si por cualquier motivo no se puede crear la ficha de cliente,
            // la reserva continúa como huésped externo (no se pierde la reserva).
            return [0, $customerData];
        }
    }

    /**
     * Colección de habitaciones activas con su info de cara a la landing.
     */
    private function roomsCollection($establishmentId = null)
    {
        return HotelRoom::with('category', 'floor', 'rates.rate')
            ->where('active', true)
            ->when($establishmentId, fn ($q) => $q->where('establishment_id', $establishmentId))
            ->orderBy('name')
            ->get()
            ->map(function ($room) {
                $rates = $room->rates->map(function ($rr) {
                    return [
                        'hotel_rate_id' => $rr->hotel_rate_id,
                        'description'   => $rr->rate->description ?? 'Tarifa',
                        'price'         => (float) $rr->price,
                    ];
                })->values();

                $images = $room->image_urls;

                // Precio "desde" para la landing: el precio personalizado de la
                // web tiene prioridad; si no está definido, se usa el mínimo de
                // las tarifas asignadas.
                $webPrice = $room->web_price ? (float) $room->web_price : null;
                $minPrice = $webPrice ?? ($rates->min('price') ?? 0);

                return [
                    'id'                => $room->id,
                    'name'              => $room->name,
                    'category'          => $room->category->description ?? 'Habitación',
                    'floor'             => $room->floor->description ?? null,
                    'description'       => $room->description,
                    'short_description' => $room->short_description,
                    'status'            => $room->status,
                    'capacity'          => $room->capacity ? (int) $room->capacity : null,
                    'beds'              => $room->beds,
                    'size'              => $room->size ? (int) $room->size : null,
                    'featured'          => (bool) $room->featured,
                    'amenities'         => is_array($room->amenities) ? array_values($room->amenities) : [],
                    'images'            => $images,
                    'main_image'        => count($images) ? $images[0] : null,
                    'rates'             => $rates,
                    'web_price'         => $webPrice,
                    'min_price'         => $minPrice,
                ];
            });
    }

    /**
     * Parsear la fecha del formulario (dd/mm/yyyy o yyyy-mm-dd) de forma tolerante.
     */
    private function parseDate($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d'] as $fmt) {
            try {
                $d = Carbon::createFromFormat($fmt, $value);
                if ($d) {
                    return $d->startOfDay();
                }
            } catch (\Throwable $e) {
                // probar siguiente formato
            }
        }
        return null;
    }

    /**
     * Fragmento HTML de alerta, con el mismo estilo del template.
     */
    private function alert($type, $message)
    {
        $html = '<div class="alert alert-' . $type . ' alert-dismissable">'
            . '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
            . e($message) . '</div>';

        return response($html, 200)->header('Content-Type', 'text/html');
    }
}
