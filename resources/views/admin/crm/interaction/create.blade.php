@extends('layouts.app_admin')
@section('content')
<interaction-create-component
    url-index-interaction="{{ route('interaction.index') }}">
</interaction-create-component>
@endsection