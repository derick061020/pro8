<?php

namespace Modules\Hotel\Http\Controllers;

use App\Models\Tenant\Person;
use App\Models\Tenant\Series;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Modules\Hotel\Models\HotelRent;
use Modules\Hotel\Models\HotelRentItem;
use Modules\Hotel\Models\HotelRentItemPayment;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Models\HotelRoomRate;
use Modules\Hotel\Models\HotelRentChange;
use App\Models\Tenant\Item;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\Company;
use App\Models\Tenant\Establishment;
use Modules\Hotel\Models\HotelRentOrder;
use App\Models\Tenant\PaymentMethodType;
use Modules\Finance\Traits\FinanceTrait;
use App\Models\Tenant\Catalogs\DocumentType;
use Modules\Hotel\Http\Requests\HotelRentRequest;
use App\Models\Tenant\Catalogs\AffectationIgvType;
use Modules\Hotel\Http\Requests\HotelRentItemRequest;
use Modules\Hotel\Exports\HotelRentExport;
use Carbon\Carbon;
use App\Models\Tenant\SaleNote;
use App\Models\Tenant\Document;

class HotelRentController extends Controller
{
    use FinanceTrait;

	public function rent($roomId)
	{
		$room = HotelRoom::with('category', 'rates.rate')
			->findOrFail($roomId);

		$affectation_igv_types = AffectationIgvType::whereActive()->get();
		$series = Series::where('establishment_id',  auth()->user()->establishment_id)->get();

		// Verificar si es un check-in de reserva
		$isCheckin = request()->get('checkin', false);
		$reservation = null;

		if ($isCheckin) {
			// Buscar la reserva activa para esta habitación
			$reservation = HotelRent::with('customer', 'items.payments')
				->where('hotel_room_id', $roomId)
				->where('is_reserve', true)
				->orderBy('id', 'DESC')
				->first();

			if (!$reservation) {
				return redirect()->back()->with('error', 'No se encontró una reserva para check-in');
			}

			// Adelanto ya abonado en la reserva (suma de pagos de sus items).
			// Se expone al frontend para mostrarlo y descontarlo del saldo.
			$advancePaid = 0;
			foreach ($reservation->items as $resItem) {
				if ($resItem->payments) {
					$advancePaid += (float) $resItem->payments->payment;
				}
			}
			$reservation->advance_paid = round($advancePaid, 2);
		}

		return view('hotel::rooms.rent', compact('room', 'affectation_igv_types','series', 'reservation'));
	}

	public function store(HotelRentRequest $request, $roomId)
	{
		DB::connection('tenant')->beginTransaction();
		try {
			$room = HotelRoom::findOrFail($roomId);

			// Detectar si es una reserva (no check-in inmediato)
			$isReservationFlag = (bool) $request->input('is_reservation', false);
			$refererHeader     = request()->header('referer');
			$isFromCalendar    = $refererHeader && strpos($refererHeader, 'reservations/calendar') !== false;
			$isReservationCtx  = $isReservationFlag || $isFromCalendar || $request->input('source') === 'calendar';

			// Validación de estado de la habitación.
			// Para check-in inmediato: la habitación debe estar DISPONIBLE.
			// Para reservas futuras: aceptamos cualquier estado salvo MANTENIMIENTO
			// (la disponibilidad real se valida por solapamiento de fechas más abajo).
			if ($room->status === 'MANTENIMIENTO') {
				DB::connection('tenant')->rollBack();
				return response()->json([
					'success' => false,
					'message' => 'La habitación está en mantenimiento y no puede recibir reservas.',
				], 422);
			}
			if (!$isReservationCtx && $room->status !== 'DISPONIBLE') {
				DB::connection('tenant')->rollBack();
				$label = ['OCUPADO' => 'ocupada', 'LIMPIEZA' => 'en limpieza', 'MANTENIMIENTO' => 'en mantenimiento'][$room->status] ?? strtolower((string) $room->status);
				return response()->json([
					'success' => false,
					'message' => "La habitación está {$label} y no puede recibir un check-in.",
				], 422);
			}

			// Verificar solapamiento (fecha + hora) con cualquier alquiler/reserva activo
			$newStart = Carbon::parse(
				$request->input('input_date') . ' ' . ($request->input('input_time') ?: '14:00')
			);
			$newEnd   = Carbon::parse(
				$request->input('output_date') . ' ' . ($request->input('output_time') ?: '12:00')
			);

			// Check-in que proviene de una reserva existente: la reserva de origen
			// cubre el mismo rango de fechas, por lo que NO debe contar como un
			// conflicto consigo misma. La excluimos del chequeo de solapamiento.
			$isCheckinFromReservation = (bool) $request->input('is_checkin_from_reservation', false);
			$sourceReservationId      = $request->input('reservation_id');
			$excludeReservationId     = ($isCheckinFromReservation && $sourceReservationId)
				? $sourceReservationId
				: null;

			$conflict = HotelRent::findOverlappingRent($roomId, $newStart, $newEnd, $excludeReservationId);
			if ($conflict) {
				DB::connection('tenant')->rollBack();
				$cStart = Carbon::parse($conflict->input_date . ' ' . ($conflict->input_time ?: '14:00'))->format('d/m/Y H:i');
				$cEnd   = Carbon::parse($conflict->output_date . ' ' . ($conflict->output_time ?: '12:00'))->format('d/m/Y H:i');
				return response()->json([
					'success' => false,
					'message' => "La habitación ya está reservada del {$cStart} al {$cEnd}.",
				], 422);
			}

			$request->merge(['hotel_room_id' => $roomId]);
			$request->merge(['establishment_id' => $room->establishment_id]);
			
			// Detectar si es una reserva
			$isReservation = $request->input('is_reservation', false);
			// También verificar por referer para mayor seguridad
			$referer = request()->header('referer');
			$isFromCalendar = $referer && strpos($referer, 'reservations/calendar') !== false;
			// Verificar si hay parámetros GET que indiquen reserva
			$hasGetParams = count($request->query()) > 0;
			// Verificar user agent para detectar iframe
			$userAgent = request()->header('User-Agent');
			$isIframe = $userAgent && (strpos($userAgent, 'Mozilla') !== false || strpos($userAgent, 'Chrome') !== false);
			// Verificar parámetro source
			$source = $request->input('source', '');
			$isFromCalendarSource = $source === 'calendar';
			
			\Log::info('Detectando reserva - is_reservation parameter: ' . $isReservation);
			\Log::info('Referer: ' . $referer);
			\Log::info('Is from calendar: ' . $isFromCalendar);
			\Log::info('Has GET params: ' . $hasGetParams);
			\Log::info('Is iframe: ' . $isIframe);
			\Log::info('Source parameter: ' . $source);
			\Log::info('Is from calendar source: ' . $isFromCalendarSource);
			\Log::info('All request data: ' . json_encode($request->all()));
			
			// Capturar el estado de pago solicitado por el usuario ANTES de cualquier
			// override por reserva, para no perder la intención de adelanto.
			$requestedPaymentStatus = $request->input('payment_status');
			$isAdvancePayment = $requestedPaymentStatus === 'ADVANCE';

			if ($isReservation || $isFromCalendar || ($hasGetParams && $isIframe) || $isFromCalendarSource) {
				$request->merge(['is_reserve' => true]);
				// Las reservas empiezan como pendientes, salvo que el usuario registre
				// un adelanto: en ese caso la reserva queda como DEBT (pago parcial) y
				// el adelanto se registra más abajo.
				if (!$isAdvancePayment) {
					$request->merge(['payment_status' => 'PENDING']);
				}
				\Log::info('Reserva detectada - is_reserve set to true');
			}

			if ($isAdvancePayment) {
				// El adelanto es un pago parcial: la renta se mantiene en DEBT.
				$request->merge(['payment_status' => 'DEBT']);
			}

			// Calcular fecha y hora de renta basada en el período
			$rentalDateTime = null;
			$rentalPrice = $request->input('rental_price');
			$rentalPeriodType = $request->input('rental_period_type', 'day'); // default: day
			
			if ($rentalPeriodType === 'hour') {
				$rentalDateTime = \Carbon\Carbon::createFromFormat(
					'Y-m-d H:i:s', 
					$request->input('input_date') . ' ' . $request->input('input_time') . ':00'
				);
			} elseif ($rentalPeriodType === 'month') {
				$rentalDateTime = \Carbon\Carbon::createFromFormat(
					'Y-m-d', 
					$request->input('input_date')
				)->startOfMonth();
			} else { // day (default)
				$rentalDateTime = \Carbon\Carbon::createFromFormat(
					'Y-m-d', 
					$request->input('input_date')
				)->setTimeFromTimeString($request->input('input_time', '14:00'));
			}
			
			$rentData = $request->only('customer_id', 'customer', 'notes', 'license_plate', 'travel_reason', 'adults', 'children', 'towels', 'hotel_room_id', 'hotel_rate_id', 'duration', 'quantity_persons', 'payment_status', 'output_date', 'output_time', 'input_date', 'input_time','data_persons','establishment_id','is_reserve');
			$rentData['rental_date_time'] = $rentalDateTime;
			$rentData['rental_price'] = $rentalPrice;
			$rentData['rental_period_type'] = $rentalPeriodType;
			
			\Log::info('Rent data before save: ' . json_encode($rentData));
			
			$rent = HotelRent::create($rentData);
			
			\Log::info('Rent created with is_reserve: ' . $rent->is_reserve);

			// Solo cambiar estado a OCUPADO si no es una reserva
			if (!$rent->is_reserve) {
				$room->status = 'OCUPADO';
				$room->save();
			} else {
				// Para reservas, mantener la habitación como disponible
				\Log::info('Reserva creada, manteniendo habitación como DISPONIBLE');
			}

			// Si este check-in convirtió una reserva en renta real, finalizar la
			// reserva de origen para no dejar un registro duplicado que vuelva a
			// bloquear la habitación en validaciones, calendario y reportes.
			if ($isCheckinFromReservation && $sourceReservationId) {
				$originalReservation = HotelRent::find($sourceReservationId);
				if ($originalReservation && $originalReservation->is_reserve && $originalReservation->id !== $rent->id) {
					$originalReservation->status = 'FINALIZADO';
					$originalReservation->save();
					\Log::info('Reserva de origen finalizada tras check-in', ['reservation_id' => $sourceReservationId, 'rent_id' => $rent->id]);
				}
			}

			// Inicializar variable $order para evitar undefined
			$order = null;
			
			// Solo generar orden si NO es renta como pagado
			// Si es renta como pagado, NO generar comprobante automáticamente
			if ($request->payment_status !== 'PAID') {
				$order = new HotelRentOrder();
				$order->hotel_rent_id = $rent->id;
				$order->order_number = 1;
				$order->order_status = $request->payment_status;
				$order->sale_note_id = $request->sale_note_id;
				$order->establishment_id = $room->establishment_id;
				$order->save();
			}
			
			// Si es renta como pagado, NO generar orden pero sí redirigir
			if ($request->payment_status === 'PAID') {
				// No crear orden, solo redirigir a recepción
				// La habitación ya está marcada como ocupada por el flujo normal
			}
			
			// Guardar cambios en la base de datos
			DB::connection('tenant')->commit();

			// Agregando la habitación a la lista de productos
			$item = new HotelRentItem();
			$item->type = 'HAB';
			$item->hotel_rent_id = $rent->id;
			$item->item_id = $request->product['item_id'];

			// Agregar la unidad de tiempo correcta a la descripción del item
			$product = $request->product;
			$timeUnit = 'noche(s)';
			if ($rentalPeriodType === 'hour') {
				$timeUnit = 'hora(s)';
			} elseif ($rentalPeriodType === 'month') {
				$timeUnit = 'mes(es)';
			}

			// Modificar la descripción del item para incluir la unidad de tiempo.
			// El sufijo debe aplicarse tanto a la descripción "externa" (la que ven
			// las vistas del checkout) como a la descripción "anidada"
			// ($product['item']['description']), que es la que termina en el XML/PDF
			// del comprobante (Facturalo lee $it->item->description). Si solo se
			// modifica la externa, el comprobante muestra "Habitación X" sin noches.
			$durationSuffix = ' x ' . $request->input('duration') . ' ' . $timeUnit;

			if (isset($product['description'])) {
				$product['description'] = $product['description'] . $durationSuffix;
			}

			if (isset($product['item']) && is_array($product['item'])) {
				if (isset($product['item']['description'])) {
					$product['item']['description'] = $product['item']['description'] . $durationSuffix;
				}
				if (isset($product['item']['name_product_pdf']) && !empty($product['item']['name_product_pdf'])) {
					$product['item']['name_product_pdf'] = $product['item']['name_product_pdf'] . $durationSuffix;
				}
			}

			$item->item = $product;
			// ADVANCE = pago parcial — el item queda como DEBT pero registramos el pago
			$isAdvance = $isAdvancePayment;
			$item->payment_status = $isAdvance ? 'DEBT' : $request->payment_status;
			$item->hotel_rent_order_id = $order ? $order->id : null;
			$item->save();

			//registrar pago (PAID o ADVANCE registran el monto pagado)
			if ($isAdvance && $request->rent_payment && ($request->rent_payment['payment'] ?? 0) > 0) {
				HotelRentItemPayment::create([
					'hotel_rent_item_id'     => $item->id,
					'date_of_payment'        => date('Y-m-d'),
					'payment_method_type_id' => $request->rent_payment['payment_method_type_id'],
					'reference'              => $request->rent_payment['reference'] ?? null,
					'payment'                => $request->rent_payment['payment'],
				]);
			} else {
				$this->saveHotelRentItemPayment($request->rent_payment, $item);
			}

			// Check-in de una reserva con adelanto: trasladar los pagos (adelantos)
			// que ya estaban registrados en la reserva original al nuevo item, para
			// que el monto abonado figure en el checkout y reduzca el saldo. Se
			// reasignan (no se duplican) para no contar el ingreso dos veces.
			if ($isCheckinFromReservation && $sourceReservationId) {
				$reservationItemIds = HotelRentItem::where('hotel_rent_id', $sourceReservationId)
					->pluck('id');
				if ($reservationItemIds->isNotEmpty()) {
					HotelRentItemPayment::whereIn('hotel_rent_item_id', $reservationItemIds)
						->update(['hotel_rent_item_id' => $item->id]);
					// Si el adelanto cubre el total del item, marcarlo como pagado.
					$this->applyAdvanceCreditToDebtItems($rent);
				}
			}

			// Si es renta como pagado, redirigir a recepción
			if ($request->payment_status === 'PAID') {
				return response()->json([
					'success' => true,
					'message' => 'Habitación rentada como pagada correctamente.',
					'redirect' => '/hotels/reception'
				], 200);
			}

			DB::connection('tenant')->commit();

			return response()->json([
				'success' => true,
				'message' => 'Habitación rentada de forma correcta.',
			], 200);
		} catch (\Throwable $th) {
			DB::connection('tenant')->rollBack();

			return response()->json([
				'success' => false,
				'message' => 'No se puede procesar su transacción. Detalles: ' . $th->getMessage(),
			], 500);
		}
	}

