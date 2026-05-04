@extends('layouts.admin.app')

@section('titulo', 'Saúde Financeira')
@section('titulo_pagina', 'Saúde Financeira')

@section('breadcrumb')
    <li class="breadcrumb-item active">Saúde Financeira</li>
@endsection

@section('conteudo')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-outline card-primary text-center py-5">
            <div class="card-body">
                <i class="bi bi-heart-pulse text-primary" style="font-size:4rem;"></i>
                <h3 class="mt-3 fw-bold">Saúde Financeira</h3>
                <p class="text-muted">Score e indicadores de saúde financeira.</p>
                <span class="badge bg-warning text-dark">Em desenvolvimento</span>
            </div>
        </div>
    </div>
</div>
@endsection