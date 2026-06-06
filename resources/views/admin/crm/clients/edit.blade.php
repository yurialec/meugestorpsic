@extends('layouts.app_admin')
@section('content')
    <crm-clients-edit-component
        :id="{{$id}}"
        url-index-client="{{ route('clients.index') }}">
    </crm-clients-edit-component>
@endsection
