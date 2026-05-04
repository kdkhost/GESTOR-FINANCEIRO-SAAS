@extends('layouts.admin.app')

@section('titulo', 'Meu Perfil')
@section('titulo_pagina', 'Meu Perfil')

@section('breadcrumb')
    <li class="breadcrumb-item active">Perfil</li>
@endsection

@section('conteudo')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-outline card-primary text-center py-5">
            <div class="card-body">
                <i class="bi bi-person-circle text-primary" style="font-size:4rem;"></i>
                <h3 class="mt-3 fw-bold">Meu Perfil</h3>
                <p class="text-muted">Gerencie suas informações pessoais e senha.</p>
                <span class="badge bg-warning text-dark">Em desenvolvimento</span>
            </div>
        </div>
    </div>
</div>
@endsection