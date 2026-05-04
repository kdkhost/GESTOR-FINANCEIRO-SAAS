@extends('layouts.admin.app')
@section('titulo', 'Saude Financeira')
@section('titulo_pagina', 'Score de Saude Financeira')
@section('breadcrumb')
    <li class="breadcrumb-item">Relatorios</li>
    <li class="breadcrumb-item active">Saude Financeira</li>
@endsection
@section('conteudo')
<div class="row mb-3">
    <div class="col-md-4">
        <div class="input-group">
            <select id="sel-mes" class="form-select">
                @foreach(['Janeiro','Fevereiro','Marco','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'] as $i => $m)
                    <option value="{{ $i+1 }}" {{ ($i+1)==date('n')?'selected':'' }}>{{ $m }}</option>
                @endforeach
            </select>
            <input type="number" id="sel-ano" class="form-control" value="{{ date('Y') }}" min="2020" max="2030">
            <button class="btn btn-primary" id="btn-calcular"><i class="bi bi-calculator me-1"></i>Calcular</button>
        </div>
    </div>
</div>

<div id="resultado-saude" style="display:none;">
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card card-outline card-primary text-center">
                <div class="card-body py-4">
                    <div id="score-circle" class="mx-auto mb-3" style="width:120px;height:120px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2.5rem;font-weight:700;color:#fff;background:#3b82f6;">
                        <span id="score-valor">0</span>
                    </div>
                    <h5 class="fw-bold" id="score-label">Calculando...</h5>
                    <p class="text-muted small">Score de Saude Financeira</p>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card card-outline card-primary h-100">
                <div class="card-header"><h3 class="card-title mb-0"><i class="bi bi-bar-chart me-2"></i>Fatores</h3></div>
                <div class="card-body" id="fatores-lista"></div>
            </div>
        </div>
    </div>
    <div class="card card-outline card-warning" id="card-recomendacoes" style="display:none;">
        <div class="card-header"><h3 class="card-title mb-0"><i class="bi bi-lightbulb me-2 text-warning"></i>Recomendacoes</h3></div>
        <div class="card-body" id="recomendacoes-lista"></div>
    </div>
</div>
@endsection
@push('scripts')
<script>
const coresScore = {Excelente:'#22c55e',Boa:'#3b82f6',Atencao:'#f59e0b',Critica:'#ef4444',Emergencial:'#7f1d1d'};

$('#btn-calcular').on('click', function() {
    mostrarLoading();
    $.get('{{ route("admin.dashboard.saude") }}', {mes:$('#sel-mes').val(), ano:$('#sel-ano').val()}, function(r) {
        ocultarLoading();
        if (!r.sucesso) { toast('Erro ao calcular.','erro'); return; }
        const s = r.saude;
        $('#resultado-saude').show();

        // Score
        const cor = coresScore[s.classificacao] || '#3b82f6';
        $('#score-circle').css('background', cor);
        $('#score-valor').text(s.indice);
        $('#score-label').text(s.classificacao);

        // Fatores
        const fl = $('#fatores-lista'); fl.empty();
        Object.values(s.fatores).forEach(f => {
            const perc = Math.round((f.pontos/f.maximo)*100);
            fl.append(`
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="fw-medium">${f.nome}</span>
                        <span>${f.pontos}/${f.maximo} pts — ${f.detalhes}</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar ${perc>=80?'bg-success':perc>=50?'bg-primary':perc>=25?'bg-warning':'bg-danger'}" style="width:${perc}%"></div>
                    </div>
                </div>
            `);
        });

        // Recomendacoes
        if (s.recomendacoes && s.recomendacoes.length) {
            $('#card-recomendacoes').show();
            const rl = $('#recomendacoes-lista'); rl.empty();
            s.recomendacoes.forEach(rec => {
                rl.append(`<div class="d-flex align-items-start gap-2 mb-2"><i class="bi bi-arrow-right-circle-fill text-warning mt-1"></i><span>${rec}</span></div>`);
            });
        }
    }).fail(()=>{ocultarLoading();toast('Erro ao calcular saude financeira.','erro');});
});

$('#btn-calcular').trigger('click');
</script>
@endpush