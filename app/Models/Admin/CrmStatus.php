<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CrmStatus extends Model
{
    protected $table = 'crm_statuses';

    const TESTE_GRATIS = 1;
    const INTERESSE = 2;
    const LIGACAO = 3;
    const CONTRATO_FECHADO = 4;
    const CADASTRO_DADOS = 5;
    const IMPLANTACAO = 6;
    const PAGO = 7;
    const CONTATO_FUTURO = 8;
    const CONTATO_PERDIDO = 9;
    const RECONQUISTA = 10;

    protected $fillable = [
        'name',
        'color',
        'is_active'
    ];
}