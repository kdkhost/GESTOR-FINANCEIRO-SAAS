<?php
namespace App\Modules\Financeiro\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Meta extends Model {
    use HasFactory, SoftDeletes;
    protected $table = 'metas';
    protected $fillable = ['user_id','nome','descricao','valor_meta','valor_atual','data_inicio','data_fim','tipo','status','cor','icone'];
    protected function casts(): array { return ['valor_meta'=>'decimal:2','valor_atual'=>'decimal:2','data_inicio'=>'date','data_fim'=>'date','deleted_at'=>'datetime']; }
    public function scopeDoUsuario($q, int $id) { return $q->where('user_id', $id); }
    public function getPercentualAttribute(): float { return $this->valor_meta > 0 ? min(100, ($this->valor_atual / $this->valor_meta) * 100) : 0; }
}