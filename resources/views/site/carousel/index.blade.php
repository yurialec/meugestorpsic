@extends('layouts.app_admin')
@section('content')
<site-carrousel-index-component
    url-update-carousel="{{ route('site.carousel.edit', ['id' => '_id']) }}"
    url-create-carousel="{{ route('site.carousel.create') }}">
</site-carrousel-index-component>
@endsection