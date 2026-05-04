@extends('layouts.admin.app')

@section('titulo', 'Inadimplência')
@section('titulo_pagina', 'Inadimplência')

@section('breadcrumb')
    <li class="breadcrumb-item active">Inadimplência</li>
@endsection

@section('conteudo')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-outline card-primary text-center py-5">
            <div class="card-body">
                <i class="bi bi-exclamation-triangle text-primary" style="font-size:4rem;"></i>
                <h3 class="mt-3 fw-bold">Inadimplência</h3>
                <p class="text-muted">Relatório de contas vencidas e inadimplência.</p>
                <span class="badge bg-warning text-dark">Em desenvolvimento</span>
            </div>
        </div>
    </div>
</div>
@endsection