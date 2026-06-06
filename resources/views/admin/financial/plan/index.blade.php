@extends('layouts.app_admin')
@section('content')
    <financial-plans-index-component
        url-create-plan="{{ route('plan.create') }}">
    </financial-plans-index-component>
@endsection