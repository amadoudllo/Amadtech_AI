<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérifiez votre email</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            color: #1a1a1a;
            font-size: 20px;
            margin-top: 0;
        }
        .content p {
            color: #666;
            margin: 15px 0;
        }
        .verification-button {
            display: inline-block;
            background-color: #f97316;
            color: white;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 25px 0;
            transition: background-color 0.3s;
        }
        .verification-button:hover {
            background-color: #ea580c;
        }
        .verification-link {
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 6px;
            word-break: break-all;
            color: #f97316;
            font-size: 12px;
            margin: 20px 0;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px 30px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #999;
            text-align: center;
        }
        .expires {
            color: #e74c3c;
            font-weight: 600;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Vérifiez votre email</h1>
        </div>

        <div class="content">
            <h2>Bienvenue, <?php echo e($user->name); ?>!</h2>
            
            <p>Merci de vous être inscrit chez Amadtech_AI. Pour accéder à votre compte, veuillez vérifier votre adresse email en cliquant sur le bouton ci-dessous:</p>

            <center>
                <a href="<?php echo e($verificationUrl); ?>" class="verification-button">
                    Vérifier mon email
                </a>
            </center>

            <p>Ou copiez et collez ce lien dans votre navigateur:</p>
            
            <div class="verification-link">
                <?php echo e($verificationUrl); ?>

            </div>

            <div class="expires">
                ⏰ Ce lien expire dans 24 heures
            </div>

            <p>Si vous n'avez pas créé ce compte, vous pouvez ignorer cet email en toute sécurité.</p>

            <p>
                Cordialement,<br>
                <strong>L'équipe Amadtech_AI</strong>
            </p>
        </div>

        <div class="footer">
            <p>Amadtech_AI - Assistant IA Intelligent<br>
            © 2025 Amadtech_AI. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Amadtech_AI\resources\views/emails/verify-email.blade.php ENDPATH**/ ?>