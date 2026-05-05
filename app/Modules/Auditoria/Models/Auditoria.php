<?php

namespace App\Modules\Auditoria\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Usuarios\Models\User;

class Auditoria extends Model
{
    protected $table = 'auditoria';
    
    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'acao',
        'entidade',
        'entidade_id',
        'dados_anteriores',
        'dados_novos',
        'ip',
        'user_agent',
        'url',
        'metodo',
        'observacao'
    ];
    
    protected $casts = [
        'dados_anteriores' => 'array',
        'dados_novos' => 'array',
        'created_at' => 'datetime'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function getAcaoFormatadaAttribute()
    {
        $cores = [
            'criar' => 'success',
            'atualizar' => 'primary',
            'excluir' => 'danger',
            'login' => 'info',
            'logout' => 'secondary',
            'erro' => 'warning',
            'visualizar' => 'light'
        ];
        
        return [
            'label' => ucfirst($this->acao),
            'cor' => $cores[$this->acao] ?? 'secondary'
        ];
    }
    
    public function getEntidadeFormatadaAttribute()
    {
        $entidades = [
            'cliente' => 'Cliente',
            'fornecedor' => 'Fornecedor',
            'conta_pagar' => 'Conta a Pagar',
            'conta_receber' => 'Conta a Receber',
            'usuario' => 'Usuario',
            'configuracao' => 'Configuracao',
            'empresa' => 'Empresa',
            'assinatura' => 'Assinatura',
            'fatura' => 'Fatura',
            'plano' => 'Plano',
            'gateway' => 'Gateway',
            'modulo' => 'Modulo',
            'permissao' => 'Permissao',
            'categoria' => 'Categoria',
            'conta_bancaria' => 'Conta Bancaria'
        ];
        
        return $entidades[$this->entidade] ?? ucfirst($this->entidade);
    }
    
    public static function registrar($dados)
    {
        $request = request();
        
        return self::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?? 'Sistema',
            'user_email' => auth()->user()?->email ?? 'sistema@localhost',
            'acao' => $dados['acao'] ?? 'outro',
            'entidade' => $dados['entidade'] ?? 'sistema',
            'entidade_id' => $dados['entidade_id'] ?? null,
            'dados_anteriores' => $dados['dados_anteriores'] ?? null,
            'dados_novos' => $dados['dados_novos'] ?? null,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'metodo' => $request->method(),
            'observacao' => $dados['observacao'] ?? null
        ]);
    }
}
