@extends('tenant.layouts.app')

@section('content')
    <inventory-index :type-user="{{ json_encode(auth()->user()->type) }}"
        :has-transfer-permission="{{ json_encode(
            auth()->user()->levels()
                ->where('value', 'inventory_transfers')
                ->exists()
        ) }}"></inventory-index>

@endsection
