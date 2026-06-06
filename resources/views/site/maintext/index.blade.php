@extends('layouts.app_admin')
@section('content')
<site-main-text-index-component
    url-update-main-text="{{ route('site.maintext.edit', ['id' => '_id']) }}"
    url-create-main-text="{{ route('site.maintext.create') }}">>
</site-main-text-index-component>
@endsection