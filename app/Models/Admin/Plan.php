<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    const TESTE_GRATIS = 'Teste Grátis';
    const TRAIL = 10;

    protected $fillable = [
        'name',
        'price',
        'description',
        'billing_cycle',
        'payment_gateway_plan_code',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}