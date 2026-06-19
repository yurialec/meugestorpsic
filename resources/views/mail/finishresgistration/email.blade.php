<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalize seu Cadastro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #007bff;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }

        .content {
            padding: 20px;
            line-height: 1.6;
            color: #333333;
        }

        .button {
            display: inline-block;
            margin: 20px 0;
            padding: 12px 24px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            padding: 10px;
            background-color: #f9f9f9;
            color: #888888;
            font-size: 12px;
        }

        @media (max-width: 600px) {
            .email-container {
                border-radius: 0;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h1>Finalize seu Cadastro</h1>
        </div>
        <div class="content">
            <p>Olá {{ $name }},</p>
            <p>Obrigado por se cadastrar! Para finalizar seu cadastro, clique no botão abaixo:</p>
            <a href="https://meugestorpsic.com.br/faca-parte/finish-register/{{ $token }}" class="button">Finalizar
                Cadastro</a>
            <p>Se o botão acima não funcionar, copie e cole o link abaixo em seu navegador:</p>
            <p><a
                    href="https://meugestorpsic.com.br/cadastre-se/finish-register/{{ $token }}">https://meugestorpsic.com.br/cadastrar/finish-register/{{ $token }}</a>
            </p>
        </div>
        <div class="footer">
            <p>Você recebeu este email porque iniciou um cadastro em nosso sistema. Se não foi você, ignore este email.
            </p>
            <p>&copy; 2023 Sua Empresa. Todos os direitos reservados.</p>
        </div>
    </div>
</body>

</html>