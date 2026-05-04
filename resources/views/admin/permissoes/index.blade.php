@extends('layouts.admin.app')

@section('titulo', 'Permissões')
@section('titulo_pagina', 'Permissões')

@section('breadcrumb')
    <li class="breadcrumb-item active">Permissões</li>
@endsection

@section('conteudo')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-outline card-primary text-center py-5">
            <div class="card-body">
                <i class="bi bi-shield-lock text-primary" style="font-size:4rem;"></i>
                <h3 class="mt-3 fw-bold">Permissões</h3>
                <p class="text-muted">Gerencie roles e permissões de acesso do sistema.</p>
                <span class="badge bg-warning text-dark">Em desenvolvimento</span>
            </div>
        </div>
    </div>
</div>
@endsection