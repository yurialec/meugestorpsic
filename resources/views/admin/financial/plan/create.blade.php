@extends('layouts.app_admin')
@section('content')
    <financial-plans-create-component
        url-index-plans="{{ route('plan.index') }}">
    </financial-plans-create-component>
@endsection
