<?php

namespace App\Models\Tenants;

use App\Models\Admin\Clients;
use App\Models\Admin\PaymentBoleto;
use App\Models\Admin\PaymentIntent;
use App\Models\Admin\Plan;
use App\Models\Admin\Subscription;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $table = 'tenant';
    protected $fillable = [
        'domain',
        'type',
        'logo',
        'user_limit',
    ];

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function client()
    {
        return $this->hasOne(Clients::class);
    }

    public function address()
    {
        return $this->hasOne(Address::class);
    }

    public function paymentIntents()
    {
        return $this->hasMany(PaymentIntent::class, 'tenant_id');
    }

    public function paymentBoletos()
    {
        return $this->hasMany(PaymentBoleto::class, 'tenant_id');
    }
}