	private function getOrderNumberHotelRent($establishment_id)
	{
		
	}

	/**
	 *
	 * Registrar pago si la habitacion/producto fueron pagados
	 *
	 * @param  array $rent_payment
	 * @param  HotelRentItem $item
	 * @return void
	 */
	/**
	 *
	 * Registrar el pago en caja/finanzas (pago global) usando el destino
	 * seleccionado, igual que en el resto del sistema. Si no hay destino no se
	 * registra el movimiento de caja (compatibilidad con pagos antiguos).
	 *
	 * @param  HotelRentItemPayment $payment
	 * @param  string|int|null      $paymentDestinationId
	 * @return void
	 */
	private function registerRentPaymentInCash(HotelRentItemPayment $payment, $paymentDestinationId)
	{
		if (empty($paymentDestinationId)) {
			return;
		}

		// Validar que la caja esté abierta antes de registrar el ingreso.
		if ($paymentDestinationId === 'cash') {
			$cash = $this->getCash();
			if (!$cash || empty($cash['cash_id'])) {
				throw new \Exception('La caja se encuentra cerrada. Debes abrirla antes de registrar el pago.');
			}
		}

		// Evitar duplicar el pago global al editar un pago existente.
		$payment->load('global_payment');
		if ($payment->global_payment) {
			$payment->global_payment()->delete();
		}

		$this->createGlobalPayment($payment, [
			'payment_destination_id' => $paymentDestinationId,
		]);
	}

	public function saveHotelRentItemPayment($rent_payment, HotelRentItem $item)
	{
		if($item->isPaid())
		{
			$record = $item->payments()->create([
				'date_of_payment' => date('Y-m-d'),
				'payment_method_type_id' => $rent_payment['payment_method_type_id'],
				'reference' => $rent_payment['reference'],
				'payment' => $rent_payment['payment'],
			]);
		}
	}


	/**
	 *
	 * Eliminar pago
	 *
	 * @param  HotelRentItem $item
	 * @return void
	 */
	public function deleteHotelRentItemPayment(HotelRentItem $item)
	{
		if(!is_null($item->payments))
		{
			$item->payments->delete();
		}
	}

