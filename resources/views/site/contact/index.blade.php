@extends('layouts.app_admin')
@section('content')
<site-contact-index-component
    url-edit-contact="{{ route('site.contact.edit', ['id' => '_id']) }}"
    url-create-contact="{{ route('site.contact.create') }}">
</site-contact-index-component>
@endsection