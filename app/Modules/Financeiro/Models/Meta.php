<?php
namespace App\Modules\Financeiro\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Meta extends Model {
    use HasFactory, SoftDeletes;
    protected $table = 'metas_financeiras';
    protected $fillable = [
        'user_id','titulo','descricao','valor_alvo','valor_atual',
        'data_inicio','data_prazo','conta_bancaria_id','status','icone','cor'
    ];
    protected function casts(): array {
        return [
            'valor_alvo'   => 'decimal:2',
            'valor_atual'  => 'decimal:2',
            'data_inicio'  => 'date',
            'data_prazo'   => 'date',
            'deleted_at'   => 'datetime',
        ];
    }
    public function scopeDoUsuario($q, int $id) { return $q->where('user_id', $id); }
    public function getPercentualAttribute(): float {
        return $this->valor_alvo > 0 ? min(100, ($this->valor_atual / $this->valor_alvo) * 100) : 0;
    }
    public function contaBancaria() { return $this->belongsTo(ContaBancaria::class); }
}