@extends('system.layouts.app')

@section('content')

    <system-clients-index :can-create-clients="@json(auth('admin')->user()->canCreateClients())"></system-clients-index>

@endsection