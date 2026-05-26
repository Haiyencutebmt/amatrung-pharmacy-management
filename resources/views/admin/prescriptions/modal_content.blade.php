@php
    $printType = ($printType ?? request('type')) === 'internal' ? 'internal' : 'patient';
@endphp

@include('admin.prescriptions.partials.document', [
    'prescription' => $prescription,
    'printType' => $printType,
])
