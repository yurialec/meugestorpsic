@extends('layouts.app_admin')
@section('content')
    <status-edit-component
        :id="{{$id}}"
        url-index-status="{{ route('status.index') }}">
    </status-edit-component>
@endsection
