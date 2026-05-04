@extends('layouts.admin.app')

@section('titulo', 'Fluxo de Caixa')
@section('titulo_pagina', 'Fluxo de Caixa')

@section('breadcrumb')
    <li class="breadcrumb-item active">Fluxo de Caixa</li>
@endsection

@section('conteudo')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-outline card-primary text-center py-5">
            <div class="card-body">
                <i class="bi bi-cash-stack text-primary" style="font-size:4rem;"></i>
                <h3 class="mt-3 fw-bold">Fluxo de Caixa</h3>
                <p class="text-muted">Relatório de entradas e saídas por período.</p>
                <span class="badge bg-warning text-dark">Em desenvolvimento</span>
            </div>
        </div>
    </div>
</div>
@endsection