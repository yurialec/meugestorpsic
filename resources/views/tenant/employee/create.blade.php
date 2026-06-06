@extends('layouts.app_tenant_admin')
@section('content')
    <tenant-users-create-component
        url-employees="{{ route('tenant.employee.index', session('tenant_domain')) }}">
    </tenant-users-create-component>
@endsection