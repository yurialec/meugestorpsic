@extends('layouts.app_tenant_admin')
@section('content')
    <tenant-patient-index-component
        url-anamnese="{{ route('tenant.anamnese.index', ['tenant' => session('tenant_domain'), 'patient_id' => '_patient_id']) }}"
        url-create-patient="{{ route('tenant.patient.create', ['tenant' => session('tenant_domain')]) }}"
        url-edit-patient="{{ route('tenant.patient.edit', ['tenant' => session('tenant_domain'), 'id' => '_id']) }}"
        url-download-pdf="{{ route('tenant.medicalRecord.pdf', ['tenant' => session('tenant_domain'), 'patient_id' => '_patient_id']) }}"
        url-download-pdf-prontuario="{{ route('tenant.medicalRecord.pdf.prontuario', ['tenant' => session('tenant_domain'), 'patient_id' => '_patient_id']) }}"
        >
    </tenant-patient-index-component>
@endsection
