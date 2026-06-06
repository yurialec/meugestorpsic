@extends('layouts.app_admin')
@section('content')
    <status-create-component
        url-index-status="{{ route('status.index') }}">
    </status-create-component>
@endsection