  public function extendTime(Request $request, $rentId)
  {
    try {
      DB::beginTransaction();

      $rent = HotelRent::findOrFail($rentId);

      $previousDuration = (int) ($rent->duration ?? 0);
      $newTotalDuration = (int) $request->duration;
      $additional       = max(0, $newTotalDuration - $previousDuration);

      // Generar SIEMPRE un nuevo item HAB para la extensión, de modo que se pueda
      // emitir un comprobante por separado y el checkout muestre el cargo nuevo.
      $period     = $rent->rental_period_type ?: 'day';
      $unitLabel  = $period === 'hour' ? 'hora(s)' : ($period === 'month' ? 'mes(es)' : 'noche(s)');
      $unitPrice  = (float) ($request->item['item']['unit_price'] ?? 0);
      if ($unitPrice <= 0) {
        $unitPrice = (float) ($request->price_per_day ?? 0);
      }
      $quantity   = $additional > 0 ? $additional : 1;
      $totalExt   = round($unitPrice * $quantity, 4);

      $room = HotelRoom::find($rent->hotel_room_id);
      $roomName = $room ? $room->name : 'Habitación';
      $description = sprintf('Extensión %s - %d %s', $roomName, $quantity, $unitLabel);

      // Construir JSON del item de extensión basado en el item original (para preservar IGV/charges)
      $baseItem = $rent->items()->where('type', 'HAB')->orderByDesc('id')->first();
      $baseJson = $baseItem && is_object($baseItem->item) ? (array) $baseItem->item : [];

      $percentage_igv  = isset($baseJson['percentage_igv']) ? floatval($baseJson['percentage_igv']) : 18;
      $total_base_igv  = $totalExt / (1 + ($percentage_igv / 100));
      $total_igv       = $totalExt - $total_base_igv;

      // Sub-objeto interno (lo que las vistas leen como it.item.item.description)
      $baseInner = isset($baseJson['item']) ? $baseJson['item'] : [];
      if (is_object($baseInner)) $baseInner = (array) $baseInner;
      if (!is_array($baseInner)) $baseInner = [];

      $innerNew = array_merge($baseInner, [
        'description'      => $description,
        'full_description' => $description,
        'unit_price'       => round($unitPrice, 4),
      ]);

      $extensionJson = array_merge($baseJson, [
        'description'            => $description,
        'full_description'       => $description,
        'name_product_pdf'       => $description,
        'quantity'               => $quantity,
        'unit_value'             => round($unitPrice, 4),
        'unit_price'             => round($unitPrice, 4),
        'unit_price_value'       => round($unitPrice, 4),
        'input_unit_price_value' => round($unitPrice, 4),
        'total'                  => round($totalExt, 4),
        'total_value'            => round($total_base_igv, 4),
        'total_base_igv'         => round($total_base_igv, 4),
        'total_igv'              => round($total_igv, 4),
        'total_taxes'            => round($total_igv, 4),
        // Marca para que Checkout pueda separar extensiones del HAB original
        'is_extension'           => true,
        // Sub-objeto interno (las tablas de pagados/pendientes leen aquí)
        'item'                   => $innerNew,
      ]);

      $extensionItem = new HotelRentItem();
      $extensionItem->type = 'HAB';
      $extensionItem->hotel_rent_id = $rent->id;
      $extensionItem->item_id = $baseItem ? $baseItem->item_id : ($room ? $room->item_id : null);
      $extensionItem->item = $extensionJson;
      $extensionItem->quantity = $quantity;
      $extensionItem->unit_price = $unitPrice;
      $extensionItem->total = $totalExt;
      $extensionItem->description = $description;
      $extensionItem->payment_status = 'DEBT';
      $extensionItem->hotel_rent_order_id = null;
      $extensionItem->save();

      // Guardar fechas/duración antiguas para el historial
      $oldDuration   = $previousDuration;
      $oldOutputDate = $rent->output_date;
      $oldOutputTime = $rent->output_time;

      // Actualizar fechas y duración total del rent
      $rent->duration = $newTotalDuration;
      $rent->output_date = $request->output_date;
      $rent->output_time = $request->output_time;
      $rent->save();

      // Aplicar adelantos disponibles antes de registrar el historial — si
      // el huésped ya pagó de más, la extensión se marca PAID automáticamente.
      $this->applyAdvanceCreditToDebtItems($rent);

      // Registrar en el historial de cambios — antes esto NO se grababa, por
      // eso las extensiones no aparecían en "Historial de cambios".
      HotelRentChange::create([
        'hotel_rent_id'    => $rent->id,
        'change_type'      => 'EXTENSION',
        'old_values'       => [
          'duration'      => $oldDuration,
          'output_date'   => $oldOutputDate,
          'output_time'   => $oldOutputTime,
          'hotel_room_id' => $rent->hotel_room_id,
          'room_name'     => $roomName,
        ],
        'new_values'       => [
          'duration'      => $newTotalDuration,
          'output_date'   => $request->output_date,
          'output_time'   => $request->output_time,
          'added'         => $additional,
          'unit'          => $unitLabel,
          'unit_price'    => $unitPrice,
          'item_id'       => $extensionItem->id,
          'hotel_room_id' => $rent->hotel_room_id,
          'room_name'     => $roomName,
        ],
        'notes'            => "Extensión de {$additional} {$unitLabel} en {$roomName}",
        'price_difference' => round($totalExt, 4),
        'user_id'          => auth()->id(),
      ]);

      // Procesar pago si se incluye - se aplica a la extensión recién creada
      if ($request->include_payment && $request->payment_amount > 0) {
        $paymentMethodId = $this->getPaymentMethodId($request->payment_method);

        if (!$paymentMethodId) {
          throw new \Exception('No se encontró un método de pago válido. Por favor, configure los métodos de pago en el sistema.');
        }

        $payment = new HotelRentItemPayment();
        $payment->hotel_rent_item_id = $extensionItem->id;
        $payment->date_of_payment = date('Y-m-d');
        $payment->payment_method_type_id = $paymentMethodId;
        $payment->reference = $request->payment_reference ?? null;
        $payment->payment = $request->payment_amount;
        $payment->save();

        if ($request->payment_amount >= $totalExt) {
          $extensionItem->payment_status = 'PAID';
          $extensionItem->save();
        }
      }

      DB::commit();

      return response()->json([
        'success' => true,
        'message' => 'Estadía extendida y registrada como cargo separado.',
      ], 200);

    } catch (\Exception $e) {
      DB::rollBack();

      return response()->json([
        'success' => false,
        'message' => 'Error al actualizar la habitación: ' . $e->getMessage(),
      ], 500);
    }
  }

  /**
   * Obtener el ID del método de pago según el método seleccionado
   */
  private function getPaymentMethodId($method)
  {
    // Obtener el ID real de la base de datos
    $paymentMethod = PaymentMethodType::where('description', 'like', '%'.ucfirst($method).'%')
      ->orWhere('id', $method) // Si ya es un ID numérico
      ->first();
    
    // Si no encuentra, buscar el método por defecto (efectivo)
    if (!$paymentMethod) {
      $paymentMethod = PaymentMethodType::where('description', 'Efectivo')
        ->orWhere('description', 'like', '%Efectivo%')
        ->first();
    }
    
    // Último fallback: usar el primer método disponible
    if (!$paymentMethod) {
      $paymentMethod = PaymentMethodType::first();
    }
    
    return $paymentMethod ? $paymentMethod->id : null;
  }

  /**
   * Eliminar una rent y sus documentos relacionados
   *
   * @param  int $rentId
   * @return Response
   */
  public function destroy($rentId)
  {
    try {
      DB::beginTransaction();
      
      $rent = HotelRent::findOrFail($rentId);
      
      // Eliminar documentos relacionados primero
      if ($rent->documents) {
        $rent->documents()->delete();
      }
      
      // Eliminar items de la rent
      if ($rent->items) {
        foreach ($rent->items as $item) {
          // Eliminar pagos del item
          if ($item->payments) {
            $item->payments()->delete();
          }
          $item->delete();
        }
      }
      
      // Eliminar la rent
      $rent->delete();
      
      DB::commit();
      
      return response()->json([
        'success' => true,
        'message' => 'Alquiler eliminado correctamente.',
      ], 200);
      
    } catch (\Exception $e) {
      DB::rollBack();
      
      return response()->json([
        'success' => false,
        'message' => 'Error al eliminar el alquiler: ' . $e->getMessage(),
      ], 500);
    }
  }


	public function searchCustomers()
	{
		$customers = $this->customers();

		return response()->json([
			'customers' => $customers,
		], 200);
	}

	public function showFormAddProduct($rentId){
		$rent = HotelRent::with('room')
			->findOrFail($rentId);

		$establishment = Establishment::query()->find(auth()->user()->establishment_id);
		$configuration = Configuration::first();

		$products = HotelRentItem::select(
				'hotel_rent_items.*', 
				DB::raw("IFNULL(CONCAT(sale_notes.series, '-', sale_notes.number), '') as document")
			)
			->leftJoin('hotel_rent_orders', 'hotel_rent_items.hotel_rent_order_id', '=', 'hotel_rent_orders.id')
			->leftJoin('sale_notes', 'hotel_rent_orders.sale_note_id', '=', 'sale_notes.id')
			->where('hotel_rent_items.hotel_rent_id', $rentId)
			->where('hotel_rent_items.type', 'PRO')
			->get();

		$series = Series::where('establishment_id',  auth()->user()->establishment_id)->get();

		return view('hotel::rooms.add-product-to-room', compact('rent', 'configuration', 'products', 'establishment','series'));
	}


	/**
	 *
	 * Agregar productos al rentar habitacion
	 *
	 * @param  HotelRentItemRequest $request
	 * @param  int $rentId
	 * @return array
	 */
	public function addProductsToRoom(HotelRentItemRequest $request, $rentId)
	{
		$rent = HotelRent::findOrFail($rentId);

		if( isset($request->sale_note_id) && $request->sale_note_id !=null) {
			$order = new HotelRentOrder();
			$order->hotel_rent_id = $rentId;
			$order->order_number = 1;
			$order->order_status = 'PAID';
			$order->sale_note_id = $request->sale_note_id;
			$order->establishment_id = $rent->establishment_id;
			$order->save();
		}
		
		foreach ($request->products as $product) {
			$item = HotelRentItem::where('hotel_rent_id', $rentId)
				->where('id', $product['id'])
				->first();
			if (!$item) {
				$item = new HotelRentItem();
				$item->type = 'PRO';
				$item->hotel_rent_id = $rentId;
				$item->item_id = $product['item_id'];
				$item->payment_status = $product['payment_status'];
				$item->save();

				$this->saveHotelRentItemPayment($product['rent_payment'], $item);
			}
			$item->item = $product;
			$item->payment_status = $product['payment_status'];
			$item->hotel_rent_order_id =  null;
			$item->save();
            $idInRequest[] = $item->id;

		}

		// Auto-aplicar adelantos disponibles a items con deuda
		$this->applyAdvanceCreditToDebtItems($rent);

		return response()->json([
			'success' => true,
			'message' => 'Información actualizada.'
		], 200);
	}

