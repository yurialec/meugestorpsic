@extends('layouts.app_tenant_admin')
@section('content')
    <consultation-index-component
        url-show-consultation="{{ route('tenant.consultation.show', ['tenant' => session('tenant_domain'), 'id' => '_id']) }}">
    </consultation-index-component>
@endsection