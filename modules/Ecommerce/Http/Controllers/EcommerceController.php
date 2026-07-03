<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Http\Controllers\Tenant\EmailController;
use App\Models\Tenant\Configuration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Item;
use App\Http\Resources\Tenant\ItemCollection;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant\User;
use App\Models\Tenant\Person;
use Illuminate\Support\Str;
use App\Models\Tenant\Order;
use App\Models\Tenant\ItemsRating;
use App\Models\Tenant\ConfigurationEcommerce;
use Modules\Ecommerce\Http\Resources\ItemBarCollection;
use stdClass;
use Illuminate\Support\Facades\Mail;
use App\Mail\Tenant\CulqiEmail;
use App\Http\Controllers\Tenant\Api\ServiceController;
use Illuminate\Support\Facades\Validator;
use Modules\Inventory\Models\InventoryConfiguration;
use App\Http\Resources\Tenant\OrderCollection;
use App\Models\Tenant\Promotion;
use Modules\ApiPeruDev\Data\ServiceData;
use App\Models\Tenant\Document;
use Modules\Item\Models\Category;
use App\Models\Tenant\Catalogs\Department;
use Modules\Ecommerce\Models\Tenant\DeliveryZone;
use Modules\Ecommerce\Models\Tenant\DeliveryZoneLocation;
use Modules\Ecommerce\Models\Tenant\DiscountCoupon;
use Modules\Ecommerce\Models\Tenant\DiscountCouponUsage;
use Modules\Ecommerce\Models\Tenant\PickupBranch;
use App\Models\Tenant\PersonAddress;

use App\Models\System\Configuration as SystemConfiguration;


