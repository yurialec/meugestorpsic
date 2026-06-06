@extends('layouts.app_tenant_admin')
@section('content')
    <tenant-patient-anamnese-form-component
        patient_id="{{ $patient_id }}"
        url-index-patients="{{ route('tenant.patient.index', ['tenant' => session('tenant_domain')]) }}">
    </tenant-patient-anamnese-form-component>
@endsection