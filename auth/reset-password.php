<?php
session_start();
require 'db.php';

if (!isset($_SESSION['can_reset_password']) || !isset($_SESSION['reset_email'])) {
    header('Location: forgot-password.php');
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (strlen($password) < 8) {
        $message = "Le mot de passe doit contenir au moins 8 caractères.";
        $messageType = 'error';
    } elseif ($password !== $confirm_password) {
        $message = "Les mots de passe ne correspondent pas.";
        $messageType = 'error';
    } else {
        $email = $_SESSION['reset_email'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare('UPDATE user SET password = ? WHERE email = ?');
            $stmt->execute([$hashed_password, $email]);
            
            $stmt = $pdo->prepare('UPDATE password_resets SET used = 1 WHERE email = ? AND code = ?');
            $stmt->execute([$email, $_SESSION['reset_code']]);
            
            unset($_SESSION['can_reset_password']);
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_code']);
            
            $_SESSION['password_reset_success'] = true;
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $message = "Une erreur est survenue. Veuillez réessayer.";
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
    <title>Réinitialisation du mot de passe</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Nouveau mot de passe</h1>
        
        <?php if ($message): ?>
            <div class="message <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <input type="password" name="password" placeholder="Nouveau mot de passe" required>
            </div>
            <div class="form-group">
                <input type="password" name="confirm_password" placeholder="Confirmez le mot de passe" required>
            </div>
            <button type="submit">Réinitialiser le mot de passe</button>
        </form>
    </div>
</body>
</html>