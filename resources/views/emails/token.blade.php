<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seu Token de Acesso</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }
        .logo {
            max-width: 200px;
            height: auto;
            margin-bottom: 20px;
        }
        .content {
            padding: 20px 0;
        }
        .token-container {
            background-color: #f0f7ff;
            border: 1px solid #d1e3fa;
            border-radius: 6px;
            padding: 15px;
            margin: 25px 0;
            text-align: center;
        }
        .token {
            font-family: 'Courier New', monospace;
            font-size: 20px;
            font-weight: bold;
            color: #0066cc;
            letter-spacing: 1px;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #777;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 4px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ $message->embed(public_path('images/LogoMarcaCortada.png')) }}" alt="Logo" class="logo">
            <h1>Seu Token de Acesso</h1>
        </div>
        
        <div class="content">
            <p>Olá {{ $name }},</p>
            
            <p>Você solicitou um token de acesso para sua conta. Use o código abaixo para confirmar sua identidade:</p>
            
            <div class="token-container">
                <div class="token">{{ $token }}</div>
            </div>
            
            <p>Este token é válido por {{ $expiresInMinutes }} minutos a partir do momento em que este email foi enviado.</p>
            
            <p>Se você não solicitou este token, pode ignorar este email com segurança.</p>
            
            <p>Obrigado</p>
        </div>
        
        <div class="footer">
            <p>Este é um email automático, por favor não responda.</p>
            <p>&copy; {{ date('Y') }} Mobilidade Urbana. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html> 