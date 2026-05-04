<?php

namespace App\Modules\Usuarios\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'telefone',
        'avatar',
        'status',
        'tipo',
        'dois_fatores',
        'dois_fatores_secret',
        'tentativas_login',
        'bloqueado_ate',
        'ultimo_ip',
        'ultimo_user_agent',
        'ultimo_acesso_em',
        'timezone',
        'locale',
        'preferencias',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'dois_fatores_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'dois_fatores'       => 'boolean',
            'tentativas_login'   => 'integer',
            'bloqueado_ate'      => 'datetime',
            'ultimo_acesso_em'   => 'datetime',
            'preferencias'       => 'array',
            'deleted_at'         => 'datetime',
        ];
    }

    // -------------------------------------------------------
    // Atributos calculados
    // -------------------------------------------------------

    public function getIsSuperadminAttribute(): bool
    {
        return $this->tipo === 'superadmin';
    }

    public function getIsAdminAttribute(): bool
    {
        return in_array($this->tipo, ['admin', 'superadmin']);
    }

    public function getEstaBloqueadoAttribute(): bool
    {
        if (! $this->bloqueado_ate) {
            return false;
        }
        return $this->bloqueado_ate->isFuture();
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return asset('images/avatar-padrao.png');
    }

    // -------------------------------------------------------
    // Relacionamentos
    // -------------------------------------------------------

    public function sessoesAtivas(): HasMany
    {
        return $this->hasMany(\App\Modules\Usuarios\Models\SessaoAtiva::class);
    }

    public function contasBancarias(): HasMany
    {
        return $this->hasMany(\App\Modules\Financeiro\Models\ContaBancaria::class);
    }

    public function categorias(): HasMany
    {
        return $this->hasMany(\App\Modules\Financeiro\Models\Categoria::class);
    }

    public function contasPagar(): HasMany
    {
        return $this->hasMany(\App\Modules\Financeiro\Models\ContaPagar::class);
    }

    public function contasReceber(): HasMany
    {
        return $this->hasMany(\App\Modules\Financeiro\Models\ContaReceber::class);
    }

    public function receitas(): HasMany
    {
        return $this->hasMany(\App\Modules\Financeiro\Models\Receita::class);
    }

    public function despesas(): HasMany
    {
        return $this->hasMany(\App\Modules\Financeiro\Models\Despesa::class);
    }

    public function transferencias(): HasMany
    {
        return $this->hasMany(\App\Modules\Financeiro\Models\Transferencia::class);
    }

    public function metas(): HasMany
    {
        return $this->hasMany(\App\Modules\Financeiro\Models\Meta::class);
    }

    public function notificacoes(): HasMany
    {
        return $this->hasMany(\App\Modules\Auditoria\Models\Notificacao::class);
    }

    // -------------------------------------------------------
    // Scopes
    // -------------------------------------------------------

    public function scopeAtivo($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopeSuperadmins($query)
    {
        return $query->where('tipo', 'superadmin');
    }

    // -------------------------------------------------------
    // Métodos utilitários
    // -------------------------------------------------------

    public function incrementarTentativasLogin(): void
    {
        $this->increment('tentativas_login');
        $maxTentativas = (int) config('auth.max_tentativas_login', 5);
        if ($this->tentativas_login >= $maxTentativas) {
            $this->update(['bloqueado_ate' => now()->addMinutes(15)]);
        }
    }

    public function resetarTentativasLogin(): void
    {
        $this->update([
            'tentativas_login' => 0,
            'bloqueado_ate'    => null,
        ]);
    }

    public function registrarAcesso(string $ip, string $userAgent): void
    {
        $this->update([
            'ultimo_ip'         => $ip,
            'ultimo_user_agent' => $userAgent,
            'ultimo_acesso_em'  => now(),
        ]);
        $this->resetarTentativasLogin();
    }
}