	/**
	 * Aplica los pagos previos en exceso (adelantos) a los items con deuda.
	 * Calcula cuánto se debe (suma de items DEBT) vs cuánto ya se pagó en
	 * total. Si los pagos cubren la deuda de algún item, ese item se marca
	 * como PAID automáticamente.
	 */
	private function applyAdvanceCreditToDebtItems(HotelRent $rent)
	{
		$debtItems = HotelRentItem::where('hotel_rent_id', $rent->id)
			->where('payment_status', 'DEBT')
			->orderBy('id', 'asc')
			->get();

		if ($debtItems->isEmpty()) return;

		// Total ya pagado: suma de todos los HotelRentItemPayment del rent
		$totalPaid = HotelRentItemPayment::whereHas('associated_record_payment', function ($q) use ($rent) {
			$q->where('hotel_rent_id', $rent->id);
		})->sum('payment');

		// Total ya cobrado en items distintos a los que tienen deuda
		// (los items PAID ya están "cubiertos" por su parte del pago).
		$paidItemsTotal = HotelRentItem::where('hotel_rent_id', $rent->id)
			->where('payment_status', 'PAID')
			->where('type', '!=', 'PAY')
			->get()
			->sum(function ($i) {
				$json = is_object($i->item) ? (array) $i->item : ($i->item ?: []);
				return floatval($json['total'] ?? 0);
			});

		$availableCredit = floatval($totalPaid) - floatval($paidItemsTotal);

		if ($availableCredit <= 0) return;

		foreach ($debtItems as $item) {
			if ($availableCredit <= 0) break;
			$json = is_object($item->item) ? (array) $item->item : ($item->item ?: []);
			$itemTotal = floatval($json['total'] ?? 0);
			if ($itemTotal <= 0) continue;
			if ($availableCredit >= $itemTotal) {
				$item->payment_status = 'PAID';
				$item->save();
				$availableCredit -= $itemTotal;
			}
		}
	}

  public function showFormChekout($rentId)
  {
    $rent = HotelRent::with('room', 'room.category', 'items')
      ->findOrFail($rentId);

	$items = HotelRentItem::select(
			'hotel_rent_items.*',
			DB::raw("COALESCE(
				CASE WHEN documents.id IS NOT NULL THEN CONCAT(documents.series, '-', documents.number) END,
				CASE WHEN sale_notes.id IS NOT NULL THEN CONCAT(sale_notes.series, '-', sale_notes.number) END,
				''
			) as document"),
			DB::raw("COALESCE(documents.total, sale_notes.total, 0) as sale_note_total")
		)
		->leftJoin('sale_notes', 'hotel_rent_items.sale_note_id', '=', 'sale_notes.id')
		->leftJoin('documents', 'hotel_rent_items.document_id', '=', 'documents.id')
		->where('hotel_rent_items.hotel_rent_id', $rent->id)
		->get();
	
	// HAB más reciente (activo) — tras un cambio de habitación, ese item es el vigente.
	$room = $items->where('type', 'HAB')->last();

    $customer = Person::withOut('department', 'province', 'district')
      ->findOrFail($rent->customer_id);

        $payment_method_types = PaymentMethodType::getPaymentMethodTypes();
        $payment_destinations = $this->getPaymentDestinations();
        $series = Series::where('establishment_id',  auth()->user()->establishment_id)->get();
        $document_types_invoice = DocumentType::whereIn('id', ['01', '03', '80'])->get();
    	$affectation_igv_types = AffectationIgvType::whereActive()->get();
		
		// Obtener todos los pagos asociados a los items de este rent
		$payments = HotelRentItemPayment::whereHas('associated_record_payment', function ($query) use ($rentId) {
			$query->whereHas('hotel_rent', function ($query) use ($rentId) {
				$query->where('id', $rentId);
			});
		})->with('associated_record_payment')->get();

    return view('hotel::rooms.checkout', compact(
            'rent', 'room',
            'customer',
            'payment_method_types',
            'payment_destinations',
            'series',
            'document_types_invoice',
      		'affectation_igv_types',
			'payments',
			'items'
        ));
  }

  /**
   * Obtener datos del checkout para AJAX
   */
  public function getCheckoutData($rentId)
  {
    $rent = HotelRent::with('room', 'room.category', 'items')
      ->findOrFail($rentId);

	$items = HotelRentItem::select(
			'hotel_rent_items.*',
			DB::raw("COALESCE(
				CASE WHEN documents.id IS NOT NULL THEN CONCAT(documents.series, '-', documents.number) END,
				CASE WHEN sale_notes.id IS NOT NULL THEN CONCAT(sale_notes.series, '-', sale_notes.number) END,
				''
			) as document"),
			DB::raw("COALESCE(documents.total, sale_notes.total, 0) as sale_note_total")
		)
		->leftJoin('sale_notes', 'hotel_rent_items.sale_note_id', '=', 'sale_notes.id')
		->leftJoin('documents', 'hotel_rent_items.document_id', '=', 'documents.id')
		->where('hotel_rent_items.hotel_rent_id', $rent->id)
		->get();
	
	// HAB más reciente (activo) — tras un cambio de habitación, ese item es el vigente.
	$room = $items->where('type', 'HAB')->last();

    $customer = Person::withOut('department', 'province', 'district')
      ->findOrFail($rent->customer_id);

        $payment_method_types = PaymentMethodType::getPaymentMethodTypes();
        $payment_destinations = $this->getPaymentDestinations();
        $series = Series::where('establishment_id', auth()->user()->establishment_id)->get();
        $document_types_invoice = DocumentType::whereIn('id', ['01', '03', '80'])->get();
    	$affectation_igv_types = AffectationIgvType::whereActive()->get();
		
		// Obtener todos los pagos asociados a los items de este rent
		$payments = HotelRentItemPayment::whereHas('associated_record_payment', function ($query) use ($rentId) {
			$query->whereHas('hotel_rent', function ($query) use ($rentId) {
				$query->where('id', $rentId);
			});
		})->with('associated_record_payment')->get();

    return response()->json([
            'success' => true,
            'data' => [
                'rent' => $rent,
                'room' => $room,
                'customer' => $customer,
                'payment_method_types' => $payment_method_types,
                'payment_destinations' => $payment_destinations,
                'series' => $series,
                'document_types_invoice' => $document_types_invoice,
                'affectation_igv_types' => $affectation_igv_types,
                'payments' => $payments,
                'items' => $items
            ]
        ]);
  }

  public function finalizeRent($rentId)
  {
    try {
      DB::connection('tenant')->beginTransaction();

      $rent = HotelRent::findOrFail($rentId);
      $items = HotelRentItem::where('hotel_rent_id', $rentId)->get();

      // arrears NUNCA debe ser null para no romper la columna NOT NULL.
      $arrears = request('arrears');
      $arrears = is_numeric($arrears) ? (int) $arrears : 0;

      // Registrar la salida real: se sobreescribe output_date/output_time con
      // el momento exacto en que se marca el checkout, para que el reporte
      // muestre la hora real de salida (no la programada).
      $checkoutAt = Carbon::now();

      $rent->update([
        'arrears'        => $arrears,
        'payment_status' => 'PAID',
        'status'         => 'FINALIZADO',
        'output_date'    => $checkoutAt->format('Y-m-d'),
        'output_time'    => $checkoutAt->format('H:i:s'),
      ]);

      foreach ($items as $item) {
        $item->update([
          'payment_status' => 'PAID',
        ]);
      }

      // Solo cambiar el estado de la habitación a LIMPIEZA. El registro en
      // hotel_cleanings se crea cuando recepción asigna un limpiador (la
      // columna user_id es NOT NULL, así que no podemos precrearlo nulo).
      $room = HotelRoom::find($rent->hotel_room_id);
      if ($room) {
        $room->status = 'LIMPIEZA';
        $room->save();
      }

      DB::connection('tenant')->commit();

      $rent = HotelRent::with('room', 'room.category', 'items')->findOrFail($rentId);
      return response()->json([
        'success'     => true,
        'message'     => 'Información procesada de forma correcta.',
        'currentRent' => $rent,
      ], 200);
    } catch (\Throwable $th) {
      DB::connection('tenant')->rollBack();
      return response()->json([
        'success' => false,
        'message' => 'Error al finalizar el alquiler: ' . $th->getMessage(),
      ], 500);
    }
  }

	/**
	 * Marca los items pasados como facturados, asociandolos al sale_note recien creado.
	 * Se llama desde el frontend luego de POST /sale-notes para "lockear" los items facturados.
	 */
	public function markItemsInvoiced(Request $request, $rentId)
	{
		$saleNoteId = $request->input('sale_note_id');
		$documentId = $request->input('document_id');
		$itemIds = $request->input('item_ids', []);

		\Log::info('markItemsInvoiced called', [
			'rentId' => $rentId,
			'saleNoteId' => $saleNoteId,
			'documentId' => $documentId,
			'itemIds' => $itemIds,
		]);

		if (!$saleNoteId && !$documentId) {
			return response()->json([
				'success' => false,
				'message' => 'Debe proporcionar sale_note_id o document_id.'
			], 422);
		}

		if (empty($itemIds)) {
			return response()->json([
				'success' => false,
				'message' => 'No se recibieron item_ids para marcar.'
			], 422);
		}

		try {
			$updated = HotelRentItem::where('hotel_rent_id', $rentId)
				->whereIn('id', $itemIds)
				->update([
					'sale_note_id' => $saleNoteId,
					'document_id' => $documentId,
					'invoiced_at' => now(),
					'payment_status' => 'PAID',
				]);
		} catch (\Exception $e) {
			\Log::error('markItemsInvoiced error: ' . $e->getMessage());
			return response()->json([
				'success' => false,
				'message' => 'Error SQL: ' . $e->getMessage(),
			], 500);
		}

		\Log::info('markItemsInvoiced result', ['updated_count' => $updated]);

		return response()->json([
			'success' => true,
			'message' => "Items marcados como facturados ({$updated}).",
			'updated_count' => $updated,
		]);
	}

