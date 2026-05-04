@extends('layouts.admin.app')

@section('titulo', 'DRE')
@section('titulo_pagina', 'DRE')

@section('breadcrumb')
    <li class="breadcrumb-item active">DRE</li>
@endsection

@section('conteudo')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-outline card-primary text-center py-5">
            <div class="card-body">
                <i class="bi bi-file-earmark-bar-graph text-primary" style="font-size:4rem;"></i>
                <h3 class="mt-3 fw-bold">DRE</h3>
                <p class="text-muted">Demonstrativo de Resultado do Exercício.</p>
                <span class="badge bg-warning text-dark">Em desenvolvimento</span>
            </div>
        </div>
    </div>
</div>
@endsection