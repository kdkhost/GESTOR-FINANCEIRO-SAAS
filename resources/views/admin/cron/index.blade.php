@extends('layouts.admin.app')

@section('titulo', 'Tarefas Agendadas')
@section('titulo_pagina', 'Tarefas Agendadas')

@section('breadcrumb')
    <li class="breadcrumb-item active">Cron</li>
@endsection

@section('conteudo')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-outline card-primary text-center py-5">
            <div class="card-body">
                <i class="bi bi-clock-history text-primary" style="font-size:4rem;"></i>
                <h3 class="mt-3 fw-bold">Tarefas Agendadas</h3>
                <p class="text-muted">Gerencie as tarefas agendadas do sistema.</p>
                <span class="badge bg-warning text-dark">Em desenvolvimento</span>
            </div>
        </div>
    </div>
</div>
@endsection