	/**
	 * Historial de comprobantes generados para una renta.
	 */
	public function invoicesHistory($rentId)
	{
		$saleNoteIds = HotelRentItem::where('hotel_rent_id', $rentId)
			->whereNotNull('sale_note_id')
			->pluck('sale_note_id')
			->unique()
			->values();

		$documentIds = HotelRentItem::where('hotel_rent_id', $rentId)
			->whereNotNull('document_id')
			->pluck('document_id')
			->unique()
			->values();

		$saleNotes = SaleNote::whereIn('id', $saleNoteIds)
			->get()
			->map(function ($s) use ($rentId) {
				$itemsCount = HotelRentItem::where('hotel_rent_id', $rentId)
					->where('sale_note_id', $s->id)
					->count();
				return [
					'id' => $s->id,
					'type' => 'sale_note',
					'document_type' => 'Nota de Venta',
					'series' => $s->series,
					'number' => $s->number,
					'identifier' => $s->series . '-' . $s->number,
					'total' => $s->total,
					'currency_type_id' => $s->currency_type_id,
					'created_at' => $s->created_at ? $s->created_at->format('Y-m-d H:i:s') : null,
					'items_count' => $itemsCount,
					'external_id' => $s->external_id,
				];
			});

		$documents = Document::whereIn('id', $documentIds)
			->get()
			->map(function ($d) use ($rentId) {
				$itemsCount = HotelRentItem::where('hotel_rent_id', $rentId)
					->where('document_id', $d->id)
					->count();
				return [
					'id' => $d->id,
					'type' => 'document',
					'document_type' => optional($d->document_type)->description ?? 'Comprobante',
					'series' => $d->series,
					'number' => $d->number,
					'identifier' => $d->series . '-' . $d->number,
					'total' => $d->total,
					'currency_type_id' => $d->currency_type_id,
					'created_at' => $d->created_at ? $d->created_at->format('Y-m-d H:i:s') : null,
					'items_count' => $itemsCount,
					'external_id' => $d->external_id,
				];
			});

		return response()->json([
			'success' => true,
			'data' => $saleNotes->concat($documents)->sortByDesc('created_at')->values()
		]);
	}


	private function customers()
	{
		$query = request('input');
		$search_by_barcode = (bool)request('search_by_barcode');

		$customers = Person::with('addresses')
			->whereType('customers')
			->whereIsEnabled()
			->whereIn('identity_document_type_id', [1, 4, 6])
			->orderBy('name');

		// Sin término: devolver los primeros 20 (para que aparezca lista al
		// hacer clic). Con término: filtrar.
		if (!empty($query)) {
			if ($search_by_barcode) {
				$customers = $customers->where('barcode', 'like', "%{$query}%");
			} else {
				if (is_numeric($query)) {
					$customers = $customers->where('number', 'like', "%{$query}%");
				} else {
					$customers = $customers->where('name', 'like', "%{$query}%");
				}
			}
		}

		$customers = $customers->take(20)
			->get()
			->transform(function ($row) {
				return [
					'id'                          => $row->id,
					'description'                 => $row->number . ' - ' . $row->name,
					'name'                        => $row->name,
					'number'                      => $row->number,
					'identity_document_type_id'   => $row->identity_document_type_id,
					'identity_document_type_code' => $row->identity_document_type->code,
					'addresses'                   => $row->addresses,
					'address'                     => $row->address,
					'internal_code'               => $row->internal_code,
					'barcode'					  => $row->barcode,
					'observation'                 => $row->observation
				];
			});

		return $customers;
	}

	public function tables()
	{
		$customers = $this->customers();
		$configuration = Configuration::select('affectation_igv_type_id')->first();

        $payment_method_types = PaymentMethodType::getTableCashPaymentMethodTypes();
        $payment_destinations = $this->getPaymentDestinations();

		return response()->json([
			'customers' => $customers,
			'configuration' => $configuration,
			'payment_method_types' => $payment_method_types,
			'payment_destinations' => $payment_destinations
		], 200);
	}


	/**
	 *
	 * Datos relacionados para agregar productos al rentar habitacion
	 *
	 * @return array
	 */
	public function rentProductsTables()
	{
        $payment_method_types = PaymentMethodType::getTableCashPaymentMethodTypes();
        $payment_destinations = $this->getPaymentDestinations();

		return [
			'payment_method_types' => $payment_method_types,
			'payment_destinations' => $payment_destinations
		];
	}

    public function report($start, $end, $establishment_id)
    {
		$user = auth()->user();
		$establishment = $user->establishment;
		$query = HotelRent::whereBetween('input_date', [$start, $end]);

		if ($establishment_id && $user->type === 'admin') {
			$query->where('establishment_id', $establishment_id);
			$establishment = Establishment::findOrFail($establishment_id);
		}

		if ($user->type != 'admin') {
			$query->where('establishment_id', $user->establishment_id);
		}
		
        $documents = $query->get();

        $records = collect($documents)->transform(function ($row) {

			$data_persons = collect((array) $row->data_persons)
				->map(function ($person) {
					$name = isset($person->name) ? $person->name : '';
					$number = isset($person->number) ? $person->number : '';
					return trim("{$name} {$number}", " ");
				})
				->implode('; ');

			$document_number = "";
			$document_date = "";
			$total = "";

			$document = Document::where('hotel_rent_id',$row->id)->first();

			if($document){
				$document_number = $document->series.'-'.$document->number;
				$document_date = $document->date_of_issue;
				$total = $document->total;
			}

			$sale_note = SaleNote::where('hotel_rent_id',$row->id)->first();

			if($sale_note){
				$document_number = $sale_note->series.'-'.$sale_note->number;
				$document_date = $sale_note->date_of_issue;
				$total = $sale_note->total;
			}

            return [
                'id' => $row->id,
                'customer' => $row->customer->description,
                'document_number' => $document_number,
                'document_date' => $document_date,
                'total' => $total,
                'input_date' => $row->input_date,
                'input_time' => $row->input_time,
				'output_date' => $row->output_date,
                'output_time' => $row->output_time,
                'duration' => $row->duration,
                'quantity_persons' => $row->quantity_persons,
                'category' => $row->room->category->description,
				'data_persons' => $data_persons,
            ];
        });

        $filename = "Reporte_Recepción";
		$company = Company::first();

		return (new HotelRentExport)
			->records($records)
			->company($company)
            ->establishment($establishment)
			->download($filename . Carbon::now() . '.xlsx');
		
    }

    /**
     * Actualizar las observaciones de un registro de alquiler
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function updateObservations($id, Request $request)
    {
        try {
            $rent = HotelRent::findOrFail($id);
            
            $rent->notes = $request->input('notes');
            $rent->save();

            return response()->json([
                'success' => true,
                'message' => 'Observaciones actualizadas exitosamente',
                'data' => $rent
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar las observaciones: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar de habitación un registro de alquiler.
     *
     * Flujo:
     *  1. Valida la nueva habitación + tarifa.
     *  2. Calcula los períodos ya consumidos en la habitación anterior
     *     (en función de input_date/time y la fecha del cambio) y los
     *     restantes hasta output_date/time.
     *  3. Cierra el item HAB vigente: ajusta quantity = consumidos,
     *     total = unit_price * consumidos, y actualiza la descripción.
     *  4. Clona el item HAB con la nueva habitación/tarifa: quantity =
     *     restantes, unit_price = precio nueva tarifa, total recalculado.
     *  5. Actualiza estados de las habitaciones, datos del alquiler y
     *     registra la operación en el historial (ROOM_CHANGE).
     */
    public function changeRoom($id, Request $request)
    {
        DB::connection('tenant')->beginTransaction();
        try {
            $newRoomId = (int) $request->input('new_room_id');
            $newRateId = $request->input('new_rate_id');

            if (!$newRoomId || !$newRateId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe seleccionar la nueva habitación y la tarifa.'
                ], 422);
            }

            $rent = HotelRent::with(['items', 'room'])->findOrFail($id);

