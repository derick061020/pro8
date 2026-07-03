@extends('system.layouts.app')

@section('content')

    <system-clients-index :delete-permission="{{json_encode($delete_permission)}}"
                          :disc-used="{{json_encode($disc_used)}}"
                          :i-used="{{json_encode($i_used)}}"
                          :storage-size="{{json_encode($storage_size)}}"
                          :version="{{json_encode($version)}}"
                          :can-create-clients="@json(auth('admin')->user()->canCreateClients())"></system-clients-index>

@endsection
