<?php

namespace App\Enums;

enum RolesEnum: string
{

    case Desenvolvedor = 'Desenvolvedor';

    case Admin = 'Admin';

    case Auxiliar = 'Auxiliar';

    case Cliente = 'Cliente';
}