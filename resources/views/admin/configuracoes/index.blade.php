@extends('layouts.admin.app')

@section('titulo', 'Configurações')
@section('titulo_pagina', 'Configurações')

@section('breadcrumb')
    <li class="breadcrumb-item active">Configurações</li>
@endsection

@section('conteudo')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-outline card-primary text-center py-5">
            <div class="card-body">
                <i class="bi bi-gear-wide-connected text-primary" style="font-size:4rem;"></i>
                <h3 class="mt-3 fw-bold">Configurações</h3>
                <p class="text-muted">Configure as preferências do sistema, SMTP, aparência e integrações.</p>
                <span class="badge bg-warning text-dark">Em desenvolvimento</span>
            </div>
        </div>
    </div>
</div>
@endsection