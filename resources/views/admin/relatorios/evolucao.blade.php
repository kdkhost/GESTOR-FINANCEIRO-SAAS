@extends('layouts.admin.app')

@section('titulo', 'Evolução Mensal')
@section('titulo_pagina', 'Evolução Mensal')

@section('breadcrumb')
    <li class="breadcrumb-item active">Evolução</li>
@endsection

@section('conteudo')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-outline card-primary text-center py-5">
            <div class="card-body">
                <i class="bi bi-graph-up text-primary" style="font-size:4rem;"></i>
                <h3 class="mt-3 fw-bold">Evolução Mensal</h3>
                <p class="text-muted">Evolução financeira mês a mês.</p>
                <span class="badge bg-warning text-dark">Em desenvolvimento</span>
            </div>
        </div>
    </div>
</div>
@endsection