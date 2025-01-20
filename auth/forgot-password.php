<?php
session_start();
require 'db.php';
require '../vendor/autoload.php';



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_code'])) {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        
        $stmt = $pdo->prepare('SELECT id FROM user WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $code = sprintf("%06d", mt_rand(0, 999999));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            
            $stmt = $pdo->prepare('INSERT INTO password_resets (email, code, expiry) VALUES (?, ?, ?)');
            $stmt->execute([$email, $code, $expiry]);
            
            
            $mail = new PHPMailer(true);
            try {
                
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; 
                $mail->SMTPAuth = true;
                $mail->Username = 'houssembouallagui1@gmail.com'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                $mail->setFrom('houssembouallagui1@gmail.com', 'Houssem');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Code de reinitialisation de mot de passe';
                $mail->Body = "Votre code de reinitialisation est : <b>{$code}</b><br>Ce code expirera dans 1 heure.";


                $mail->send();
                $_SESSION['reset_email'] = $email;
                $message = 'Un code de réinitialisation a été envoyé à votre adresse email.';
                $messageType = 'success';
            } catch (Exception $e) {
                $message = "L'envoi de l'email a échoué. Erreur: {$mail->ErrorInfo}";
                $messageType = 'error';
            }
        } else {
            $message = "Aucun compte n'est associé à cette adresse email.";
            $messageType = 'error';
        }
    }

    if (isset($_POST['verify_code'])) {
        $code = $_POST['code'];
        $email = $_SESSION['reset_email'];
        
        $stmt = $pdo->prepare('SELECT * FROM password_resets WHERE email = ? AND code = ? AND used = 0 AND expiry > NOW() ORDER BY created_at DESC LIMIT 1');
        $stmt->execute([$email, $code]);
        
        if ($row = $stmt->fetch()) {
            $updateStmt = $pdo->prepare('UPDATE password_resets SET used = 1 WHERE email = ? AND code = ?');
            $updateStmt->execute([$email, $code]);
            
            $_SESSION['can_reset_password'] = true;
            $_SESSION['reset_code'] = $code;
            header('Location: reset-password.php');
            exit;
        } else {
            $message = "Code invalide ou expiré.";
            $messageType = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap');

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Montserrat', sans-serif;
    }

    body {
        background-color: #c9d6ff;
        background: linear-gradient(to right, #e2e2e2, #c9d6ff);
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 20px;
    }

    .container {
        background-color: #fff;
        border-radius: 30px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.35);
        width: 100%;
        max-width: 500px;
        padding: 40px;
    }

    h1 {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
    }

    .form-group {
        margin-bottom: 20px;
    }

    input {
        width: 100%;
        padding: 12px 15px;
        background-color: #eee;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: background-color 0.3s ease;
    }

    input:focus {
        background-color: #e0e0e0;
    }

    button {
        width: 100%;
        padding: 12px;
        background-color: #EC5A13;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #d14f11;
    }

    .message {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
    }

    .message.success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .message.error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .back-link {
        display: block;
        text-align: center;
        margin-top: 20px;
        color: #EC5A13;
        text-decoration: none;
        font-size: 14px;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .step {
        display: none;
    }

    .step.active {
        display: block;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Réinitialisation du mot de passe</h1>

        <?php if ($message): ?>
        <div class="message <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="step <?= !isset($_SESSION['reset_email']) ? 'active' : '' ?>" id="step1">
            <form method="POST" action="">
                <div class="form-group">
                    <input type="email" name="email" placeholder="Votre adresse email" required>
                </div>
                <button type="submit" name="send_code">Envoyer le code</button>
            </form>
        </div>

        <div class="step <?= isset($_SESSION['reset_email']) ? 'active' : '' ?>" id="step2">
            <form method="POST" action="">
                <div class="form-group">
                    <input type="text" name="code" placeholder="Entrez le code reçu" required>
                </div>
                <button type="submit" name="verify_code">Vérifier le code</button>
            </form>
        </div>

        <a href="index.php" class="back-link">Retour à la connexion</a>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_SESSION['reset_email'])): ?>
        document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
        document.querySelector('#step2').classList.add('active');
        <?php else: ?>
        document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
        document.querySelector('#step1').classList.add('active');
        <?php endif; ?>
    });
    </script>
</body>

</html>