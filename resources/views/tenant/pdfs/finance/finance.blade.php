<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Relatório Financeiro</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #333;">

    <!-- Cabeçalho -->
    <table width="100%" style="margin-bottom:30px; border-bottom:1px solid #ccc;">
        <tr>
            <td width="150" valign="top">
                @if($psychologistLogo)
                    <img src="{{ $psychologistLogo }}" alt="Logo"
                        style="max-width:120px; height:auto; border:1px solid #ddd; padding:5px; border-radius:4px;">
                @endif
            </td>
            <td valign="top">
                <h3 style="margin:0 0 5px 0;">{{ $client['name'] }}</h3>
                <p style="margin:2px 0;">CPF: {{ $client['cpf'] }}</p>
                <p style="margin:2px 0;">CRP: {{ $client['crp'] }}</p>
                <p style="margin:2px 0;">Telefone: {{ $client['phone'] }}</p>
            </td>
        </tr>
    </table>

    <h2 style="margin-bottom:15px;">
        Relatório Financeiro — {{ \Carbon\Carbon::now()->format('d/m/Y') }}
    </h2>

    @if(!empty($finance))
        <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse;">
            <thead>
                <tr style="background-color:#f2f2f2;">
                    <th align="left" style="border:1px solid #ccc;">Data do lançamento</th>
                    <th align="left" style="border:1px solid #ccc;">Paciente</th>
                    <th align="right" style="border:1px solid #ccc;">Valor (R$)</th>
                    <th align="left" style="border:1px solid #ccc;">Forma de Pagamento</th>
                    <th align="left" style="border:1px solid #ccc;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($finance as $f)
                    <tr>
                        <td style="border:1px solid #ccc;">
                            {{ $f['created_at'] }}
                        </td>
                        <td style="border:1px solid #ccc;">
                            {{ $f['patient']['full_name'] }}
                        </td>
                        <td align="right" style="border:1px solid #ccc;">
                            {{ number_format((float) $f['amount'], 2, ',', '.') }}
                        </td>
                        <td style="border:1px solid #ccc;">
                            {{ $f['payment_method']['label'] }}
                        </td>
                        <td style="border:1px solid #ccc;">
                            @if($f['status'] === 'open')
                                Aberto
                            @elseif($f['status'] === 'pending')
                                Pendente
                            @elseif($f['status'] === 'paid')
                                Pago
                            @elseif($f['status'] === 'free')
                                Grátis
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Nenhum registro financeiro encontrado.</p>
    @endif

</body>

</html>