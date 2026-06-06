@extends('layouts.app_admin')
@section('content')
<crm-clients-create-component
    url-index-client="{{ route('clients.index') }}">
</crm-clients-create-component>
@endsection