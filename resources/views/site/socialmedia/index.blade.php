@extends('layouts.app_admin')
@section('content')
<site-social-media-index-component
    url-create-social-media="{{ route('site.socialmedia.create') }}">
</site-social-media-index-component>
@endsection