class EcommerceController extends Controller
{
    /**
     * Descripción general reutilizable para meta tags y otros lugares
     */
    public static function getEcommerceDescription($company = null)
    {
        // Buscar el nombre comercial en varios campos posibles
        $trade_name = data_get($company, 'trade_name')
            ?: 'Tu tienda online';
        return "Compra online en {$trade_name}. Encuentra una amplia variedad de productos de calidad, ofertas exclusivas y precios competitivos, con envíos rápidos y una experiencia de compra segura y confiable.";
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function __construct(){
        // Compartir variable records
        view()->share('records', Item::where('apply_store', 1)->orderBy('id', 'DESC')->take(2)->get());

        // Compartir descripción ecommerce globalmente usando el modelo Company
        $companyModel = \App\Models\Tenant\Company::first();
        $ecommerceDescription = self::getEcommerceDescription($companyModel);
        view()->share('ecommerceDescription', $ecommerceDescription);
    }

    // public function index()
    // {
    //   $dataPaginate = Item::where([['apply_store', 1], ['internal_id','!=', null]])->paginate(15);
    //   $configuration = InventoryConfiguration::first();
    //   return view('ecommerce::index', ['dataPaginate' => $dataPaginate, 'configuration' => $configuration->stock_control]);
    // }
    public function index($name = null)
    {
        if ($name) {
            $name = str_replace('-', ' ', $name);
        }

        $category = Category::where('name', $name)->first();

        // Obtener preferencias de configuración
        $configEcommerce = ConfigurationEcommerce::first();
        $preferences = $configEcommerce && $configEcommerce->preferences
            ? (is_string($configEcommerce->preferences) ? json_decode($configEcommerce->preferences, true) : $configEcommerce->preferences)
            : ['show_description' => 1, 'show_stock' => 0, 'only_available_products' => 0];

        // Obtener el modelo Company para meta tags y descripción
        $company = \App\Models\Tenant\Company::first();

        // Query base
        $query = Item::where([['apply_store', 1], ['internal_id', '!=', null]]);

        // Filtrar solo productos disponibles si está activado
        if (isset($preferences['only_available_products']) && $preferences['only_available_products'] == 1) {
            $query->where('stock', '>', 0);
        }

        $dataPaginate = $query->orderBy('created_at', 'DESC')
            ->category($category ? $category->id : null)
            ->paginate(8);

        $configuration = InventoryConfiguration::first();
        $categories = Category::get();

        // Obtener los anuncios publicitarios (spots) activos
        $spots = Promotion::where('apply_restaurant', 0)
            ->where('type', 'spots')
            ->where('status', 1)
            ->orderBy('id', 'ASC')
            ->limit(4)
            ->get();

        // Obtener la descripción general para meta tags
        $ecommerceDescription = self::getEcommerceDescription($company);

        $customLinks = \App\Models\Tenant\ConfigurationEcommerce::getCustomLinks();

        return view('ecommerce::index', [
            'dataPaginate' => $dataPaginate,
            'configuration' => $configuration->stock_control,
            'spots' => $spots,
            'preferences' => $preferences,
            'ecommerceDescription' => $ecommerceDescription,
            'company' => $company,
            'customLinks' => $customLinks
        ])->with('categories', $categories);
    }

    // public function category(Request $request)
    // {
    //   $dataPaginate = Item::select('i.*')
    //     ->where([['i.apply_store', 1], ['i.internal_id','!=', null], ['it.tag_id', $request->category]])
    //     ->from('items as i')
    //     ->join('item_tags as it', 'it.item_id','i.id')->paginate(15);
    //     $configuration = InventoryConfiguration::first();
    //   return view('ecommerce::index', ['dataPaginate' => $dataPaginate, 'configuration' => $configuration->stock_control]);
    // }

    public function getDescriptionWithPromotion($item, $promotion_id)
    {
        $promotion = Promotion::findOrFail($promotion_id);

        return "{$item->description} - {$promotion->name}";
    }

    public function item($slug, $promotion_id = null)
    {
        $id = (int) $slug;
        $row = Item::find($id);
        $exchange_rate_sale = $this->getExchangeRateSale();
        $sale_unit_price = ($row->has_igv) ? $row->sale_unit_price : $row->sale_unit_price*1.18;

        $description = $promotion_id ? $this->getDescriptionWithPromotion($row, $promotion_id) : $row->description;

        $record = (object)[
            'id' => $row->id,
            'internal_id' => $row->internal_id,
            'unit_type_id' => $row->unit_type_id,
            'description' => $description,
            'category' => $row->category,
            'stock' => $row->getStockByWarehouseMain(),
            // 'description' => $row->description,
            'technical_specifications' => $row->technical_specifications,
            'name' => $row->name,
            'second_name' => $row->second_name,
            'sale_unit_price' => ($row->currency_type_id === 'PEN') ? $sale_unit_price : ($sale_unit_price * $exchange_rate_sale),
            'currency_type' => $row->currency_type,
            'has_igv' => (bool) $row->has_igv,
            'sale_unit' => $row->sale_unit_price,
            'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
            'currency_type_symbol' => $row->currency_type->symbol,
            'image' =>  $row->image,
            'image_medium' => $row->image_medium,
            'image_small' => $row->image_small,
            'tags' => $row->tags->pluck('tag_id')->toArray(),
            'images' => $row->images,
            'attributes' => $row->attributes ? $row->attributes : [],
            'promotion_id' => $promotion_id,
        ];
        $categories = \Modules\Item\Models\Category::get();
        return view('ecommerce::items.record', compact('record', 'categories'));
    }

    public function items()
    {
        $records = Item::where('apply_store', 1)->get();
        return view('ecommerce::items.index', compact('records'));
    }

    public function itemsBar()
    {
        $records = Item::where('apply_store', 1)->get();
        // return new ItemCollection($records);
        return new ItemBarCollection($records);

    }

    public function partialItem($id)
    {
        $record = Item::find($id);
        return view('ecommerce::items.partial', compact('record'));
    }

    public function detailCart()
    {
        $configuration = ConfigurationEcommerce::first();
        $categories = \Modules\Item\Models\Category::get();

        // Obtener el tipo de descuento global configurado (02 afecta la base, 03 no afecta)
        $systemConfig = Configuration::with('globalDiscountType')->first();
        $global_discount_type = $systemConfig->globalDiscountType;

        // Obtener la primera dirección guardada del cliente autenticado para pre-cargar el modal
        $userAddress = null;
        if ($ecommerceUser = auth('ecommerce')->user()) {
            $firstAddress = $ecommerceUser->addresses()->first();
            if ($firstAddress) {
                $userAddress = [
                    'address'       => $firstAddress->address,
                    'department_id' => $firstAddress->department_id,
                    'province_id'   => $firstAddress->province_id,
                    'district_id'   => $firstAddress->district_id,
                    'phone'         => $firstAddress->phone,
                ];
            }
        }

        $enable_electronic_documents = (bool) ($configuration->enable_electronic_documents ?? false);
        $enable_store_pickup          = (bool) ($configuration->enable_store_pickup ?? false);
        $enable_yape                  = (bool) ($configuration->enable_yape ?? false);
        $enable_transfer              = (bool) ($configuration->enable_transfer ?? false);

        // Sucursales de recojo activas para el checkout
        $pickup_branches = $enable_store_pickup
            ? PickupBranch::active()->orderBy('name')->get(['id', 'name', 'address'])->toArray()
            : [];

        return view('ecommerce::cart.detail', compact('configuration', 'categories', 'global_discount_type', 'userAddress', 'enable_electronic_documents', 'enable_store_pickup', 'pickup_branches', 'enable_yape', 'enable_transfer'));
    }

    public function orderList()
    {
        if (auth('ecommerce')->user()) {
            $configuration = ConfigurationEcommerce::first();
            $categories = \Modules\Item\Models\Category::get();
            return view('ecommerce::document_list.order', compact('configuration', 'categories'));
        } else {
            return redirect('ecommerce');
        }
    }

    public function documentList()
    {
        if (auth('ecommerce')->user()) {
            $categories = \Modules\Item\Models\Category::get();
            return view('ecommerce::document_list.document', compact('categories'));
        } else {
            return redirect('ecommerce');
        }
    }

    public function orders(Request $request)
    {
        if (auth('ecommerce')->user()) {
            $user = auth('ecommerce')->user();

            // Inicializar la consulta base
            $records = Order::where(function($query) use ($user) {
                $query->where('customer', 'LIKE', '%' . $user->email . '%')
                      ->orWhereJsonContains('customer->correo_electronico', $user->email);
            });

            // Aplicar filtro de estado si se proporciona
            if ($request->state_order_id) {
                $state_allowed = $request->state_order_id && $request->state_order_id != 'all' ? [$request->state_order_id] : [1, 2, 3, 4];
                $records = $records->whereIn('status_order_id', $state_allowed);
            }

            // Aplicar filtro de fecha si se proporciona
            if ($request->date_of_endd || $request->date_of_start) {
                $date_of_start = $request->date_of_start ?? date('Y-m-d');
                $date_of_end = $request->date_of_endd ?? date('Y-m-d');
                $records = $records->whereBetween('created_at', [$date_of_start, $date_of_end]);
            }

            // Obtener los resultados paginados
            $records = $records->paginate(config('tenant.items_per_page', 10));

            // Transformar los datos manteniendo la estructura de paginación
            $records->getCollection()->transform(function ($row) {
                return $row->getCollectionData();
            });

            return $records;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado'
            ], 401);
        }
    }

    public function documents(Request $request)
    {
        if (auth('ecommerce')->user()) {
            $user = auth('ecommerce')->user();


            // Buscar órdenes del usuario
            $orders = Order::where(function($query) use ($user) {
                        $query->where('customer', 'LIKE', '%' . $user->email . '%')
                              ->orWhereJsonContains('customer->correo_electronico', $user->email);
                    })->get();

            $arrays_external_id = $orders->pluck('document_external_id')->filter()->toArray();

            if (empty($arrays_external_id)) {
                return response()->json([
                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => config('tenant.items_per_page', 10),
                    'total' => 0
                ]);
            }

            $documents = Document::where(function($q) use($arrays_external_id, $user, $request) {
                            $q->whereIn('external_id', $arrays_external_id)
                                ->orWhere('customer->email', $user->email);
                        });

            if ($request->date_of_endd || $request->date_of_start) {
                $date_of_start = $request->date_of_start ?? date('Y-m-d');
                $date_of_end = $request->date_of_endd ?? date('Y-m-d');
                $documents = $documents->whereBetween('date_of_issue', [$date_of_start, $date_of_end]);
            }

            if ($request->state_type_id ) {
                $state_allowed = $request->state_type_id && $request->state_type_id != 'all'  ? [$request->state_type_id] : ['05', '09', '11'];
                $documents = $documents->whereIn('state_type_id', $state_allowed);
            }

            $documents = $documents->orderBy('date_of_issue', 'desc')
                        ->paginate(config('tenant.items_per_page'));

            // Transformar los datos manteniendo la estructura de paginación
            $documents->getCollection()->transform(function ($dc) {
                return [
                    'number' => $dc->getNumberFullAttribute(),
                    'description' => $dc->document_type->description,
                    'date_of_issue' => $dc->date_of_issue->format('Y-m-d'),
                    'customer' => [
                        'name' => $dc->customer->name,
                        'number' => $dc->customer->number,
                        'identity_document_type_id' => $dc->customer->identity_document_type_id,
                    ],
                    'status' => $dc->state_type->description,
                    'state_type_id' => $dc->state_type_id,
                    'download_pdf' => $dc->download_external_pdf,
                    'download_xml' =>  $dc->download_external_xml,
                    'total' => $dc->total,
                ];
            });

            return $documents;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No autentificado'
            ], 401);
        }
    }

    public function pay()
    {
        return view('ecommerce::cart.pay');
    }

    public function showLogin()
    {
        return view('ecommerce::user.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('ecommerce')->attempt($credentials)) {
           return[
               'success' => true,
               'message' => 'Login Success'
           ];
        }
        else{
            return[
                'success' => false,
                'message' => 'Usuario o Password incorrectos'
            ];
        }

    }

    public function logout()
    {
        Auth::guard('ecommerce')->logout();

        $referer = request()->headers->get('referer');

        if ($referer && (str_contains($referer, '/pedidos') || str_contains($referer, 'pedidos/'))) {
            return redirect('/pedidos');
        }

        // Detectar si viene de restaurant y redirigir apropiadamente
        if ($referer && str_contains($referer, '/restaurant')) {
            return redirect('restaurant/list/items');
        }

        return redirect('ecommerce');
    }

    public function storeUser(Request $request)
    {
        try{

            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'ruc' => 'required|string|min:8|max:11',
                'name' => 'nullable|string|max:255',
                'pswd' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return [
                    'success' => false,
                    'message' => $validator->errors()->first()
                ];
            }

            $verify = Person::where('email', $request->email)
                        ->orWhere('number', $request->ruc)
                        ->first();
            if($verify)
            {
                return [
                    'success' => false,
                    'message' => 'Email o RUC/DNI no disponible'
                ];
            }

            $type = (strlen($request->ruc)==8) ? 'dni' : 'ruc';
            $name = $request->name;
            $identity_document_type_id = (strlen($request->ruc)==8) ? 1 : 6;
            $address = null;
            $department_id = null;
            $province_id = null;
            $district_id = null;

            $dataDocument = $this->searchDocument($type,$request->ruc);


            if($dataDocument["success"]){
                $name = $dataDocument["data"]["name"];
                if($type==='ruc'){
                    $address = $dataDocument['data']['address'];
                    $departmentId = $dataDocument['data']['location_id'][0] ?? null;
                    $provinceId = $dataDocument['data']['location_id'][1] ?? null;
                    $districtId = $dataDocument['data']['location_id'][2] ?? null;
                }
            }

            if(!($dataDocument["success"]) && $type==='dni'){
                $identity_document_type_id = 0;
            }

            $person = new Person();
            $person->type = 'customers';
            $person->identity_document_type_id = $identity_document_type_id;
            $person->number = $request->ruc;
            $person->name = $name;
            $person->country_id = 'PE';
            $person->nationality_id = 'PE';
            $person->department_id = $department_id;
            $person->province_id = $province_id;
            $person->district_id = $district_id;
            $person->address = $address;
            $person->establishment_code = '0000';
            $person->email = $request->email;
            $person->password = bcrypt($request->pswd);

            $person->save();

            $credentials = [ 'email' => $person->email, 'password' => $request->pswd ];
            Auth::guard('ecommerce')->attempt($credentials);
            return [
                'success' => true,
                'message' => 'Usuario registrado'
            ];

        }catch(Exception $e)
        {
            return [
                'success' => false,
                'message' =>  $e->getMessage()
            ];
        }

    }

    public function transactionFinally(Request $request)
    {
        try{
            //1. confirmar dato de comprobante en order
            $order_generated = Order::find($request->orderId);
            $order_generated->document_external_id = $request->document_external_id;
            $order_generated->number_document = $request->number_document;
            $order_generated->save();

            return [
                'success' => true,
                'message' => 'Order Actualizada',
                'order_total' => $order_generated->total
            ];
        }
        catch(Exception $e)
        {
            return [
                'success' => false,
                'message' =>  $e->getMessage()
            ];
        }

    }

    /**
     * Valida uno o varios códigos de cupón y retorna el mejor aplicable
     */
    public function validateCoupon(Request $request)
    {
        $codes = [];
        if ($request->codes && is_array($request->codes)) {
            $codes = $request->codes;
        } elseif ($request->code) {
            $codes = [$request->code];
        }

        if (empty($codes)) {
            return response()->json(['success' => false, 'message' => 'cupon no existe'], 404);
        }

        $order_total = (float) ($request->order_total ?? 0);
        $user = auth('ecommerce')->user();
        $person_id = $user?->id;

        $found = false;
        $validCoupons = [];

        foreach ($codes as $code) {
            $coupon = DiscountCoupon::where('code', $code)->first();
            if (!$coupon) continue;
            $found = true;

            if (!$coupon->active) continue;
            if ($coupon->is_expired) continue;

            if (!$coupon->canBeUsedBy($person_id, $order_total)) continue;

            $discount = $coupon->calculateDiscountAmount($order_total);
            $validCoupons[] = [
                'coupon' => $coupon,
                'discount' => $discount
            ];
        }

        if (empty($validCoupons)) {
            if (!$found) {
                return response()->json(['success' => false, 'message' => 'cupon no existe'], 404);
            }
            // Al menos uno existía pero ninguno es válido
            return response()->json(['success' => false, 'message' => 'cupon no valido'], 422);
        }

        // Escoger el de mayor descuento
        usort($validCoupons, function ($a, $b) {
            return $b['discount'] <=> $a['discount'];
        });

        $best = $validCoupons[0];
        $coupon = $best['coupon'];
        $discount = $best['discount'];
        $new_total = max(0, round($order_total - $discount, 2));

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'discount' => $discount,
                'new_total' => $new_total,
                'free_shipping' => (bool) $coupon->free_shipping
            ]
        ]);
    }

    /**
     * Aplica el cupón a una orden existente y registra el uso
     */
    public function applyCoupon(Request $request)
    {
        $order = Order::find($request->order_id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Orden no encontrada'], 404);
        }

        $user = auth('ecommerce')->user();
        $person_id = $user?->id;

        $coupon = null;
        if ($request->discount_coupon_id) {
            $coupon = DiscountCoupon::find($request->discount_coupon_id);
        } elseif ($request->discount_coupon_code) {
            $coupon = DiscountCoupon::where('code', $request->discount_coupon_code)->first();
        }

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'cupon no existe'], 404);
        }

        if (!$coupon->canBeUsedBy($person_id, $order->total)) {
            return response()->json(['success' => false, 'message' => 'cupon no valido'], 422);
        }

        $discount = $coupon->calculateDiscountAmount($order->total);

        // Actualizar la orden (un solo cupón por venta)
        $order->total_discount = $discount;
        $order->discount_coupon_code = $coupon->code;
        $order->discount_coupon_id = $coupon->id;
        $order->total = round(max(0, $order->total - $discount), 2);
        $order->save();

        // Registrar uso
        DiscountCouponUsage::create([
            'discount_coupon_id' => $coupon->id,
            'person_id' => $person_id,
            'order_id' => $order->id
        ]);

        return response()->json(['success' => true, 'order' => $order]);
    }

    public function paymentCash(Request $request)
    {

        $validator = Validator::make($request->customer, [
            'telefono' => 'required|numeric',
            'direccion' => 'required',
            'codigo_tipo_documento_identidad' => 'required|numeric',
            'numero_documento' => 'required|numeric',
            'identity_document_type_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } else {
            try {
                $type = ($request->purchase["datos_del_cliente_o_receptor"]["codigo_tipo_documento_identidad"]=='6')?'ruc':'dni';
                $document_number = $request->purchase["datos_del_cliente_o_receptor"]["numero_documento"];

                $dataDocument = $this->searchDocument($type,$document_number);
                if ($dataDocument["success"]) {
                    $clientData = [ "apellidos_y_nombres_o_razon_social" => $dataDocument["data"]["name"] ];
                    if ($type === 'ruc') {
                        $clientData["direccion"] = $dataDocument['data']['address'];
                        $clientData["ubigeo"] = $dataDocument['data']['location_id'][2] ?? null;
                    }
                    $request->merge([
                        'purchase' => array_merge($request->purchase, [
                            "datos_del_cliente_o_receptor" => array_merge(
                                $request->purchase["datos_del_cliente_o_receptor"],
                                $clientData
                            )
                        ])
                    ]);
                }

                $user = auth('ecommerce')->user();
                $order = Order::create([
                'external_id' => Str::uuid()->toString(),
                'customer' =>  $request->customer,
                'shipping_address' => $request->input('shipping_address', ''),
                'items' =>  $request->items,
                'total' => $request->precio_culqi,
                'reference_payment' => $request->input('reference_payment', 'efectivo'),
                'status_order_id' => 1,
                'purchase' => $request->purchase
              ]);

            // Si se envía cupón en la petición, aplicarlo inmediatamente
            try {
                if ($request->discount_coupon_code || $request->discount_coupon_id) {
                    $coupon = null;
                    if ($request->discount_coupon_id) {
                        $coupon = DiscountCoupon::find($request->discount_coupon_id);
                    } elseif ($request->discount_coupon_code) {
                        $coupon = DiscountCoupon::where('code', $request->discount_coupon_code)->first();
                    }

                    if ($coupon) {
                        // Si el frontend ya envió el monto de descuento (`total_discount`),
                        // usar ese valor en lugar de recalcularlo sobre el total ya descontado
                        // (evita aplicar el cupón dos veces).
                        if ($request->total_discount && $request->total_discount > 0) {
                            $discount = round((float) $request->total_discount, 2);
                            // reconstruir total original (antes del descuento) para validaciones
                            $original_total = round($order->total + $discount, 2);

                            if ($coupon->canBeUsedBy($user?->id, $original_total)) {
                                $order->total_discount = $discount;
                                $order->discount_coupon_code = $coupon->code;
                                $order->discount_coupon_id = $coupon->id;
                                // el total ya viene con descuento desde el frontend; mantenerlo
                                $order->total = round(max(0, $original_total - $discount), 2);
                                $order->save();

                                DiscountCouponUsage::create([
                                    'discount_coupon_id' => $coupon->id,
                                    'person_id' => $user?->id,
                                    'order_id' => $order->id
                                ]);
                            }
                        } else {
                            // Si frontend no envió el monto, calcularlo en servidor usando el total actual
                            if ($coupon->canBeUsedBy($user?->id, $order->total)) {
                                $discount = $coupon->calculateDiscountAmount($order->total);
                                $order->total_discount = $discount;
                                $order->discount_coupon_code = $coupon->code;
                                $order->discount_coupon_id = $coupon->id;
                                $order->total = round(max(0, $order->total - $discount), 2);
                                $order->save();

                                DiscountCouponUsage::create([
                                    'discount_coupon_id' => $coupon->id,
                                    'person_id' => $user?->id,
                                    'order_id' => $order->id
                                ]);
                            }
                        }
                    }
                }
            } catch(\Exception $e) {
                // No interrumpir el proceso por errores de cupón
            }

            $customer_email = $user->email;
            $document = new stdClass;
            $document->client = $user->name;
            $document->product = $request->producto;
            $document->total = $request->precio_culqi;
            $document->items = $request->items;

            $this->paymentCashEmail($customer_email, $document);

            //Mail::to($customer_email)->send(new CulqiEmail($document));
            return [
                'success' => true,
                'order' => $order
            ];

        }catch(Exception $e)
        {
            return [
                'success' => false,
                'message' =>  $e->getMessage()
            ];
        }
      }
    }

    public function paymentCashEmail($customer_email, $document)
    {
        try {
            $email = $customer_email;
            $mailable = new CulqiEmail($document);
            $id = (int) $document->id;
            $model = __FILE__.";;".__LINE__;
            $sendIt = EmailController::SendMail($email, $mailable, $id, $model);
            /*
            Configuration::setConfigSmtpMail();
            $array_email = explode(',', $customer_email);
            if (count($array_email) > 1) {
                foreach ($array_email as $email_to) {
                    $email_to = trim($email_to);
                if(!empty($email_to)) {
                        Mail::to($email_to)->send(new CulqiEmail($document));
                    }
                }
            } else {
                Mail::to($customer_email)->send(new CulqiEmail($document));
            }*/
        }catch(\Exception $e)
        {
            return true;
        }
    }

    public function ratingItem(Request $request)
    {
        if(auth('ecommerce')->user())
        {
            $user_id = auth('ecommerce')->id();
            $row = ItemsRating::firstOrNew( ['user_id' => $user_id, 'item_id' => $request->item_id ] );
            $row->value = $request->value;
            $row->save();
            return[
                'success' => false,
                'message' => 'Rating Guardado'
            ];
        }
        return[
            'success' => false,
            'message' => 'No se guardo Rating'
        ];

    }

    public function getRating($id)
    {
        if(auth('ecommerce')->user())
        {
            $user_id = auth('ecommerce')->id();
            $row = ItemsRating::where('user_id', $user_id)->where('item_id', $id)->first();
            return[
                'success' => true,
                'value' => ($row) ? $row->value : 0,
                'message' => 'Valor Obtenido'
            ];
        }
        return[
            'success' => false,
            'value' => 0,
            'message' => 'No se obtuvo valor'
        ];

    }

    private function getExchangeRateSale(){

        $exchange_rate = app(ServiceController::class)->exchangeRateTest(date('Y-m-d'));

        return (array_key_exists('sale', $exchange_rate)) ? $exchange_rate['sale'] : 1;


    }

    public function saveDataUser(Request $request)
    {
        $user = auth('ecommerce')->user();
        if ($request->address) {
            $user->address = $request->address;
        }
        if ($user->telephone = $request->telephone) {
            $user->telephone = $request->telephone;
        }

        $user->save();

        // Registrar la dirección de entrega en el historial de direcciones del cliente si tiene ubigeo completo
        $deliveryAddress = $request->input('delivery_address');
        $districtId      = $request->input('district_id');

        if ($deliveryAddress && $districtId) {
            $user->addresses()->firstOrCreate(
                [
                    'address'     => $deliveryAddress,
                    'district_id' => $districtId,
                ],
                [
                    'country_id'    => 'PE',
                    'department_id' => $request->input('department_id'),
                    'province_id'   => $request->input('province_id'),
                    'district_id'   => $districtId,
                    'address'       => $deliveryAddress,
                    'phone'         => $request->input('telephone'),
                    'main'          => false,
                ]
            );
        }

        return ['success' => true];

    }

    public function searchDocument($type, $number)
    {
        return (new ServiceData)->service($type, $number);
    }

    public function getGoogleMaps()
    {
        $config = SystemConfiguration::first();
        return $config ? $config->google_maps_api_key : null;
    }

    public function getGoogleMapsScript()
    {
        $apiKey = $this->getGoogleMaps();

        if (empty($apiKey)) {
            return response()->json(['error' => 'Google Maps API key is missing or invalid.'], 400);
        }

        $scriptUrl = "https://maps.googleapis.com/maps/api/js?key={$apiKey}&libraries=places";
        return redirect($scriptUrl);
    }

    public function getLocationCascade()
    {
        $locations = [];
        $departments = Department::where('active', true)->get();

        foreach ($departments as $department) {
            $children_provinces = [];
            foreach ($department->provinces as $province) {
                $children_districts = [];
                foreach ($province->districts as $district) {
                    $children_districts[] = [
                        'value' => $district->id,
                        'label' => $district->description
                    ];
                }
                $children_provinces[] = [
                    'value' => $province->id,
                    'label' => $province->description,
                    'children' => $children_districts
                ];
            }
            $locations[] = [
                'value' => $department->id,
                'label' => $department->description,
                'children' => $children_provinces
            ];
        }

        return response()->json($locations);
    }

    public function termsConditions()
    {
        $config = \App\Models\Tenant\ConfigurationEcommerce::first();
        $terms_conditions = $config ? $config->terms_conditions : null;
        $categories = \Modules\Item\Models\Category::get();
        return view('ecommerce::pages_fields.terms_conditions', compact('terms_conditions', 'categories'));
    }

    public function privacyPolicy()
    {
        $config = \App\Models\Tenant\ConfigurationEcommerce::first();
        $privacy_policy = $config ? $config->privacy_policy : null;
        $categories = \Modules\Item\Models\Category::get();
        return view('ecommerce::pages_fields.privacy_policy', compact('privacy_policy', 'categories'));
    }

    public function aboutUs()
    {
        $config = \App\Models\Tenant\ConfigurationEcommerce::first();
        $about_us = $config ? $config->about_us : null;
        $categories = \Modules\Item\Models\Category::get();
        return view('ecommerce::pages_fields.about_us', compact('about_us', 'categories'));
    }

    public function claimsBook()
    {
        $categories = \Modules\Item\Models\Category::get();
        return view('ecommerce::pages_fields.claims_book', compact('categories'));
    }

    /**
     * Verifica si una dirección tiene cobertura de delivery y retorna todas las zonas aplicables.
     * Recopila matches en todos los niveles de especificidad para que el usuario elija:
     *   1. Dpto + Prov + Distrito (coincidencia exacta)
     *   2. Dpto + Prov (province_id definido, district_id nulo en la fila)
     *   3. Solo Dpto (province_id nulo en la fila)
     *   Si hay coincidencias en varios niveles se devuelven todas para que el cliente seleccione.
     */
    public function checkDeliveryZone(Request $request)
    {
        $department = $request->input('department');
        $province   = $request->input('province');
        $district   = $request->input('district');

        if (empty($department)) {
            return response()->json(['found' => false, 'message' => '']);
        }

        // Colección de zonas activas
        $activeZoneIds = DeliveryZone::active()->pluck('id');

        if ($activeZoneIds->isEmpty()) {
            $message = ConfigurationEcommerce::first()?->delivery_no_coverage_message ?? '';
            return response()->json(['found' => false, 'configured' => false, 'message' => $message]);
        }

        $matchedZoneIds = collect();

        // Nivel 1: coincidencia exacta (dpto + prov + distrito)
        if ($district && $province) {
            $ids = DeliveryZoneLocation::whereIn('delivery_zone_id', $activeZoneIds)
                ->where('department_id', $department)
                ->where('province_id', $province)
                ->where('district_id', $district)
                ->pluck('delivery_zone_id');
            $matchedZoneIds = $matchedZoneIds->merge($ids);
        }

        // Nivel 2: coincidencia por dpto + provincia (sin distrito específico)
        if ($province) {
            $ids = DeliveryZoneLocation::whereIn('delivery_zone_id', $activeZoneIds)
                ->where('department_id', $department)
                ->where('province_id', $province)
                ->whereNull('district_id')
                ->pluck('delivery_zone_id');
            $matchedZoneIds = $matchedZoneIds->merge($ids);
        }

        // Nivel 3: cobertura sólo por departamento
        $ids = DeliveryZoneLocation::whereIn('delivery_zone_id', $activeZoneIds)
            ->where('department_id', $department)
            ->whereNull('province_id')
            ->whereNull('district_id')
            ->pluck('delivery_zone_id');
        $matchedZoneIds = $matchedZoneIds->merge($ids);

        $uniqueZoneIds = $matchedZoneIds->unique()->values();

        if ($uniqueZoneIds->isEmpty()) {
            $message = ConfigurationEcommerce::first()?->delivery_no_coverage_message ?? '';
            return response()->json(['found' => false, 'configured' => true, 'message' => $message]);
        }

        // Retornar todas las zonas que hacen match para que el usuario elija
        $zones = DeliveryZone::whereIn('id', $uniqueZoneIds)
            ->get()
            ->map(fn($zone) => [
                'id'    => $zone->id,
                'name'  => $zone->name,
                'price' => $zone->price,
            ])
            ->values();

        return response()->json([
            'found' => true,
            'zones' => $zones,
        ]);
    }
}
