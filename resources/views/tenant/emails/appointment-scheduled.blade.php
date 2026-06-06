<!DOCTYPE html>
<html>
<head>
    <title>Confirmação de Agendamento</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 10px; text-align: center; }
        .content { padding: 20px; }
        .footer { margin-top: 20px; padding: 10px; text-align: center; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Confirmação de Agendamento</h2>
        </div>
        
        <div class="content">
            <p>Olá {{ $patient->name }},</p>
            
            <p>Seu agendamento foi confirmado com sucesso!</p>
            
            <p><strong>Detalhes do agendamento:</strong></p>
            <ul>
                <li><strong>Data:</strong> {{ \Carbon\Carbon::parse($appointment->day)->format('d/m/Y') }}</li>
                <li><strong>Hora:</strong> {{ $appointment->hour }}</li>
            </ul>
            
            <p>Caso precise remarcar ou cancelar, por favor entre em contato conosco.</p>
        </div>
        
        <div class="footer">
            <p>Este é um e-mail automático, por favor não responda.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>