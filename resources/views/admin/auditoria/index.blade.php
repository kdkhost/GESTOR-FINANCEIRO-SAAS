@extends('layouts.admin.app')

@section('titulo', 'Auditoria')
@section('titulo_pagina', 'Auditoria')

@section('breadcrumb')
    <li class="breadcrumb-item active">Auditoria</li>
@endsection

@section('conteudo')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-outline card-primary text-center py-5">
            <div class="card-body">
                <i class="bi bi-journal-text text-primary" style="font-size:4rem;"></i>
                <h3 class="mt-3 fw-bold">Auditoria</h3>
                <p class="text-muted">Visualize o log completo de ações realizadas no sistema.</p>
                <span class="badge bg-warning text-dark">Em desenvolvimento</span>
            </div>
        </div>
    </div>
</div>
@endsection