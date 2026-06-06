@extends('layouts.app_admin')
@section('content')
<site-logo-index-component
    url-update-logo="{{ route('site.logo.edit', ['id' => '_id']) }}"
    url-create-logo="{{ route('site.logo.create') }}">>
</site-logo-index-component>
@endsection