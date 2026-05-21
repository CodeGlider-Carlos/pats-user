@extends('portal.layout')

@section('title', $seccion)

@section('content')

<div class="page-header">
    <h1>{{ $seccion }}</h1>
</div>

<div class="prox-banner">
    <span class="prox-tag">Próximamente</span>
    <i class="mdi mdi-rocket-launch-outline"></i>
    <h2>Estamos trabajando en esto</h2>
    <p>La sección <strong>{{ $seccion }}</strong> estará disponible muy pronto. Te notificaremos cuando esté lista.</p>
</div>

@endsection
