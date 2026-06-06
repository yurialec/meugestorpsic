@extends('layouts.app_admin')
@section('content')
    <crm-clients-index-component
        url-create-client="{{ route('clients.create') }}">
    </crm-clients-index-component>
@endsection
