<?php

namespace App\Models\Admin;

use App\Models\Tenants\ClientConfig;
use App\Models\Tenants\Schedule;
use App\Models\Tenants\Tenant;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Clients extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'tenant_id',
        'cpf',
        'function',
        'phone',
        'token',
        'token_expiration',
        'crp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'client_id');
    }
    
    public function config()
    {
        return $this->hasOne(ClientConfig::class, 'client_id');
    }
}