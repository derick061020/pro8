<?php

namespace Modules\Hotel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Hotel\Http\Requests\HotelAddRateToRoomRequest;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Models\HotelFloor;
use Modules\Hotel\Models\HotelCategory;
use Modules\Hotel\Http\Requests\HotelRoomRequest;
use Modules\Hotel\Http\Requests\HotelFloorRequest;
use Modules\Hotel\Models\HotelRate;
use Modules\Hotel\Models\HotelRoomRate;
use Modules\Hotel\Models\HotelRent;
use App\Models\Tenant\Establishment;

class HotelRoomController extends Controller
{
	/**
	 * Display a listing of the resource.
	 * @return Response
	 */
	public function index()
	{
		$user = auth()->user();

		$query = HotelRoom::with('establishment','category', 'floor')->orderBy('id', 'DESC');

		if (request()->ajax()) {

			if (request('establishment_id') && $user->type === 'admin') {
				$query->where('establishment_id', request('establishment_id'));
			}

			if ($user->type != 'admin') {
				$query->where('establishment_id', $user->establishment_id);
			}

			if (request('hotel_floor_id')) {
				$query->where('hotel_floor_id', request('hotel_floor_id'));
			}
			if (request('hotel_category_id')) {
				$query->where('hotel_category_id', request('hotel_category_id'));
			}
			if (request('status')) {
				$query->where('status', request('status'));
			}
			if (request('name')) {
				$query->where('name', 'like', '%' . request('name') . '%');
			}

			$rooms = $query->paginate(25);
			return response()->json([
				'success' => true,
				'rooms' => $rooms
			], 200);
		}

		$query->where('establishment_id', $user->establishment_id);

		$rooms = $query->paginate(25);

		$establishments = Establishment::select('id','description')->get();
		$userType = auth()->user()->type;
		$establishmentId = auth()->user()->establishment_id;

		$categories = HotelCategory::where('active', true)
			->where('establishment_id', $user->establishment_id)
			->orderBy('description')
			->get();

		$floors = HotelFloor::where('active', true)
			->where('establishment_id', $user->establishment_id)
			->orderBy('description')
			->get();

		$roomStatus = HotelRoom::$status;



		return view('hotel::rooms.index', compact('rooms', 'floors', 'categories', 'roomStatus','establishments','userType','establishmentId'));

	}

	/**
	 * Store a newly created resource in storage.
	 * @param Request $request
	 * @return Response
	 */
	public function store(HotelRoomRequest $request)
	{
		$room = HotelRoom::create($request->validated());
		$room->status = 'DISPONIBLE';
		$room->save();

		return response()->json([
			'success' => true,
			'data'    => $room
		], 200);
	}

	/**
	 * Update the specified resource in storage.
	 * @param Request $request
	 * @param int $id
	 * @return Response
	 */
	public function update(HotelFloorRequest $request, $id)
	{
		$room = HotelRoom::findOrFail($id);
		$oldName = $room->name;
		$room = $room->fill($request->only('description', 'active', 'name', 'hotel_category_id', 'hotel_floor_id','establishment_id'));
		$room->save();

		// Si cambió el nombre, propagar el nuevo nombre a los items HAB de
		// alquileres activos (no finalizados) para que las boletas/notas
		// emitidas a partir de este momento usen el nombre actualizado.
		if ($oldName !== $room->name) {
			$this->propagateRoomNameToActiveItems($room, $oldName);
		}

		return response()->json([
			'success' => true,
			'data'    => $room
		], 200);
	}

	/**
	 * Actualiza el nombre de la habitación dentro del JSON `item` de los
	 * HotelRentItem de tipo HAB en alquileres activos. No toca items ya
	 * facturados (sale_note_id/document_id no nulos) para no falsear los
	 * comprobantes ya emitidos.
	 */
	private function propagateRoomNameToActiveItems(HotelRoom $room, $oldName)
	{
		$activeRents = HotelRent::where('hotel_room_id', $room->id)
			->where('status', '!=', 'FINALIZADO')
			->pluck('id');

		if ($activeRents->isEmpty()) return;

		$items = \Modules\Hotel\Models\HotelRentItem::whereIn('hotel_rent_id', $activeRents)
			->where('type', 'HAB')
			->whereNull('sale_note_id')
			->whereNull('document_id')
			->get();

		foreach ($items as $item) {
			$json = is_object($item->item) ? (array) $item->item : ($item->item ?: []);
			$inner = isset($json['item']) ? $json['item'] : [];
			if (is_object($inner)) $inner = (array) $inner;
			if (!is_array($inner)) $inner = [];

			$replace = function ($text) use ($oldName, $room) {
				if (!$text || !is_string($text)) return $text;
				return str_replace($oldName, $room->name, $text);
			};

			foreach (['description', 'full_description', 'name_product_pdf', 'name'] as $k) {
				if (isset($json[$k])) $json[$k] = $replace($json[$k]);
				if (isset($inner[$k])) $inner[$k] = $replace($inner[$k]);
			}
			$json['item'] = $inner;

			$item->item = $json;
			if ($item->description) $item->description = $replace($item->description);
			$item->save();
		}
	}

