@extends('layouts.app_admin')
@section('content')
    <status-index-component
        url-create-status="{{ route('status.create') }}">
    </status-index-component>
@endsection