            if ($rent->status === 'FINALIZADO') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede cambiar la habitación de un alquiler finalizado.'
                ], 400);
            }

            if ((int) $rent->hotel_room_id === $newRoomId) {
                return response()->json([
                    'success' => false,
                    'message' => 'La habitación seleccionada es la misma que la actual.'
                ], 400);
            }

            $oldRoom = HotelRoom::findOrFail($rent->hotel_room_id);

            $newRoom = HotelRoom::where('id', $newRoomId)
                ->where('status', 'DISPONIBLE')
                ->where('active', true)
                ->first();

            if (!$newRoom) {
                return response()->json([
                    'success' => false,
                    'message' => 'La habitación seleccionada no está disponible.'
                ], 400);
            }

            // Precio de la tarifa para la nueva habitación
            $newRoomRate = HotelRoomRate::where('hotel_room_id', $newRoomId)
                ->where('hotel_rate_id', $newRateId)
                ->first();

            if (!$newRoomRate) {
                return response()->json([
                    'success' => false,
                    'message' => 'La tarifa seleccionada no está configurada para la nueva habitación.'
                ], 400);
            }

            $newUnitPrice = (float) $newRoomRate->price;

            $inputAt  = Carbon::parse("{$rent->input_date} {$rent->input_time}");
            $outputAt = Carbon::parse("{$rent->output_date} {$rent->output_time}");

            // Fecha/hora del cambio (default: ahora). Si llega fuera del rango
            // de la estadía (reloj del cliente desfasado, sobre-estadía, etc.)
            // se acota silenciosamente para que el split siempre sea válido.
            $changeDate = $request->input('change_date', Carbon::now()->format('Y-m-d'));
            $changeTime = $request->input('change_time', Carbon::now()->format('H:i'));
            $changeAt   = Carbon::parse("{$changeDate} {$changeTime}");

            if ($changeAt->lte($inputAt)) {
                $changeAt = $inputAt->copy()->addMinute();
            }
            if ($changeAt->gte($outputAt)) {
                $changeAt = $outputAt->copy()->subMinute();
            }

            // Cálculo de períodos consumidos / restantes según rental_period_type
            $period = $rent->rental_period_type ?: 'day';
            [$consumed, $remaining, $unitLabel] = $this->calculateRoomChangeSplit(
                $period,
                $inputAt,
                $changeAt,
                (int) $rent->duration
            );

            // Snapshot del estado actual para el historial
            $oldHotelRateId = $rent->hotel_rate_id;

            // Item HAB vigente (no facturado aún)
            $oldItem = $rent->items()
                ->where('type', 'HAB')
                ->whereNull('sale_note_id')
                ->whereNull('document_id')
                ->orderByDesc('id')
                ->first();

            if (!$oldItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró un item de habitación abierto para cerrar.'
                ], 400);
            }

            // Datos base del item HAB vigente
            $oldItemJson    = is_object($oldItem->item) ? (array) $oldItem->item : ($oldItem->item ?: []);
            $oldUnitColumn  = (float) $oldItem->unit_price;
            $oldUnitJson    = (float) ($oldItemJson['unit_price'] ?? $oldItemJson['unit_price_value'] ?? 0);
            // La columna unit_price viene como "0.0000" (string truthy) en items
            // creados antes de esta refactor — preferir JSON si la columna es 0.
            $oldUnitPrice   = $oldUnitColumn > 0 ? $oldUnitColumn : $oldUnitJson;

            // Item de la nueva habitación (para reescribir identidad en el JSON)
            $newItemRecord = Item::find($newRoom->item_id);

            $newTotal       = round($newUnitPrice * $remaining, 4);
            $newDescription = sprintf(
                'Estadía en %s - %d %s (%s → %s)',
                $newRoom->name,
                $remaining,
                $unitLabel,
                ($consumed === 0 ? $inputAt : $changeAt)->format('d/m/Y H:i'),
                $outputAt->format('d/m/Y H:i')
            );

            if ($consumed === 0) {
                // Cambio dentro del mismo período (p. ej. minutos después del check-in):
                // no se cobra nada por la habitación anterior, se reemplaza el item
                // vigente con los datos de la nueva habitación/tarifa.
                $replacedJson = $this->rewriteHotelItemForRoom(
                    $oldItemJson,
                    $newItemRecord,
                    $newRoom,
                    $newDescription,
                    $newUnitPrice,
                    $remaining,
                    $newTotal
                );

                $oldItem->item_id     = $newRoom->item_id;
                $oldItem->item        = $replacedJson;
                $oldItem->quantity    = $remaining;
                $oldItem->unit_price  = $newUnitPrice;
                $oldItem->total       = $newTotal;
                $oldItem->description = $newDescription;
                $oldItem->save();

                $newItem    = $oldItem;
                $oldTotalNew = 0.0;
            } else {
                // Split: cierra el item antiguo por los períodos consumidos y crea
                // un nuevo item HAB para los restantes con la nueva habitación/tarifa.
                $oldTotalNew    = round($oldUnitPrice * $consumed, 4);
                $oldDescription = sprintf(
                    'Estadía en %s - %d %s (%s → %s)',
                    $oldRoom->name,
                    $consumed,
                    $unitLabel,
                    $inputAt->format('d/m/Y H:i'),
                    $changeAt->format('d/m/Y H:i')
                );

                $oldItemJson = $this->rewriteHotelItemDescription(
                    $oldItemJson,
                    $oldDescription,
                    $oldUnitPrice,
                    $consumed,
                    $oldTotalNew
                );

                $oldItem->item        = $oldItemJson;
                $oldItem->quantity    = $consumed;
                $oldItem->unit_price  = $oldUnitPrice;
                $oldItem->total       = $oldTotalNew;
                $oldItem->description = $oldDescription;
                $oldItem->save();

                $newItemJson = $this->rewriteHotelItemForRoom(
                    $oldItemJson,
                    $newItemRecord,
                    $newRoom,
                    $newDescription,
                    $newUnitPrice,
                    $remaining,
                    $newTotal
                );

                $newItem = HotelRentItem::create([
                    'type'                => 'HAB',
                    'hotel_rent_id'       => $rent->id,
                    'item_id'             => $newRoom->item_id,
                    'item'                => $newItemJson,
                    'payment_status'      => 'DEBT',
                    'hotel_rent_order_id' => null,
                    'quantity'            => $remaining,
                    'unit_price'          => $newUnitPrice,
                    'total'               => $newTotal,
                    'description'         => $newDescription,
                ]);
            }

            // 3) Actualizar alquiler
            $rent->hotel_room_id = $newRoom->id;
            $rent->hotel_rate_id = $newRateId;
            $rent->rental_price  = $newUnitPrice;
            $rent->save();

            // 4) Estados de habitaciones — la antigua pasa a limpieza para preparar
            $oldRoom->status = 'LIMPIEZA';
            $oldRoom->save();

            $newRoom->status = 'OCUPADO';
            $newRoom->save();

            // 5) Historial de cambios
            $priceDifference = round(($newUnitPrice - $oldUnitPrice) * $remaining, 4);

            HotelRentChange::create([
                'hotel_rent_id'    => $rent->id,
                'change_type'      => 'ROOM_CHANGE',
                'old_values'       => [
                    'hotel_room_id'  => $oldRoom->id,
                    'room_name'      => $oldRoom->name,
                    'hotel_rate_id'  => $oldHotelRateId,
                    'unit_price'     => $oldUnitPrice,
                    'item_id'        => $oldItem->id,
                ],
                'new_values'       => [
                    'hotel_room_id'  => $newRoom->id,
                    'room_name'      => $newRoom->name,
                    'hotel_rate_id'  => (int) $newRateId,
                    'unit_price'     => $newUnitPrice,
                    'item_id'        => $newItem->id,
                    'consumed'       => $consumed,
                    'remaining'      => $remaining,
                    'unit'           => $unitLabel,
                    'changed_at'     => $changeAt->toDateTimeString(),
                ],
                'notes'            => "Cambio de habitación: {$oldRoom->name} → {$newRoom->name}",
                'price_difference' => $priceDifference,
                'user_id'          => auth()->id(),
            ]);

            DB::connection('tenant')->commit();

            $rent->load(['room', 'customer', 'items']);

            return response()->json([
                'success' => true,
                'message' => 'Habitación cambiada exitosamente.',
                'data'    => [
                    'rent'             => $rent,
                    'old_room'         => $oldRoom->name,
                    'new_room'         => $newRoom->name,
                    'consumed'         => $consumed,
                    'remaining'        => $remaining,
                    'unit'             => $unitLabel,
                    'old_item_id'      => $oldItem->id,
                    'new_item_id'      => $newItem->id,
                    'price_difference' => $priceDifference,
                ],
            ], 200);

        } catch (\Throwable $th) {
            DB::connection('tenant')->rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar de habitación: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Calcula períodos consumidos y restantes para un cambio de habitación.
     *
     * Una "noche" (o "hora" / "mes") solo cuenta cuando el período completo
     * ha transcurrido — por eso `floor`, no `ceil`. Si el cambio ocurre
     * dentro del primer período (p. ej. minutos después del check-in),
     * `consumed = 0` y arriba se hace reemplazo directo sin split.
     *
     * @return array{0:int,1:int,2:string} [consumidos, restantes, etiqueta]
     */
    private function calculateRoomChangeSplit($period, Carbon $inputAt, Carbon $changeAt, $duration)
    {
        switch ($period) {
            case 'hour':
                $unit    = 'hora(s)';
                $elapsed = $inputAt->floatDiffInHours($changeAt);
                break;
            case 'month':
                $unit    = 'mes(es)';
                $elapsed = $inputAt->floatDiffInMonths($changeAt);
                break;
            case 'day':
            default:
                $unit    = 'noche(s)';
                $elapsed = $inputAt->floatDiffInDays($changeAt);
                break;
        }

        $duration  = max(1, (int) $duration);
        // consumed acotado a [0, duration-1] para que siempre quede al menos 1 para la nueva habitación
        $consumed  = max(0, min((int) floor($elapsed), $duration - 1));
        $remaining = max(1, $duration - $consumed);

        return [$consumed, $remaining, $unit];
    }

    /**
     * Reescribe el JSON del item HAB con la identidad de una habitación.
     *
     * Sobrescribe todas las claves que la app usa para mostrar/imprimir
     * el item (nombre, descripción, internal_id, item_id, totales) — tanto
     * en el nivel exterior del payload como en el sub-objeto `item` interno
     * que produce calculateRowItem.
     */
    private function rewriteHotelItemForRoom(
        array $base,
        ?Item $newItem,
        HotelRoom $newRoom,
        $description,
        $unitPrice,
        $quantity,
        $total
    ) {
        $itemId     = $newItem ? (int) $newItem->id : (int) $newRoom->item_id;
        $internalId = $newItem ? $newItem->internal_id : ($base['internal_id'] ?? null);
        $name       = $newItem ? $newItem->name : ($base['name'] ?? $newRoom->name);

        // Sub-objeto `item` interno (lo que las vistas leen como it.item.name / it.item.description)
        $innerCurrent = $base['item'] ?? [];
        if (is_object($innerCurrent)) {
            $innerCurrent = (array) $innerCurrent;
        }
        $innerNew = array_merge(is_array($innerCurrent) ? $innerCurrent : [], [
            'id'               => $itemId,
            'item_id'          => $itemId,
            'internal_id'      => $internalId,
            'name'             => $name,
            'description'      => $description,
            'full_description' => $description,
            'unit_price'       => $unitPrice,
        ]);

        // Nivel exterior — claves de identidad y de precio/cantidad/total
        return array_merge($base, [
            'id'                     => $itemId,
            'item_id'                => $itemId,
            'internal_id'            => $internalId,
            'name'                   => $name,
            'description'            => $description,
            'full_description'       => $description,
            'name_product_pdf'       => $description,
            'quantity'               => $quantity,
            'unit_value'             => $unitPrice,
            'unit_price'             => $unitPrice,
            'unit_price_value'       => $unitPrice,
            'input_unit_price_value' => $unitPrice,
            'total'                  => $total,
            'item'                   => $innerNew,
        ]);
    }

    /**
     * Reescribe solo la descripción, cantidad y total del item HAB
     * (preservando la identidad original de la habitación que se cierra).
     */
    private function rewriteHotelItemDescription(array $base, $description, $unitPrice, $quantity, $total)
    {
        $innerCurrent = $base['item'] ?? [];
        if (is_object($innerCurrent)) {
            $innerCurrent = (array) $innerCurrent;
        }
        $innerNew = array_merge(is_array($innerCurrent) ? $innerCurrent : [], [
            'description'      => $description,
            'full_description' => $description,
            'unit_price'       => $unitPrice,
        ]);

        return array_merge($base, [
            'description'            => $description,
            'full_description'       => $description,
            'name_product_pdf'       => $description,
            'quantity'               => $quantity,
            'unit_value'             => $unitPrice,
            'unit_price'             => $unitPrice,
            'unit_price_value'       => $unitPrice,
            'input_unit_price_value' => $unitPrice,
            'total'                  => $total,
            'item'                   => $innerNew,
        ]);
    }

    /**
     * Extender estadía de un registro de alquiler
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function extendStay($id, Request $request)
    {
        DB::connection('tenant')->beginTransaction();
        try {
            $rent = HotelRent::findOrFail($id);
            
            // Validar que el alquiler esté activo
            if ($rent->status !== 'ACTIVE') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede extender la estadía de un registro que no está activo'
                ], 400);
            }
            
            $days = $request->input('days', 1);
            $newOutputDate = $request->input('new_output_date');
            $newOutputTime = $request->input('new_output_time');
            
            // Validar que los días sea un número positivo
            if (!is_numeric($days) || $days <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'El número de días debe ser un valor positivo'
                ], 400);
            }
            
            // Calcular nueva fecha de salida si no se proporciona
            if (!$newOutputDate) {
                $currentOutputDate = Carbon::parse($rent->output_date . ' ' . $rent->output_time);
                $newOutputDate = $currentOutputDate->addDays($days)->format('Y-m-d');
                $newOutputTime = $currentOutputDate->format('H:i');
            }
            
            // Validar que la nueva fecha sea posterior a la actual
            $currentOutput = Carbon::parse($rent->output_date . ' ' . $rent->output_time);
            $newOutput = Carbon::parse($newOutputDate . ' ' . $newOutputTime);
            
            if ($newOutput <= $currentOutput) {
                return response()->json([
                    'success' => false,
                    'message' => 'La nueva fecha de salida debe ser posterior a la actual'
                ], 400);
            }
            
            // Actualizar duración y fechas del alquiler
            $rent->duration += $days;
            $rent->output_date = $newOutputDate;
            $rent->output_time = $newOutputTime;
            $rent->save();
            
            // Crear un nuevo item de cargo por la extensión
            $room = $rent->room;
            $rate = $room->rates()->where('id', $rent->hotel_rate_id)->first();
            
            if ($rate) {
                $description = "Extensión de estadía - {$days} día(s) adicional(es)";
                $extensionTotal = $rate->rate_price * $days;

                $itemData = [
                    'type' => 'HAB',
                    'hotel_rent_id' => $rent->id,
                    'item_id' => $room->item_id,
                    'item' => [
                        'description' => $description,
                        'unit_price' => $rate->rate_price,
                        'quantity' => $days,
                        'total' => $extensionTotal,
                        'is_extension' => true,
                        'item' => [
                            'description' => $description,
                            'unit_price' => $rate->rate_price,
                        ],
                    ],
                    'quantity' => $days,
                    'unit_price' => $rate->rate_price,
                    'total' => $extensionTotal,
                    'description' => $description,
                    'payment_status' => 'DEBT',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                $extensionItem = HotelRentItem::create($itemData);

                // Si se incluye pago al extender, registrarlo y marcar pagado
                // cuando cubra el total de la extensión.
                if ($request->boolean('include_payment') && (float) $request->input('payment_amount', 0) > 0) {
                    $paymentMethodId = $this->getPaymentMethodId($request->input('payment_method'));
                    if (!$paymentMethodId) {
                        throw new \Exception('No se encontró un método de pago válido para registrar la extensión.');
                    }

                    $paymentAmount = (float) $request->input('payment_amount', 0);
                    HotelRentItemPayment::create([
                        'hotel_rent_item_id' => $extensionItem->id,
                        'date_of_payment' => date('Y-m-d'),
                        'payment_method_type_id' => $paymentMethodId,
                        'reference' => $request->input('payment_reference'),
                        'payment' => $paymentAmount,
                    ]);

                    if ($paymentAmount >= $extensionTotal) {
                        $extensionItem->payment_status = 'PAID';
                        $extensionItem->save();
                    }
                }

                // Aplicar adelantos existentes a items con deuda.
                $this->applyAdvanceCreditToDebtItems($rent);
            }
            
            DB::connection('tenant')->commit();
            
            // Cargar el alquiler actualizado con sus relaciones
            $rent->load(['room', 'customer', 'items']);
            
            return response()->json([
                'success' => true,
                'message' => 'Estadía extendida correctamente',
                'rent' => $rent
            ], 200);
            
        } catch (\Throwable $th) {
            DB::connection('tenant')->rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al extender la estadía: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Guardar pago parcial
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function savePayment($id, Request $request)
    {
        DB::connection('tenant')->beginTransaction();
        try {
            $rent = HotelRent::findOrFail($id);
            
            $amount = $request->input('amount', 0);
            $method = $request->input('method', 'cash');
            $reference = $request->input('reference', '');
            $received = $request->input('received', 0);
            $change = $request->input('change', 0);
            $paymentId = $request->input('payment_id'); // Para edición

            // Método de pago: priorizar el id real del sistema enviado desde la
            // vista (se sincroniza automáticamente con los métodos configurados).
            // Se mantiene el mapeo legacy por compatibilidad si no llega el id.
            $paymentMethodTypeId = $request->input('payment_method_type_id');
            if (empty($paymentMethodTypeId)) {
                $paymentMethodTypeId = $this->getPaymentMethodTypeId($method);
            }

            // Destino del pago (caja general o cuenta bancaria) para registrar el
            // movimiento en finanzas/caja como en el resto del sistema.
            $paymentDestinationId = $request->input('payment_destination_id');
            
            // Determinar si es edición o creación
            $isEditing = !empty($paymentId);
            
            // Validar monto (permitir negativos para devoluciones)
            if ($amount == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'El monto no puede ser 0'
                ], 400);
            }
            
            // Si es edición, buscar el pago existente y actualizarlo
            if ($isEditing) {
                $payment = HotelRentItemPayment::findOrFail($paymentId);
                
                // Actualizar datos del pago
                $payment->payment = $amount;
                $payment->payment_method_type_id = $paymentMethodTypeId;
                $payment->reference = $reference;
                $payment->change = $change;
                $payment->save();

                // Re-sincronizar el movimiento de caja con el nuevo método/destino.
                $this->registerRentPaymentInCash($payment, $paymentDestinationId);

                // No crear nuevos items, solo actualizar el pago existente
                $debtItem = null; // Para evitar lógica de creación abajo
                
                \Log::info('Pago actualizado. ID: ' . $payment->id . ', Nuevo monto: ' . $amount);
                
            } else {
                // Lógica original para nuevos pagos
                // Determinar si es una devolución (pago negativo)
                $isRefund = $amount < 0;
                
                if ($isRefund) {
                    // Para devoluciones / vuelto: buscar un item al cual asociar el
                    // pago negativo. Se prioriza el último item pagado; si no existe,
                    // se usa cualquier item base disponible del alquiler.
                    $debtItem = HotelRentItem::where('hotel_rent_id', $rent->id)
                        ->where('payment_status', 'PAID')
                        ->where('type', '!=', 'PAY')
                        ->orderBy('id', 'desc')
                        ->first();

                    $refundItemId = $debtItem
                        ? $debtItem->id
                        : $this->resolvePaymentItemId($rent);

                    if (!$refundItemId) {
                        DB::connection('tenant')->rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'No se pudo registrar la devolución porque no se encontró un item para asociar el vuelto.'
                        ], 422);
                    }

                    // El destino del vuelto sale de caja (egreso). Si no llega el
                    // destino desde la vista, se asume caja general.
                    if (empty($paymentDestinationId)) {
                        $paymentDestinationId = 'cash';
                    }

                    // Registrar la devolución como un pago negativo asociado al item.
                    $refundPayment = new HotelRentItemPayment();
                    $refundPayment->hotel_rent_item_id = $refundItemId;
                    $refundPayment->payment = $amount; // negativo
                    $refundPayment->payment_method_type_id = $paymentMethodTypeId;
                    $refundPayment->reference = $reference;
                    $refundPayment->change = 0;
                    $refundPayment->date_of_payment = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                    $refundPayment->save();

                    // Registrar el egreso en caja/finanzas.
                    $this->registerRentPaymentInCash($refundPayment, $paymentDestinationId);

                    DB::connection('tenant')->commit();

                    return response()->json([
                        'success'      => true,
                        'message'      => 'Vuelto/devolución registrado correctamente.',
                        'payment_id'   => $refundPayment->id,
                        'total_amount' => $amount,
                        'is_refund'    => true,
                    ], 200);
                } else {
                    // Para pagos normales: buscar todos los items con deuda
                    $debtItems = HotelRentItem::where('hotel_rent_id', $rent->id)
                        ->where('payment_status', 'DEBT')
                        ->get();

                    // Si no hay deuda, el pago se trata como ADELANTO/crédito:
                    // se registra como un pseudo-item PAY y queda disponible
                    // para futuros cargos (productos/extensiones).
                    if ($debtItems->isEmpty()) {
                        $paymentItemId = $this->resolvePaymentItemId($rent);
                        if (!$paymentItemId) {
                            DB::connection('tenant')->rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => 'No se pudo registrar el adelanto porque no se encontró un item base para asociar el pago.'
                            ], 422);
                        }

                        $creditItem = new HotelRentItem();
                        $creditItem->hotel_rent_id = $rent->id;
                        $creditItem->item_id = $paymentItemId;
                        $creditItem->type = 'PAY';
                        $creditItem->payment_status = 'PAID';
                        $creditItem->item = (object) [
                            'description' => 'Adelanto de pago',
                            'total'       => abs($amount),
                        ];
                        $creditItem->save();

                        $creditPayment = new HotelRentItemPayment();
                        $creditPayment->hotel_rent_item_id = $creditItem->id;
                        $creditPayment->payment = $amount;
                        $creditPayment->payment_method_type_id = $paymentMethodTypeId;
                        $creditPayment->reference = $reference;
                        $creditPayment->change = 0;
                        $creditPayment->date_of_payment = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                        $creditPayment->save();

                        // Registrar el adelanto en caja/finanzas.
                        $this->registerRentPaymentInCash($creditPayment, $paymentDestinationId);

                        DB::connection('tenant')->commit();

                        return response()->json([
                            'success'        => true,
                            'message'        => 'Adelanto registrado correctamente. Quedará disponible para futuros cargos.',
                            'payment_id'     => $creditPayment->id,
                            'total_amount'   => $amount,
                            'is_advance'     => true,
                        ], 200);
                    }
                }
            }
            
            // Solo ejecutar lógica de creación si no es edición
            if (!$isEditing) {
                $remainingAmount = $amount;
                $paymentItems = [];
                
                // Distribuir el pago entre todos los items con deuda
                foreach ($debtItems as $debtItem) {
                    if ($remainingAmount <= 0) break;
                    
                    // Obtener el total del item
                    $itemData = $debtItem->item;
                    $itemTotal = isset($itemData->total) ? $itemData->total : 0;
                    
                    // Calcular total ya pagado para este item
                    $totalPaid = HotelRentItemPayment::where('hotel_rent_item_id', $debtItem->id)
                        ->sum('payment');
                    
                    $remainingDebt = $itemTotal - $totalPaid;
                    
                    if ($remainingDebt <= 0) continue; // Este item ya está pagado
                    
                    // Determinar cuánto aplicar a este item
                    $paymentAmount = min($remainingAmount, $remainingDebt);
                    
                    // Crear registro de pago para este item
                    $payment = new HotelRentItemPayment();
                    $payment->hotel_rent_item_id = $debtItem->id;
                    $payment->payment = $paymentAmount;
                    $payment->payment_method_type_id = $paymentMethodTypeId;
                    $payment->reference = $reference;
                    $payment->change = 0; // El cambio se maneja a nivel general
                    $payment->date_of_payment = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                    $payment->save();

                    // Registrar el pago en caja/finanzas.
                    $this->registerRentPaymentInCash($payment, $paymentDestinationId);

                    \Log::info("Pago creado para item {$debtItem->id}: {$paymentAmount}");
                    
                    // Actualizar estado del item si está completamente pagado
                    $newTotalPaid = $totalPaid + $paymentAmount;
                    if ($newTotalPaid >= $itemTotal && $itemTotal > 0) {
                        $debtItem->payment_status = 'PAID';
                        $debtItem->save();
                        \Log::info("Item {$debtItem->id} marcado como PAGADO");
                    }
                    
                    // Reducir el monto restante
                    $remainingAmount -= $paymentAmount;
                    
                    // Guardar referencia al primer item para la respuesta
                    if (empty($paymentItems)) {
                        $paymentItems[] = [
                            'item_id' => $debtItem->id,
                            'payment_id' => $payment->id,
                            'item_total' => $itemTotal,
                            'total_paid' => $newTotalPaid,
                            'remaining_debt' => max(0, $itemTotal - $newTotalPaid)
                        ];
                    }
                }
                
                // Crear un item de pago general si sobra dinero
                if ($remainingAmount > 0) {
                    $paymentItemId = $this->resolvePaymentItemId($rent, $debtItems->first());
                    if (!$paymentItemId) {
                        DB::connection('tenant')->rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'No se pudo guardar el excedente porque no se encontró un item base para asociarlo.'
                        ], 422);
                    }

                    $paymentItem = new HotelRentItem();
                    $paymentItem->hotel_rent_id = $rent->id;
                    $paymentItem->item_id = $paymentItemId;
                    $paymentItem->type = 'PAY'; // Tipo de pago
                    $paymentItem->payment_status = 'PAID';
                    $paymentItem->item = (object)[
                        'description' => 'Pago excedente',
                        'total' => $remainingAmount
                    ];
                    $paymentItem->save();
                }
            }
            
            DB::connection('tenant')->commit();
            
            return response()->json([
                'success' => true,
                'message' => $isEditing ? 'Pago actualizado correctamente' : 'Pago guardado correctamente',
                'payment_id' => $isEditing ? $paymentId : ($payment->id ?? null),
                'payment_items' => $paymentItems ?? [],
                'total_amount' => $amount,
                'remaining_amount' => $remainingAmount ?? 0
            ], 200);
            
        } catch (\Throwable $th) {
            DB::connection('tenant')->rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar pago: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Revertir pago
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function reversePayment($id, Request $request)
    {
        DB::connection('tenant')->beginTransaction();
        try {
            $rent = HotelRent::findOrFail($id);
            $paymentId = $request->input('payment_id');
            
            // Buscar el pago
            Log::info('Buscando pago para revertir. Rent ID: ' . $rent->id . ', Payment ID: ' . $paymentId);
            
            // Verificar items del rent para depuración
            $rentItems = HotelRentItem::where('hotel_rent_id', $rent->id)->get();
            Log::info('Items del Rent ID ' . $rent->id . ': ' . json_encode($rentItems->pluck('id')->toArray()));
            
            
            
            $payment = HotelRentItemPayment::where('id', $paymentId)
                ->first();
                
            
            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró el pago (ID: ' . $paymentId . '). Verifique que el pago exista e intente nuevamente.',
                    'debug' => [
                        'success' => false,
                        'message' => 'No hay items pagados para aplicar esta devolución'
                    ]
                ], 400);
            }
            if($payment){
                // Obtener el item asociado al pago
                $associatedItem = HotelRentItem::find($payment->hotel_rent_item_id);
                
                // Eliminar el pago
                $payment->delete();
                
                // Resetear el estado del item a PENDING
                if($associatedItem){
                    $associatedItem->payment_status = 'PENDING';
                    $associatedItem->save();
                }
            }
            
            DB::connection('tenant')->commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Pago revertido correctamente'
            ], 200);
            
        } catch (\Throwable $th) {
            DB::connection('tenant')->rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al revertir pago: ' . $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtiene un item_id válido para registrar movimientos tipo PAY.
     *
     * @param  HotelRent $rent
     * @param  HotelRentItem|null $referenceItem
     * @return int|null
     */
    private function resolvePaymentItemId(HotelRent $rent, ?HotelRentItem $referenceItem = null)
    {
        if ($referenceItem && !empty($referenceItem->item_id)) {
            return (int) $referenceItem->item_id;
        }

        $habItem = HotelRentItem::where('hotel_rent_id', $rent->id)
            ->where('type', 'HAB')
            ->whereNotNull('item_id')
            ->orderByDesc('id')
            ->first();

        if ($habItem && !empty($habItem->item_id)) {
            return (int) $habItem->item_id;
        }

        if (!$rent->relationLoaded('room')) {
            $rent->load('room');
        }

        if ($rent->room && !empty($rent->room->item_id)) {
            return (int) $rent->room->item_id;
        }

        $fallbackItem = HotelRentItem::where('hotel_rent_id', $rent->id)
            ->whereNotNull('item_id')
            ->orderByDesc('id')
            ->first();

        return $fallbackItem ? (int) $fallbackItem->item_id : null;
    }

    /**
     * Obtener ID del método de pago
     * @param string $method
     * @return int
     */
    private function getPaymentMethodTypeId($method)
    {
        $methodMap = [
            'cash' => '01',         // Efectivo
            'credit_card' => '02',  // Tarjeta de crédito
            'debit_card' => '03',   // Tarjeta de débito
            'transfer' => '04',     // Transferencia
            'yape_plin' => '05'     // Yape/Plin
        ];
        
        $methodCode = $methodMap[$method] ?? '01';
        
        // Buscar el payment method type real en la base de datos
        $paymentMethod = PaymentMethodType::where('id', $methodCode)->first();
        
        return $paymentMethod ? $paymentMethod->id : '01';
    }

    /**
     * Obtener label del método de pago
     * @param string $method
     * @return string
     */
    private function getPaymentMethodLabel($method)
    {
        $methodMap = [
            'cash' => '01',         // Efectivo
            'credit_card' => '02',  // Tarjeta de crédito
            'debit_card' => '03',   // Tarjeta de débito
            'transfer' => '04',     // Transferencia
            'yape_plin' => '05'     // Yape/Plin
        ];
        
        $methodCode = $methodMap[$method] ?? '01';
        
        // Buscar el payment method type real en la base de datos
        $paymentMethod = PaymentMethodType::where('id', $methodCode)->first();
        
        return $paymentMethod ? $paymentMethod->description : 'Efectivo';
    }

}
