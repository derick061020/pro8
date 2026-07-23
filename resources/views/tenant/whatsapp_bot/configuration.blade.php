@extends('tenant.layouts.app')

@section('content')
    <tenant-whatsapp-bot-configuration :configuration='@json($configuration)'></tenant-whatsapp-bot-configuration>
@endsection
