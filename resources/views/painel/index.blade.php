@extends('layouts.admin.app')

@section('titulo', 'Painel')
@section('titulo_pagina', 'Meu Painel')

@section('breadcrumb')
    <li class="breadcrumb-item active">Painel</li>
@endsection

@section('conteudo')
<div class="row g-3">
    <div class="col-xl-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title mb-0"><i class="bi bi-house-door me-2"></i>Bem-vindo, {{ $usuario->name }}!</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Este e o seu painel pessoal. Entre em contato com o administrador para mais funcionalidades.
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                                    <i class="bi bi-person fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">Meu Perfil</h6>
                                <a href="{{ route('admin.perfil') }}" class="small text-primary">Editar perfil</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <div class="flex-shrink-0">
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                                    <i class="bi bi-envelope fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">{{ $usuario->email }}</h6>
                                <span class="small text-muted">E-mail cadastrado</span>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="mb-3">Informacoes da Conta</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted" style="width:150px;">Tipo:</td>
                            <td><span class="badge bg-primary">{{ ucfirst($usuario->tipo) }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status:</td>
                            <td><span class="badge bg-success">{{ ucfirst($usuario->status) }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Ultimo acesso:</td>
                            <td>{{ $usuario->ultimo_acesso_em ? $usuario->ultimo_acesso_em->format('d/m/Y H:i') : 'Nunca' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Cadastrado em:</td>
                            <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title mb-0"><i class="bi bi-lightning me-2"></i>Acoes Rapidas</h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.perfil') }}" class="btn btn-outline-primary">
                        <i class="bi bi-person-gear me-2"></i>Editar Perfil
                    </a>
                    <a href="{{ route('admin.perfil') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-key me-2"></i>Alterar Senha
                    </a>
                    <form action="{{ route('auth.logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-box-arrow-right me-2"></i>Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
