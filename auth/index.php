<?php
session_start();
require 'db.php';

// Gestion de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if ($email && $password) {
        $stmt = $pdo->prepare('SELECT * FROM user WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['welcome_message'] = "Bienvenue, " . $user['prenom'] . " " . $user['nom'] . " (" . $user['role'] . ") !";
            session_regenerate_id(true);
            header('Location: dashboard.php');
            exit;
        } else {
            $loginError = "Email ou mot de passe incorrect.";
        }
    } else {
        $loginError = "Veuillez remplir tous les champs.";
    }
}

// Gestion de l'accès invité
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guest_access'])) {
    $_SESSION['role'] = 'guest';
    $_SESSION['welcome_message'] = "Bienvenue, Invité !";
    header('Location: dashboard.php');
    exit;
}

// Gestion de l'inscription (optionnel)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    // Votre code d'inscription existant...
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion et Inscription</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="container" id="container">
        <!-- Login Form -->
        <div class="form-container sign-in">
            <form method="POST" action="">
                <h1>Connexion</h1>
                <?php if (isset($_SESSION['register_success'])): ?>
                    <p class="success"><?= htmlspecialchars($_SESSION['register_success']) ?></p>
                    <?php unset($_SESSION['register_success']); ?>
                <?php endif; ?>
                <?php if (isset($loginError)): ?>
                    <p class="error"><?= htmlspecialchars($loginError) ?></p>
                <?php endif; ?>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit" name="login">Se connecter</button>
                <a href="forgot-password.php" style="color: #EC5A13; margin-top: 15px;">Mot de passe oublié ?</a>
            </form>
        </div>

        <!-- Registration Form -->
        <div class="form-container sign-up">
            <form method="POST"  enctype="multipart/form-data" action="register.php">
                <h1>Inscription</h1>
                <?php if (isset($registerError)): ?>
                    <p class="error"><?= htmlspecialchars($registerError) ?></p>
                <?php endif; ?>
                <input type="text" name="nom" placeholder="Nom" required>
                <input type="text" name="prenom" placeholder="Prénom" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <input type="text" name="adresse" placeholder="Adresse">
                <input type="text" name="telephone" placeholder="Téléphone">
                <input type="file" name="avatar" accept="image/*">
                <label>
                    <input type="checkbox" name="notificationsActives"> Activer les notifications
                </label>
                <button type="submit" name="register" >S'inscrire</button>
            </form>
        </div>

        <!-- Guest Access Button -->
        

        <!-- Toggle Panel -->
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Content de vous revoir !</h1>
                    <p>Connectez-vous pour accéder à votre compte.</p>
                    <button class="hidden" id="login">Se connecter</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Bonjour, ami !</h1>
                    <p>Inscrivez-vous pour accéder à toutes les fonctionnalités.</p>
                    <button class="hidden" id="register">S'inscrire</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('container');
        const loginButton = document.getElementById('login');
        const registerButton = document.getElementById('register');

        registerButton.addEventListener('click', () => {
            container.classList.add('active');
        });

        loginButton.addEventListener('click', () => {
            container.classList.remove('active');
        });
    </script>
</body>
</html>