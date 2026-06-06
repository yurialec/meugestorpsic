@extends('layouts.app_tenant_admin')
@section('content')
    <appointment-index-component
        url-open-consultation="{{ route('tenant.consultation.show', ['tenant' => session('tenant_domain'), 'id' => '_id']) }}"
        url-configuration="{{ route('tenant.profile', ['tenant' => session('tenant_domain')]) }}">
    </appointment-index-component>
@endsection