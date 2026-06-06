@extends('layouts.app_admin')
@section('content')
    <financial-plans-edit-component
        :id={{$id}}
        url-index-plans="{{ route('plan.index') }}">
    </financial-plans-edit-component>
@endsection