	/**
	 * Remove the specified resource from storage.
	 * @param int $id
	 * @return Response
	 */
	public function destroy($id)
	{
		try {
			HotelRoom::where('id', $id)
				->delete();

			return response()->json([
				'success' => true,
				'message' => 'Información actualizada'
			], 200);
		} catch (\Throwable $th) {
			return response()->json([
				'success' => false,
				'data'    => 'Ocurrió un error al procesar su petición. Detalles: ' . $th->getMessage()
			], 500);
		}
	}

	public function changeRoomStatus($roomId)
	{
		HotelRoom::where('id', $roomId)
			->update([
				'status' => request('status')
			]);

		return response()->json([
			'success' => true,
			'message' => 'La habitación cambió su estado a DISPONIBLE',
		], 200);
	}

	public function tables($id)
	{
		$user = auth()->user();

		$categories = $this->getTablesQuery(HotelCategory::class, $id, $user);
		$floors = $this->getTablesQuery(HotelFloor::class, $id, $user);
		$rates = $this->getTablesQuery(HotelRate::class, $id, $user);

		return response()->json([
			'success'    => true,
			'rates'      => $rates,
			'floors'     => $floors,
			'categories' => $categories
		], 200);
	}

	private function getTablesQuery($model, $id, $user)
	{
		return $model::where('active', true)
			->when($id > 0 || $user->type !== 'admin', function ($query) use ($id, $user) {
				$query->where('establishment_id', $id > 0 ? $id : $user->establishment_id);
			})
			->orderBy('description')
			->get();
	}

	public function myRates($roomId)
	{
		$myRates = HotelRoomRate::with('rate')
			->where('hotel_room_id', $roomId)
			->get();

		return response()->json([
			'success'      => true,
			'room_rates'   => $myRates,
		], 200);
	}

	public function addRateToRoom(HotelAddRateToRoomRequest $request, $roomId)
	{
		$roomRate = HotelRoomRate::create($request->only('hotel_room_id', 'hotel_rate_id', 'price'));
		$roomRate->load('rate');

		return response()->json([
			'success'     => true,
			'room_rate'   => $roomRate,
		], 200);
	}

	public function deleteRoomRate($roomId, $roomRateId)
	{
		HotelRoomRate::where('hotel_room_id', $roomId)
			->where('id', $roomRateId)
			->delete();

		return response()->json([
			'success'     => true,
			'message'     => 'Información actualizada',
		], 200);
	}

	/**
	 * Eliminar el registro de alquiler de una habitación ocupada
	 * @param int $id
	 * @return Response
	 */
	public function deleteRecord($id)
	{
		try {
			\Illuminate\Support\Facades\DB::connection('tenant')->beginTransaction();

			$activeRent = HotelRent::findOrFail($id);

			// Desvincular documentos y sale notes que apunten a este rent
			// (no se pueden borrar porque son comprobantes ya emitidos, pero
			// sí podemos romper la FK para permitir eliminar el alquiler).
			\App\Models\Tenant\Document::where('hotel_rent_id', $id)
				->update(['hotel_rent_id' => null]);
			\App\Models\Tenant\SaleNote::where('hotel_rent_id', $id)
				->update(['hotel_rent_id' => null]);

			// Eliminar pagos de los items
			foreach ($activeRent->items as $item) {
				if ($item->payments) {
					$item->payments()->delete();
				}
			}

			// Eliminar items relacionados con el alquiler
			$activeRent->items()->delete();

			// Eliminar órdenes relacionadas con el alquiler
			$activeRent->orders()->delete();

			// Eliminar entradas del historial de cambios (si existen)
			\Modules\Hotel\Models\HotelRentChange::where('hotel_rent_id', $id)->delete();

			// Obtener la habitación para cambiar su estado
			$room = HotelRoom::find($activeRent->hotel_room_id);
			if ($room) {
				$room->status = 'DISPONIBLE';
				$room->save();
			}

			// Eliminar el registro de alquiler
			$activeRent->delete();

			\Illuminate\Support\Facades\DB::connection('tenant')->commit();

			return response()->json([
				'success' => true,
				'message' => 'Registro de alquiler eliminado exitosamente. La habitación ahora está disponible.'
			], 200);

		} catch (\Throwable $th) {
			\Illuminate\Support\Facades\DB::connection('tenant')->rollBack();
			return response()->json([
				'success' => false,
				'message' => 'Ocurrió un error al eliminar el registro: ' . $th->getMessage()
			], 500);
		}
	}
}
