@extends('layouts.admin.app')

@section('titulo', 'Recorrências')
@section('titulo_pagina', 'Recorrências')

@section('breadcrumb')
    <li class="breadcrumb-item active">Recorrências</li>
@endsection

@section('conteudo')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="card-title mb-0">
                    <i class="bi bi-arrow-repeat me-2 text-primary"></i>Recorrências
                </h3>
                <button class="btn btn-primary btn-sm" id="btn-novo">
                    <i class="bi bi-plus-lg me-1"></i>Novo
                </button>
            </div>
            <div class="card-body">
                <p class="text-muted">Lançamentos recorrentes automáticos</p>
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="tabela-recorrencias">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nome</th>
                                <th>Status</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Nenhum registro encontrado.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection