@extends('layouts.app_tenant_admin')
@section('content')
    <tenant-users-component url-create-employee="{{ route('tenant.employee.create', session('tenant_domain')) }}"
        url-edit-employee="{{ route('tenant.employee.edit', [session('tenant_domain'), 'id' => '_id']) }}">
    </tenant-users-component>
@endsection