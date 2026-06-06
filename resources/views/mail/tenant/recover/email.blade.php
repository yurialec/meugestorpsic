<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">

    <div
        style="max-width: 600px; margin: 30px auto; background-color: #ffffff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">

        <h1 style="color: #333333;">Olá, {{ $name }}</h1>

        <p style="font-size: 16px; color: #555555;">
            Você solicitou a recuperação de senha no dia {{ now()->format('d/m/Y \à\s H:i') }}.
            Clique no botão abaixo para redefinir sua senha:
        </p>

        <a href="{{ $resetLink }}"
            style="display: inline-block; margin-top: 20px; padding: 12px 25px; background-color: #007bff; color: #ffffff !important; text-decoration: none; border-radius: 5px; font-weight: bold;">
            Redefinir minha senha
        </a>

        <div style="margin-top: 30px; font-size: 12px; color: #999999; text-align: center;">
            Este link expira em 60 minutos.<br>
            Se você não solicitou essa alteração, ignore este e-mail.
        </div>

    </div>

</body>

</html>