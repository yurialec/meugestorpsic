@extends('layouts.app_admin')
@section('content')
<site-about-index-component
    url-update-about="{{ route('site.about.edit', ['id' => '_id']) }}"
    url-create-about="{{ route('site.about.create') }}">
</site-about-index-component>
@endsection