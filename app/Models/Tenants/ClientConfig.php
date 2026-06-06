<?php

namespace App\Models\Tenants;

use App\Models\Admin\Clients;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientConfig extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'client_config';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'consultation_price',
        'consultation_duration',
        'first_access',
    ];

    public function client()
    {
        return $this->belongsTo(Clients::class);
    }
}
