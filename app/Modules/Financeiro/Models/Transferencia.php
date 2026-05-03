<?php
namespace App\Modules\Financeiro\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Transferencia extends Model {
    use HasFactory, SoftDeletes;
    protected $table = 'transferencias';
    protected $fillable = ['user_id','conta_origem_id','conta_destino_id','valor','data_transferencia','descricao','taxa'];
    protected function casts(): array { return ['valor'=>'decimal:2','taxa'=>'decimal:2','data_transferencia'=>'date','deleted_at'=>'datetime']; }
    public function contaOrigem() { return $this->belongsTo(ContaBancaria::class, 'conta_origem_id'); }
    public function contaDestino() { return $this->belongsTo(ContaBancaria::class, 'conta_destino_id'); }
    public function scopeDoUsuario($q, int $id) { return $q->where('user_id', $id); }
}