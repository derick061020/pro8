<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant\StatusOrder;

class StatusOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function records()
    {
        return response()->json(StatusOrder::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255'
        ]);

        $status = StatusOrder::create([
            'description' => $request->description
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado creado correctamente',
            'data' => $status
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:255'
        ]);

        $status = StatusOrder::findOrFail($id);
        $status->description = $request->description;
        $status->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'data' => $status
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $status = StatusOrder::findOrFail($id);

        // verificar que no tenga pedidos asociados
        if ($status->order()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar, tiene pedidos asociados'
            ]);
        }

        $status->delete();

        return response()->json([
            'success' => true,
            'message' => 'Estado eliminado correctamente'
        ]);
    }
}
