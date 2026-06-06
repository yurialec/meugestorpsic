@extends('layouts.app_tenant_admin')
@section('content')
    <consultation-show-component :id="{{ $id }}"
        url-list-consultations="{{ route('tenant.consultation.index', ['tenant' => session('tenant_domain')]) }}">
    </consultation-show-component>
@endsection