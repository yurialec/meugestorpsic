@extends('layouts.app_tenant_admin')
@section('content')
    <tenant-employee-edit-component
        id="{{ $id }}"
        url-employees="{{ route('tenant.employee.index', session('tenant_domain')) }}">
    </tenant-employee-edit-component>
@endsection