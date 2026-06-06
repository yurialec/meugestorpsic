@extends('layouts.app_tenant_admin')
@section('content')
    <configuration-index-component
        url-list-plans="{{  route('tenant.subscription.index', ['tenant' => session('tenant_domain')]) }}">
    </configuration-index-component>
@endsection