@extends('layouts.app_admin')
@section('content')
    <interaction-index-component
        url-create-interaction="{{ route('interactions.create') }}">
    </interaction-index-component>
@endsection
