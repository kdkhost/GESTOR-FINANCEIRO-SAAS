<?php
namespace App\Modules\Financeiro\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Receita extends Model {
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id','conta_bancaria_id','categoria_id','subcategoria_id','cliente_id','descricao','valor','data_receita','forma_pagamento_id','numero_documento','observacoes','recorrente','recorrencia_id'];
    protected function casts(): array { return ['valor'=>'decimal:2','data_receita'=>'date','recorrente'=>'boolean','deleted_at'=>'datetime']; }
    public function categoria() { return $this->belongsTo(Categoria::class); }
    public function contaBancaria() { return $this->belongsTo(ContaBancaria::class); }
    public function cliente() { return $this->belongsTo(Cliente::class); }
    public function scopeDoUsuario($q, int $id) { return $q->where('user_id', $id); }
    public function scopePorPeriodo($q, $i, $f) { return $q->whereBetween('data_receita', [$i, $f]); }
}