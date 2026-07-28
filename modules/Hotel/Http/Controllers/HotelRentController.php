<?php

namespace Modules\Hotel\Http\Controllers;

use App\Models\Tenant\Person;
use App\Models\Tenant\Series;
use App\Services\SeriesResolver;
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
use App\Http\Controllers\Tenant\SaleNoteController;

class HotelRentController extends Controller
{
    use FinanceTrait;

	public function rent($roomId)
	{
		$room = HotelRoom::with('category', 'rates.rate')
			->findOrFail($roomId);

		$affectation_igv_types = AffectationIgvType::whereActive()->get();
		$series = app(SeriesResolver::class)->applyContext(Series::where('establishment_id',  auth()->user()->establishment_id))->get();

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
			$isPaidPayment    = $requestedPaymentStatus === 'PAID';

			if ($isReservation || $isFromCalendar || ($hasGetParams && $isIframe) || $isFromCalendarSource) {
				$request->merge(['is_reserve' => true]);
				// Las reservas empiezan como pendientes, salvo que el usuario:
				//  - registre un adelanto: la reserva queda como DEBT (pago parcial)
				//    y el adelanto se registra más abajo, o
				//  - la marque como PAGADO: la reserva queda como PAID y se registra
				//    el pago total más abajo (antes se sobrescribía a PENDING y la
				//    reserva pagada aparecía como no pagada).
				if (!$isAdvancePayment && !$isPaidPayment) {
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
			
			$rentData = $request->only('customer_id', 'customer', 'notes', 'license_plate', 'travel_reason', 'reservation_origin', 'adults', 'children', 'towels', 'hotel_room_id', 'hotel_rate_id', 'duration', 'quantity_persons', 'payment_status', 'output_date', 'output_time', 'input_date', 'input_time','data_persons','establishment_id','is_reserve');
			$rentData['rental_date_time'] = $rentalDateTime;
			$rentData['rental_price'] = $rentalPrice;
			$rentData['rental_period_type'] = $rentalPeriodType;
			
			\Log::info('Rent data before save: ' . json_encode($rentData));
			
			// Check-in que proviene de una reserva: en lugar de crear un nuevo
			// HotelRent (lo que dejaba la reserva original FINALIZADA y la renta
			// como DOS registros separados en calendario/reportes), reutilizamos
			// la propia reserva y la convertimos en renta. Así queda UN SOLO
			// registro que conserva su id, su historial y sus adelantos.
			$rent                  = null;
			$convertedReservation  = false;
			$oldReservationItemIds = collect();
			if ($isCheckinFromReservation && $sourceReservationId) {
				$existingReservation = HotelRent::find($sourceReservationId);
				if ($existingReservation && $existingReservation->is_reserve) {
					// Ítems HAB que la reserva ya tenía: más abajo se crea un
					// ítem HAB nuevo para la renta, así que guardamos los viejos
					// para trasladarles los pagos (adelantos) y luego eliminarlos.
					$oldReservationItemIds = HotelRentItem::where('hotel_rent_id', $existingReservation->id)
						->where('type', 'HAB')
						->pluck('id');
					// Las órdenes previas de la reserva se eliminan para no
					// duplicarlas con la orden nueva de la renta. Pero los ítems HAB
					// de la reserva todavía las apuntan vía hotel_rent_order_id; la
					// FK impide borrar las órdenes mientras algún ítem las referencie
					// (los ítems viejos se eliminan más abajo, no aquí). Por eso
					// primero desvinculamos los ítems de esas órdenes.
					$oldOrderIds = HotelRentOrder::where('hotel_rent_id', $existingReservation->id)->pluck('id');
					if ($oldOrderIds->isNotEmpty()) {
						HotelRentItem::whereIn('hotel_rent_order_id', $oldOrderIds)
							->update(['hotel_rent_order_id' => null]);
						HotelRentOrder::whereIn('id', $oldOrderIds)->delete();
					}

					$existingReservation->fill($rentData);
					$existingReservation->is_reserve         = false;
					// 'status' no admite NULL (default 'INICIADO'); una renta activa
					// usa ese mismo valor, así que lo fijamos explícitamente al
					// convertir la reserva en renta.
					$existingReservation->status             = 'INICIADO';
					$existingReservation->rental_date_time   = $rentalDateTime;
					$existingReservation->rental_price       = $rentalPrice;
					$existingReservation->rental_period_type = $rentalPeriodType;
					$existingReservation->save();

					$rent                 = $existingReservation;
					$convertedReservation = true;
					\Log::info('Reserva convertida en renta (mismo registro)', ['rent_id' => $rent->id]);
				}
			}

			if (!$rent) {
				$rent = HotelRent::create($rentData);
			}

			\Log::info('Rent created with is_reserve: ' . $rent->is_reserve);

			// Solo cambiar estado a OCUPADO si no es una reserva
			if (!$rent->is_reserve) {
				$room->status = 'OCUPADO';
				$room->save();
			} else {
				// Para reservas, mantener la habitación como disponible
				\Log::info('Reserva creada, manteniendo habitación como DISPONIBLE');
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

			// Check-in de una reserva con adelanto: trasladar los pagos (adelantos)
			// que ya estaban registrados en la reserva original al nuevo item ANTES
			// de registrar el pago, para que cuenten como abonado y no se dupliquen
			// ni dejen saldo. Se reasignan (no se duplican).
			if ($isCheckinFromReservation && $sourceReservationId) {
				// Al convertir la reserva en renta usamos los ítems HAB viejos
				// capturados antes de crear el nuevo (si no, la consulta por
				// hotel_rent_id incluiría también el ítem recién creado).
				$reservationItemIds = $convertedReservation
					? $oldReservationItemIds
					: HotelRentItem::where('hotel_rent_id', $sourceReservationId)->pluck('id');
				if ($reservationItemIds->isNotEmpty()) {
					HotelRentItemPayment::whereIn('hotel_rent_item_id', $reservationItemIds)
						->update(['hotel_rent_item_id' => $item->id]);
				}
				// Reserva reutilizada: eliminar los ítems HAB originales (ya sin
				// pagos) para no dejar dos ítems de habitación en la misma renta.
				if ($convertedReservation && $reservationItemIds->isNotEmpty()) {
					HotelRentItem::whereIn('id', $reservationItemIds)->delete();
				}
			}

			// Registrar el pago.
			if ($isAdvance) {
				// ADVANCE = pago parcial: se registra exactamente el monto indicado
				// por el usuario; el resto queda como deuda.
				if ($request->rent_payment && ($request->rent_payment['payment'] ?? 0) > 0) {
					$advancePayment = HotelRentItemPayment::create([
						'hotel_rent_item_id'     => $item->id,
						'date_of_payment'        => date('Y-m-d H:i:s'),
						'payment_method_type_id' => $request->rent_payment['payment_method_type_id'],
						'reference'              => $request->rent_payment['reference'] ?? null,
						'payment'                => $request->rent_payment['payment'],
					]);

					$this->linkRentPaymentToOpenCash($advancePayment, $request->rent_payment['payment_destination_id'] ?? null);
				}
			} elseif ($request->payment_status === 'PAID') {
				// PAGADO: el pago debe cubrir el TOTAL del item. El front enviaba
				// sólo el saldo (total - adelanto), por lo que en estadías de
				// varios días quedaba un saldo "no pagado" en el checkout. Se toma
				// el total real del item (rate x duración) y se descuenta lo ya
				// abonado (adelantos trasladados de la reserva) para registrar el
				// saldo completo y que la deuda quede en 0.
				$itemTotal   = (float) ($product['total'] ?? ($request->rent_payment['payment'] ?? 0));
				$alreadyPaid = (float) $item->payments()->sum('payment');
				$remaining   = round($itemTotal - $alreadyPaid, 2);
				if ($remaining > 0) {
					$paidPayment = $item->payments()->create([
						'date_of_payment'        => date('Y-m-d H:i:s'),
						'payment_method_type_id' => $request->rent_payment['payment_method_type_id'] ?? null,
						'reference'              => $request->rent_payment['reference'] ?? null,
						'payment'                => $remaining,
					]);

					$this->linkRentPaymentToOpenCash($paidPayment, $request->rent_payment['payment_destination_id'] ?? null);
				}
			}

			// Si el adelanto cubre el total del item, marcarlo como pagado.
			if ($isCheckinFromReservation && $sourceReservationId) {
				$this->applyAdvanceCreditToDebtItems($rent);
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

	/**
	 *
	 * Vincular a la caja abierta un pago de hotel registrado en flujos que no
	 * envían un destino explícito (adelantos al crear/editar la renta, pagos de
	 * productos, extensiones). A diferencia de la pantalla de pago, NO bloquea la
	 * operación si la caja está cerrada: simplemente no se vincula a caja.
	 *
	 * Crea un global_payment con destino = caja para que el pago aparezca en el
	 * cierre y reportes de caja chica como el resto de ingresos.
	 *
	 * @param  HotelRentItemPayment $payment
	 * @param  string|int|null      $paymentDestinationId
	 * @return void
	 */
	private function linkRentPaymentToOpenCash(HotelRentItemPayment $payment, $paymentDestinationId = null)
	{
		// Si no se indica destino, por defecto va a caja general.
		if (empty($paymentDestinationId)) {
			$paymentDestinationId = 'cash';
		}

		// Solo se registra el movimiento en caja si hay una caja abierta. No se
		// interrumpe la operación de hotel cuando la caja está cerrada.
		if ($paymentDestinationId === 'cash') {
			$cash = $this->getCash();
			if (!$cash || empty($cash['cash_id'])) {
				return;
			}
		}

		// Evitar duplicar el pago global (p. ej. al reprocesar).
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
				'date_of_payment' => date('Y-m-d H:i:s'),
				'payment_method_type_id' => $rent_payment['payment_method_type_id'],
				'reference' => $rent_payment['reference'],
				'payment' => $rent_payment['payment'],
			]);

			$this->linkRentPaymentToOpenCash($record, $rent_payment['payment_destination_id'] ?? null);
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

      // Total de la extensión. Si el front envía un total personalizado
      // (extension_total editable), se usa tal cual y el precio unitario se
      // deriva de él; si no, se calcula como precio × cantidad.
      $customTotal = (float) ($request->extension_total ?? 0);
      if ($customTotal > 0) {
        $totalExt  = round($customTotal, 4);
        $unitPrice = $quantity > 0 ? round($totalExt / $quantity, 4) : $totalExt;
      } else {
        $totalExt  = round($unitPrice * $quantity, 4);
      }

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
        // Preferir el id real del método de pago enviado por el front (tomado de
        // la API de métodos de pago); compatibilidad con el flujo antiguo que
        // enviaba un alias de texto en payment_method.
        $paymentMethodId = $request->filled('payment_method_type_id')
          ? $request->payment_method_type_id
          : $this->getPaymentMethodId($request->payment_method);

        if (!$paymentMethodId) {
          throw new \Exception('No se encontró un método de pago válido. Por favor, configure los métodos de pago en el sistema.');
        }

        $payment = new HotelRentItemPayment();
        $payment->hotel_rent_item_id = $extensionItem->id;
        $payment->date_of_payment = date('Y-m-d H:i:s');
        $payment->payment_method_type_id = $paymentMethodId;
        $payment->reference = $request->payment_reference ?? null;
        $payment->payment = $request->payment_amount;
        $payment->save();

        $this->linkRentPaymentToOpenCash($payment, $request->input('payment_destination_id'));

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

		$series = app(SeriesResolver::class)->applyContext(Series::where('establishment_id',  auth()->user()->establishment_id))->get();

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
		
		// Productos recién agregados en esta petición (para registrar en el
		// historial de cambios y poder revertirlos luego).
		$addedProducts = [];

		foreach ($request->products as $product) {
			$item = HotelRentItem::where('hotel_rent_id', $rentId)
				->where('id', $product['id'])
				->first();
			$isNew = false;
			if (!$item) {
				$item = new HotelRentItem();
				$item->type = 'PRO';
				$item->hotel_rent_id = $rentId;
				$item->item_id = $product['item_id'];
				$item->payment_status = $product['payment_status'];
				$item->save();

				$this->saveHotelRentItemPayment($product['rent_payment'], $item);
				$isNew = true;
			}
			$item->item = $product;
			$item->payment_status = $product['payment_status'];
			$item->hotel_rent_order_id =  null;
			$item->save();
            $idInRequest[] = $item->id;

			if ($isNew) {
				$inner = $product['item'] ?? [];
				if (is_object($inner)) $inner = (array) $inner;
				$name = $product['description']
					?? ($inner['description'] ?? ($inner['name'] ?? ($product['name'] ?? 'Producto')));
				$addedProducts[] = [
					'item_id'     => $item->id,
					'name'        => $name,
					'quantity'    => $product['quantity'] ?? 1,
					'total'       => floatval($product['total'] ?? 0),
				];
			}
		}

		// Auto-aplicar adelantos disponibles a items con deuda
		$this->applyAdvanceCreditToDebtItems($rent);

		// Registrar en el historial de cambios los productos recién agregados,
		// de modo que aparezcan en el historial de la habitación y se puedan
		// revertir (eliminar el producto y su cobro). Se crea UN registro por
		// producto para que cada uno sea un movimiento independiente y se pueda
		// revertir individualmente sin afectar a los demás.
		foreach ($addedProducts as $addedProduct) {
			HotelRentChange::create([
				'hotel_rent_id'    => $rent->id,
				'change_type'      => 'PRODUCT_ADD',
				'old_values'       => [],
				'new_values'       => [
					'products'  => [$addedProduct],
					'item_ids'  => [$addedProduct['item_id']],
				],
				'notes'            => 'Producto agregado: ' . $addedProduct['name'],
				'price_difference' => floatval($addedProduct['total'] ?? 0),
				'user_id'          => auth()->id(),
			]);
		}

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

    // Reconciliar el estado de pago de los items con lo realmente pagado antes
    // de mostrar el checkout. Sin esto, una reserva pagada por adelantado (o
    // pagada al reservar por la web) abría el checkout con los items todavía en
    // DEBT y se veía como "falta pagar" aunque ya estuviera pagada. Es
    // idempotente: solo marca PAID lo que el crédito disponible cubre.
    try {
        $this->applyAdvanceCreditToDebtItems($rent);
        $rent->refresh();
    } catch (\Throwable $th) {
        // No bloquear la carga del checkout si la reconciliación falla.
    }

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
        $series = app(SeriesResolver::class)->applyContext(Series::where('establishment_id',  auth()->user()->establishment_id))->get();
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
	 * Genera una nota de venta con los consumos pendientes de una reserva/renta.
	 *
	 * Pensado para el modal de detalle del calendario de reservas: permite emitir
	 * la NV sin pasar por el checkout, pero SOLO cuando la reserva ya está
	 * pagada (deuda <= 0). Reutiliza SaleNoteController::storeWithData, que es
	 * el mismo camino que usa el checkout al hacer POST /sale-notes.
	 *
	 * La nota queda ligada a la renta (hotel_rent_id) para que la caja NO la
	 * cuente dos veces: el ingreso de hotel se reporta por los pagos de la
	 * reserva (ver Cash::getHotelRentIncome). Por la misma razón los pagos
	 * copiados llevan `hotel_rent_item_payment_id`, marcador que savePayments
	 * usa para no volver a registrarlos en caja.
	 */
	public function generateSaleNoteFromRent($rentId)
	{
		$rent = HotelRent::with('items')->findOrFail($rentId);

		// Reconciliar adelantos → items PAID antes de evaluar la deuda (misma
		// llamada que hace el checkout al abrirse).
		try {
			$this->applyAdvanceCreditToDebtItems($rent);
			$rent->refresh();
			$rent->load('items');
		} catch (\Throwable $th) {
			// No bloquear la emisión si la reconciliación falla.
		}

		$debt = $this->calculateRentDebt($rent);
		if ($debt > 0.009) {
			return response()->json([
				'success' => false,
				'message' => 'La reserva tiene un saldo pendiente de S/ ' . number_format($debt, 2)
					. '. Solo se puede generar la nota de venta cuando está pagada.',
			], 422);
		}

		$pendingItems = HotelRentItem::where('hotel_rent_id', $rent->id)
			->whereNotIn('type', ['PAY', '9'])
			->whereNull('sale_note_id')
			->whereNull('document_id')
			->orderBy('id', 'asc')
			->get();

		if ($pendingItems->isEmpty()) {
			return response()->json([
				'success' => false,
				'message' => 'Todos los consumos de esta reserva ya cuentan con comprobante.',
			], 422);
		}

		$establishmentId = $rent->establishment_id ?: auth()->user()->establishment_id;

		$series = app(SeriesResolver::class)->applyContext(
			Series::where('establishment_id', $establishmentId)->where('document_type_id', '80')
		)->first();

		if (!$series) {
			return response()->json([
				'success' => false,
				'message' => 'No hay una serie configurada para notas de venta en esta sucursal.',
			], 422);
		}

		// Filas del comprobante: el JSON guardado en hotel_rent_items.item ya es
		// la fila calculada por calculateRowItem (el mismo objeto que arma el
		// checkout), así que se reutiliza tal cual.
		$rows = [];
		$itemIds = [];

		foreach ($pendingItems as $rentItem) {
			$row = $rentItem->item;
			$row = is_object($row) ? json_decode(json_encode($row), true) : (is_array($row) ? $row : []);

			if (empty($row) || empty($row['item'])) {
				continue;
			}

			// `record_id` apunta a filas de otro comprobante; en una nota nueva
			// debe ir en null para que SaleNoteItem cree registros nuevos.
			$row['record_id'] = null;
			unset($row['id']);

			foreach ([
				'quantity', 'unit_value', 'unit_price', 'total_base_igv', 'percentage_igv',
				'total_igv', 'total_base_isc', 'percentage_isc', 'total_isc',
				'total_base_other_taxes', 'percentage_other_taxes', 'total_other_taxes',
				'total_taxes', 'total_value', 'total_charge', 'total_discount', 'total',
				'total_plastic_bag_taxes',
			] as $numeric) {
				$row[$numeric] = isset($row[$numeric]) ? (float) $row[$numeric] : 0;
			}

			$row['attributes'] = $row['attributes'] ?? [];
			$row['charges']    = $row['charges'] ?? [];
			$row['discounts']  = $row['discounts'] ?? [];

			$rows[] = $row;
			$itemIds[] = $rentItem->id;
		}

		if (empty($rows)) {
			return response()->json([
				'success' => false,
				'message' => 'La reserva no tiene consumos facturables.',
			], 422);
		}

		$totals = $this->calculateSaleNoteTotals($rows);

		$inputs = [
			'customer_id'              => $rent->customer_id,
			'establishment_id'         => $establishmentId,
			'series_id'                => $series->id,
			'prefix'                   => 'NV',
			'date_of_issue'            => date('Y-m-d'),
			'time_of_issue'            => date('H:i:s'),
			'due_date'                 => date('Y-m-d'),
			'currency_type_id'         => 'PEN',
			'purchase_order'           => null,
			'exchange_rate_sale'       => 0,
			'operation_type_id'        => '0101',
			'items'                    => $rows,
			'charges'                  => [],
			'discounts'                => [],
			'attributes'               => [],
			'guides'                   => [],
			'payments'                 => $this->buildSaleNotePaymentsFromRent($rent, $totals['total']),
			'additional_information'   => null,
			'actions'                  => ['format_pdf' => 'a4'],
			'hotel_data_persons'       => $rent->data_persons,
			'source_module'            => 'HOTEL',
			'hotel_rent_id'            => $rent->id,
		] + $totals;

		$result = (new SaleNoteController())->storeWithData($inputs);

		if (!isset($result['success']) || !$result['success']) {
			return response()->json([
				'success' => false,
				'message' => $result['message'] ?? 'No se pudo generar la nota de venta.',
			], 500);
		}

		$saleNoteId = $result['data']['id'];

		HotelRentItem::where('hotel_rent_id', $rent->id)
			->whereIn('id', $itemIds)
			->update([
				'sale_note_id'   => $saleNoteId,
				'invoiced_at'    => now(),
				'payment_status' => 'PAID',
			]);

		return response()->json([
			'success'     => true,
			'message'     => 'Nota de venta ' . ($result['data']['number_full'] ?? '') . ' generada.',
			'sale_note_id' => $saleNoteId,
			'number_full' => $result['data']['number_full'] ?? null,
		]);
	}

	/**
	 * Totales del comprobante a partir de sus filas. Réplica de
	 * onCalculateTotals() de Checkout.vue para que la NV emitida desde el
	 * calendario cuadre igual que la emitida desde el checkout.
	 */
	private function calculateSaleNoteTotals(array $rows)
	{
		$taxed = $exonerated = $unaffected = $free = $exportation = 0;
		$igv = $value = $total = $plasticBag = $discount = $charge = 0;

		foreach ($rows as $row) {
			$affectation = (string) ($row['affectation_igv_type_id'] ?? '');

			$discount += (float) $row['total_discount'];
			$charge   += (float) $row['total_charge'];

			if ($affectation === '10') {
				$taxed += (float) $row['total_value'];
			}

			if ($affectation === '20') {
				$exonerated += (float) $row['total_value'];
			}

			if (!in_array($affectation, ['10', '20', '30', '40'], true)) {
				$free += (float) $row['total_value'];
			} else {
				$igv   += (float) $row['total_igv'];
				$total += (float) $row['total'];
			}

			$value      += (float) $row['total_value'];
			$plasticBag += (float) $row['total_plastic_bag_taxes'];
		}

		$plasticBag = round($plasticBag, 2);
		$documentTotal = round($total + $plasticBag, 2);

		return [
			'total_prepayment'        => 0,
			'total_charge'            => round($charge, 2),
			'total_discount'          => round($discount, 2),
			'total_exportation'       => round($exportation, 2),
			'total_free'              => round($free, 2),
			'total_taxed'             => round($taxed, 2),
			'total_unaffected'        => round($unaffected, 2),
			'total_exonerated'        => round($exonerated, 2),
			'total_igv'               => round($igv, 2),
			'total_base_isc'          => 0,
			'total_isc'               => 0,
			'total_base_other_taxes'  => 0,
			'total_other_taxes'       => 0,
			'total_taxes'             => round($igv, 2),
			'total_value'             => round($value, 2),
			'total_plastic_bag_taxes' => $plasticBag,
			'total'                   => $documentTotal,
			'subtotal'                => $documentTotal,
		];
	}

	/**
	 * Pagos de la reserva a copiar en la nota de venta, escalados al total del
	 * comprobante (puede haber consumos ya facturados en otro comprobante).
	 * Cada fila lleva `hotel_rent_item_payment_id` para que NO se vuelva a
	 * registrar el pago en caja: ya está contabilizado por la reserva.
	 */
	private function buildSaleNotePaymentsFromRent(HotelRent $rent, $documentTotal)
	{
		$documentTotal = round((float) $documentTotal, 2);

		if ($documentTotal <= 0) {
			return [];
		}

		$payments = HotelRentItemPayment::whereHas('associated_record_payment', function ($q) use ($rent) {
			$q->where('hotel_rent_id', $rent->id);
		})->orderBy('id', 'asc')->get();

		$paidTotal = round((float) $payments->sum('payment'), 2);

		if ($payments->isEmpty() || $paidTotal <= 0) {
			return [];
		}

		$rows = [];
		$remaining = $documentTotal;
		$lastIndex = $payments->count() - 1;

		foreach ($payments->values() as $index => $payment) {
			$amount = $index === $lastIndex
				? round($remaining, 2)
				: round(((float) $payment->payment * $documentTotal) / $paidTotal, 2);

			$remaining = round($remaining - $amount, 2);

			if ($amount <= 0) {
				continue;
			}

			$rows[] = [
				'id'                         => null,
				'sale_note_id'               => null,
				'hotel_rent_item_payment_id' => $payment->id,
				'date_of_payment'            => $payment->date_of_payment
					? Carbon::parse($payment->date_of_payment)->format('Y-m-d')
					: date('Y-m-d'),
				'payment_method_type_id'     => $payment->payment_method_type_id,
				'reference'                  => $payment->reference,
				'payment'                    => $amount,
				'change'                     => 0,
			];
		}

		return $rows;
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
                'customer' => isset($row->customer->description)
                    ? $row->customer->description
                    : (isset($row->customer->name) ? $row->customer->name : ''),
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

            // Snapshot del estado actual para el historial
            $period         = $rent->rental_period_type ?: 'day';
            $oldHotelRateId = $rent->hotel_rate_id;

            // Todos los items HAB abiertos (no facturados), del más antiguo al
            // más reciente. Tras una o varias EXTENSIONES la estadía queda
            // repartida en VARIOS items HAB; el cambio de habitación debe
            // tratarlos en conjunto. La versión anterior solo modificaba el
            // último item pero calculaba las noches restantes contra la
            // duración TOTAL del alquiler, de modo que las noches de los items
            // que no se tocaban se seguían cobrando ADEMÁS de las nuevas →
            // doble cobro.
            $openHabItems = $rent->items()
                ->where('type', 'HAB')
                ->whereNull('sale_note_id')
                ->whereNull('document_id')
                ->orderBy('id')
                ->get();

            if ($openHabItems->isEmpty()) {
                // === Cambio de habitación cuando la estadía YA tiene comprobantes ===
                // Todos los items HAB están facturados (document_id / sale_note_id),
                // así que no hay nada "abierto" que cerrar/dividir. Antes esto
                // abortaba con "No se encontró un item de habitación abierto para
                // cerrar". Ahora se permite el cambio: las noches ya facturadas
                // quedan en la habitación anterior y las noches RESTANTES (desde la
                // fecha del cambio) se cobran en la nueva habitación como un item
                // nuevo en DEBT, con su propia tarifa → aparece como una segunda
                // línea con el precio diferente (igual que en la pro7).
                $oldRentalPrice = (float) $rent->rental_price;
                $totalDuration  = max(1, (int) ($rent->duration ?? 0));

                [$consumed, $remaining, $unitLabel] = $this->calculateRoomChangeSplit(
                    $period,
                    $inputAt,
                    $changeAt,
                    $totalDuration
                );

                // JSON base tomado del último HAB (aunque esté facturado) para
                // preservar la estructura de IGV/charges en el nuevo item.
                $allHab  = $rent->items()->where('type', 'HAB')->orderBy('id')->get();
                $lastHab = $allHab->last();
                $baseJsonForNew = $lastHab && is_object($lastHab->item)
                    ? (array) $lastHab->item
                    : ($lastHab->item ?? []);
                if (!is_array($baseJsonForNew)) $baseJsonForNew = [];

                $newItemRecord  = Item::find($newRoom->item_id);
                $newTotal       = round($newUnitPrice * $remaining, 4);
                $newDescription = sprintf(
                    'Estadía en %s - %d %s (%s → %s)',
                    $newRoom->name,
                    $remaining,
                    $unitLabel,
                    $changeAt->format('d/m/Y H:i'),
                    $outputAt->format('d/m/Y H:i')
                );

                $newItemJson = $this->rewriteHotelItemForRoom(
                    $baseJsonForNew,
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

                // Actualizar alquiler y estados de habitaciones.
                $rent->hotel_room_id = $newRoom->id;
                $rent->hotel_rate_id = $newRateId;
                $rent->rental_price  = $newUnitPrice;
                $rent->save();

                $oldRoom->status = 'LIMPIEZA';
                $oldRoom->save();
                $newRoom->status = 'OCUPADO';
                $newRoom->save();

                // Aplicar crédito disponible (saldo a favor) a la nueva deuda.
                $this->applyAdvanceCreditToDebtItems($rent);

                HotelRentChange::create([
                    'hotel_rent_id'    => $rent->id,
                    'change_type'      => 'ROOM_CHANGE',
                    'old_values'       => [
                        'hotel_room_id' => $oldRoom->id,
                        'room_name'     => $oldRoom->name,
                        'hotel_rate_id' => $oldHotelRateId,
                        'unit_price'    => $oldRentalPrice,
                        'item_id'       => $lastHab->id ?? null,
                        'invoiced'      => true,
                    ],
                    'new_values'       => [
                        'hotel_room_id' => $newRoom->id,
                        'room_name'     => $newRoom->name,
                        'hotel_rate_id' => (int) $newRateId,
                        'unit_price'    => $newUnitPrice,
                        'item_id'       => $newItem->id,
                        'consumed'      => $consumed,
                        'remaining'     => $remaining,
                        'unit'          => $unitLabel,
                        'changed_at'    => $changeAt->toDateTimeString(),
                    ],
                    'notes'            => "Cambio de habitación (con comprobante): {$oldRoom->name} → {$newRoom->name}",
                    'price_difference' => $newTotal,
                    'user_id'          => auth()->id(),
                ]);

                DB::connection('tenant')->commit();

                $rent->load(['room', 'customer', 'items']);

                return response()->json([
                    'success' => true,
                    'message' => 'Habitación cambiada. Las noches ya facturadas quedan en la habitación anterior; las restantes se cobran en la nueva habitación.',
                    'data'    => [
                        'rent'             => $rent,
                        'old_room'         => $oldRoom->name,
                        'new_room'         => $newRoom->name,
                        'consumed'         => $consumed,
                        'remaining'        => $remaining,
                        'unit'             => $unitLabel,
                        'new_item_id'      => $newItem->id,
                        'price_difference' => $newTotal,
                    ],
                ], 200);
            }

            // Resuelve la cantidad real de noches de un item. OJO: los items
            // creados en store() NO setean la columna `quantity` (queda en su
            // valor por defecto = 1); las noches reales viven en el JSON
            // `item.quantity`. El resto de la app (UI, totales) lee el JSON, así
            // que aquí también se prefiere el JSON, con fallback a la columna.
            $resolveQuantity = function (HotelRentItem $it) {
                $json = is_object($it->item) ? (array) $it->item : ($it->item ?: []);
                $jq   = isset($json['quantity']) ? (float) $json['quantity'] : 0.0;
                if ($jq > 0) return (int) round($jq);
                $col = (int) $it->quantity;
                return $col > 0 ? $col : 1;
            };

            // Resuelve el precio unitario real de un item (la columna unit_price
            // viene como "0.0000" en items antiguos → preferir el JSON).
            $resolveUnit = function (HotelRentItem $it) {
                $json = is_object($it->item) ? (array) $it->item : ($it->item ?: []);
                $col  = (float) $it->unit_price;
                $j    = (float) ($json['unit_price'] ?? $json['unit_price_value'] ?? 0);
                return $col > 0 ? $col : $j;
            };

            // Duración realmente abierta = suma de las noches (del JSON) de TODOS
            // los HAB abiertos (estadía original + todas las extensiones).
            $openDuration = max(1, (int) $openHabItems->sum(function (HotelRentItem $it) use ($resolveQuantity) {
                return $resolveQuantity($it);
            }));

            [$consumed, $remaining, $unitLabel] = $this->calculateRoomChangeSplit(
                $period,
                $inputAt,
                $changeAt,
                $openDuration
            );

            // Snapshot de TODOS los items abiertos para poder revertir con fidelidad.
            $itemsSnapshot = $openHabItems->map(function (HotelRentItem $it) {
                return [
                    'id'             => $it->id,
                    'item_id'        => $it->item_id,
                    'item'           => is_object($it->item) ? (array) $it->item : ($it->item ?: []),
                    'quantity'       => $it->quantity,
                    'unit_price'     => $it->unit_price,
                    'total'          => $it->total,
                    'description'    => $it->description,
                    'payment_status' => $it->payment_status,
                ];
            })->values()->all();

            // Precio unitario representativo de la habitación anterior y JSON base
            // para el nuevo item (preserva IGV/charges del último HAB abierto).
            $lastOpen       = $openHabItems->last();
            $oldUnitPrice   = $resolveUnit($lastOpen);
            $baseJsonForNew = is_object($lastOpen->item) ? (array) $lastOpen->item : ($lastOpen->item ?: []);

            // Item de la nueva habitación (para reescribir identidad en el JSON)
            $newItemRecord  = Item::find($newRoom->item_id);
            $newTotal       = round($newUnitPrice * $remaining, 4);
            $newDescription = sprintf(
                'Estadía en %s - %d %s (%s → %s)',
                $newRoom->name,
                $remaining,
                $unitLabel,
                ($consumed === 0 ? $inputAt : $changeAt)->format('d/m/Y H:i'),
                $outputAt->format('d/m/Y H:i')
            );

            $newItemJson = $this->rewriteHotelItemForRoom(
                $baseJsonForNew,
                $newItemRecord,
                $newRoom,
                $newDescription,
                $newUnitPrice,
                $remaining,
                $newTotal
            );

            // Crear PRIMERO el item de la nueva habitación (las `remaining`
            // noches), para poder reasignarle los pagos de las noches futuras.
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

            // Trasladar TODOS los pagos de los items HAB que se están cambiando
            // al nuevo item. El dinero ya pagado debe seguir a la habitación
            // nueva; luego applyAdvanceCreditToDebtItems lo re-aplica (item más
            // antiguo primero) según el crédito disponible, de modo que solo se
            // cobre —o devuelva— la DIFERENCIA real respecto a la tarifa nueva.
            // Antes los pagos quedaban en el item anterior (ya recortado o
            // eliminado) y la habitación nueva aparecía como deuda total / con
            // vuelto, aunque la nueva tarifa fuera mayor.
            $openIds = $openHabItems->pluck('id')->all();
            HotelRentItemPayment::whereIn('hotel_rent_item_id', $openIds)
                ->update(['hotel_rent_item_id' => $newItem->id]);

            // Retira las noches "futuras" de los items abiertos, empezando por
            // los más recientes (los primeros días corresponden a la habitación
            // anterior ya consumida). Los items afectados pasan a DEBT para que
            // el crédito ya pagado los re-marque como pagados si alcanza.
            $toRemove       = $remaining;   // noches que dejan la habitación anterior
            $oldFutureTotal = 0.0;          // valor (a tarifa antigua) de esas noches
            $oldAnchorItem  = null;         // último item que permanece (ancla histórico)

            foreach ($openHabItems->reverse()->values() as $it) {
                if ($toRemove <= 0) {
                    // Noche(s) ya consumida(s) en la habitación anterior: se
                    // mantienen. A DEBT para que el crédito las re-marque.
                    $it->payment_status = 'DEBT';
                    $it->save();
                    if (!$oldAnchorItem) {
                        $oldAnchorItem = $it;
                    }
                    continue;
                }

                $q      = $resolveQuantity($it);
                $itUnit = $resolveUnit($it);

                if ($toRemove >= $q) {
                    // Item completo en la zona futura → sus noches pasan a la
                    // nueva habitación; el item anterior se elimina (sus pagos
                    // ya fueron trasladados al nuevo item).
                    $oldFutureTotal += round($itUnit * $q, 4);
                    $it->delete();
                    $toRemove -= $q;
                } else {
                    // Item en el límite: parte consumida (se queda en la
                    // habitación anterior), parte futura (pasa a la nueva).
                    $keep            = $q - $toRemove;
                    $oldFutureTotal += round($itUnit * $toRemove, 4);

                    $keepTotal       = round($itUnit * $keep, 4);
                    $keepDescription = sprintf(
                        'Estadía en %s - %d %s (%s → %s)',
                        $oldRoom->name,
                        $keep,
                        $unitLabel,
                        $inputAt->format('d/m/Y H:i'),
                        $changeAt->format('d/m/Y H:i')
                    );

                    $itJson          = is_object($it->item) ? (array) $it->item : ($it->item ?: []);
                    $it->item        = $this->rewriteHotelItemDescription(
                        $itJson, $keepDescription, $itUnit, $keep, $keepTotal
                    );
                    $it->quantity       = $keep;
                    $it->unit_price     = $itUnit;
                    $it->total          = $keepTotal;
                    $it->description    = $keepDescription;
                    $it->payment_status = 'DEBT';
                    $it->save();

                    $oldAnchorItem = $it;
                    $toRemove = 0;
                }
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

            // Aplicar adelantos disponibles a la nueva deuda (o liberar saldo a
            // favor si la nueva habitación resulta más barata).
            $this->applyAdvanceCreditToDebtItems($rent);

            // 5) Historial de cambios. La diferencia real es el costo de las
            // noches nuevas menos el de las noches retiradas de la habitación
            // anterior (negativo si la nueva tarifa es menor).
            $priceDifference = round($newTotal - $oldFutureTotal, 4);

            HotelRentChange::create([
                'hotel_rent_id'    => $rent->id,
                'change_type'      => 'ROOM_CHANGE',
                'old_values'       => [
                    'hotel_room_id'  => $oldRoom->id,
                    'room_name'      => $oldRoom->name,
                    'hotel_rate_id'  => $oldHotelRateId,
                    'unit_price'     => $oldUnitPrice,
                    'item_id'        => $oldAnchorItem->id ?? null,
                    'rental_price'   => $oldUnitPrice,
                    'items_snapshot' => $itemsSnapshot,
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
                    'old_item_id'      => $oldAnchorItem->id ?? null,
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
                    $extensionPayment = HotelRentItemPayment::create([
                        'hotel_rent_item_id' => $extensionItem->id,
                        'date_of_payment' => date('Y-m-d H:i:s'),
                        'payment_method_type_id' => $paymentMethodId,
                        'reference' => $request->input('payment_reference'),
                        'payment' => $paymentAmount,
                    ]);

                    $this->linkRentPaymentToOpenCash($extensionPayment, $request->input('payment_destination_id'));

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
     * Calcula la deuda real de un alquiler con la misma fórmula que usa el
     * frontend (Checkout.vue) y HotelReceptionController::attachRentBalances:
     *
     *   deuda = Σ(total de items, excluyendo PAY) - Σ(pagos) + arrears
     *
     * Es la fuente de verdad para la deuda: NO depende del payment_status de
     * los items (que puede quedar en PAID al emitir un comprobante sin pago).
     */
    private function calculateRentDebt(HotelRent $rent)
    {
        $items = $rent->items ?: collect();

        $totalOriginalItems = $items
            ->filter(function ($i) { return $i->type !== 'PAY'; })
            ->sum(function ($i) {
                $itemObj = $i->item;
                return (is_object($itemObj) && isset($itemObj->total)) ? floatval($itemObj->total) : 0;
            });

        $netPayments = floatval(
            HotelRentItemPayment::whereHas('associated_record_payment', function ($q) use ($rent) {
                $q->where('hotel_rent_id', $rent->id);
            })->sum('payment')
        );

        $arrears = floatval($rent->arrears ?? 0);

        return round($totalOriginalItems - $netPayments + $arrears, 2);
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

                    // No hay items en estado DEBT. Hay dos escenarios distintos:
                    //  a) Realmente no se debe nada -> el pago es un ADELANTO/crédito.
                    //  b) Sí existe deuda real (p.ej. se emitió el comprobante sin
                    //     pagar y los items quedaron en PAID): la deuda se calcula
                    //     con la fórmula real, no con el payment_status. En ese caso
                    //     el pago liquida la deuda y el excedente es VUELTO (change).
                    if ($debtItems->isEmpty()) {
                        $paymentItemId = $this->resolvePaymentItemId($rent);
                        if (!$paymentItemId) {
                            DB::connection('tenant')->rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => 'No se pudo registrar el adelanto porque no se encontró un item base para asociar el pago.'
                            ], 422);
                        }

                        $realDebt = $this->calculateRentDebt($rent);

                        // Caso (b): existe deuda real aunque los items estén PAID.
                        if ($realDebt > 0.009) {
                            // El pago debe colgar de un HotelRentItem real (no del
                            // catálogo). Usamos el item facturable más reciente.
                            $targetItem = HotelRentItem::where('hotel_rent_id', $rent->id)
                                ->where('type', '!=', 'PAY')
                                ->orderByDesc('id')
                                ->first();

                            if (!$targetItem) {
                                DB::connection('tenant')->rollBack();
                                return response()->json([
                                    'success' => false,
                                    'message' => 'No se pudo registrar el pago porque no se encontró un item al cual asociarlo.'
                                ], 422);
                            }

                            $appliedToDebt = min($amount, $realDebt);
                            $change        = round($amount - $appliedToDebt, 2); // vuelto

                            $debtPayment = new HotelRentItemPayment();
                            $debtPayment->hotel_rent_item_id = $targetItem->id;
                            $debtPayment->payment = $appliedToDebt;
                            $debtPayment->payment_method_type_id = $paymentMethodTypeId;
                            $debtPayment->reference = $reference;
                            $debtPayment->change = $change;
                            $debtPayment->date_of_payment = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                            $debtPayment->save();

                            // En caja solo entra lo aplicado a la deuda; el vuelto se
                            // devuelve al cliente y no forma parte del ingreso.
                            $this->registerRentPaymentInCash($debtPayment, $paymentDestinationId);

                            DB::connection('tenant')->commit();

                            return response()->json([
                                'success'      => true,
                                'message'      => $change > 0
                                    ? 'Pago registrado correctamente. Vuelto: S/ ' . number_format($change, 2)
                                    : 'Pago guardado correctamente',
                                'payment_id'   => $debtPayment->id,
                                'total_amount' => $appliedToDebt,
                                'change'       => $change,
                            ], 200);
                        }

                        // Caso (a): adelanto/crédito real para futuros cargos.
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
                $lastPayment = null; // último pago creado, para registrar el vuelto

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
                    $payment->change = 0; // el vuelto se asigna al final, sobre el último pago
                    $payment->date_of_payment = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                    $payment->save();
                    $lastPayment = $payment;

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
                
                // Si sobra dinero tras cubrir toda la deuda, es VUELTO: se registra
                // como `change` sobre el último pago (no como ingreso/crédito), para
                // que aparezca en el historial y no se pierda como antes.
                if ($remainingAmount > 0) {
                    if ($lastPayment) {
                        $lastPayment->change = round($remainingAmount, 2);
                        $lastPayment->save();
                    } else {
                        // Borde defensivo: no se creó ningún pago en el bucle (los
                        // items ya estaban cubiertos). Registramos un pago portador
                        // del vuelto sobre el primer item con deuda (HotelRentItem real).
                        $carrierItem = $debtItems->first();
                        if (!$carrierItem) {
                            DB::connection('tenant')->rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => 'No se pudo registrar el vuelto porque no se encontró un item base para asociarlo.'
                            ], 422);
                        }

                        $payment = new HotelRentItemPayment();
                        $payment->hotel_rent_item_id = $carrierItem->id;
                        $payment->payment = 0;
                        $payment->payment_method_type_id = $paymentMethodTypeId;
                        $payment->reference = $reference;
                        $payment->change = round($remainingAmount, 2);
                        $payment->date_of_payment = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                        $payment->save();
                    }
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

                // Eliminar el movimiento de caja/finanzas asociado para no dejar
                // un ingreso huérfano en el cierre de caja.
                $payment->load('global_payment');
                if ($payment->global_payment) {
                    $payment->global_payment()->delete();
                }

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
