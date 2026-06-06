@extends('layouts.app_admin')
@section('content')
    <interaction-edit-component
        :id="{{$id}}"
        url-index-interaction="{{ route('interactions.index') }}">
    </interaction-edit-component>
@endsection
