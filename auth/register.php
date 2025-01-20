<?php
session_start();
require 'db.php'; // Assurez-vous que ce fichier contient la connexion PDO à votre base de données

// Gestion de l'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    // Récupération des données
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $adresse = filter_input(INPUT_POST, 'adresse', FILTER_SANITIZE_STRING);
    $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
    $notificationsActives = isset($_POST['notificationsActives']) ? 1 : 0;

    // Initialisation des erreurs
    $errors = [];

    // Validation
    if (empty($nom)) $errors[] = "Le champ 'Nom' est obligatoire.";
    if (empty($prenom)) $errors[] = "Le champ 'Prénom' est obligatoire.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Adresse email invalide.";
    if (empty($password) || strlen($password) < 8) $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";

    // Gestion du téléversement de l'avatar
    $avatarPath = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['avatar']['name']);
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

        // Vérification du type de fichier
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowedTypes)) {
            $errors[] = "Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
        } elseif (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
            $avatarPath = $uploadFile;
        } else {
            $errors[] = "Une erreur est survenue lors du téléversement du fichier.";
        }
    }

    if (empty($errors)) {
        // Hachage du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare('SELECT id FROM user WHERE email = :email');
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = "Cet email est déjà utilisé.";
        } else {
            // Insérer l'utilisateur
            $stmt = $pdo->prepare('
                INSERT INTO user (nom, prenom, email, password, adresse, telephone, notificationsActives, avatar)
                VALUES (:nom, :prenom, :email, :password, :adresse, :telephone, :notificationsActives, :avatar)
            ');
            if ($stmt->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'password' => $hashedPassword,
                'adresse' => $adresse,
                'telephone' => $telephone,
                'notificationsActives' => $notificationsActives,
                'avatar' => $avatarPath
            ])) {
                $_SESSION['register_success'] = "Inscription réussie ! Veuillez vous connecter.";
                header('Location: index.php');
                exit;
            } else {
                $errors[] = "Une erreur est survenue lors de l'inscription.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">
    <style>
        /* Votre CSS ici */
    </style>
</head>
<body>
    <div class="container">
        <h1 class="login__title">Créer un compte</h1>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['register_success'])): ?>
            <div class="success">
                <p><?= htmlspecialchars($_SESSION['register_success']) ?></p>
            </div>
            <?php unset($_SESSION['register_success']); ?>
        <?php endif; ?>

        <form action="register.php" method="POST" enctype="multipart/form-data">
            <!-- Nom et Prénom -->
            <div class="login__box">
                <input type="text" id="nom" name="nom" required placeholder=" ">
                <label for="nom">Nom</label>
            </div>
            <div class="login__box">
                <input type="text" id="prenom" name="prenom" required placeholder=" ">
                <label for="prenom">Prénom</label>
            </div>

            <!-- Email -->
            <div class="login__box">
                <input type="email" id="email" name="email" required placeholder=" ">
                <label for="email">Email</label>
            </div>

            <!-- Mot de passe -->
            <div class="login__box">
                <input type="password" id="password" name="password" required placeholder=" ">
                <label for="password">Mot de passe</label>
            </div>

            <!-- Adresse -->
            <div class="login__box">
                <input type="text" id="adresse" name="adresse" placeholder=" ">
                <label for="adresse">Adresse</label>
            </div>

            <!-- Téléphone -->
            <div class="login__box">
                <input type="tel" id="telephone" name="telephone" placeholder=" ">
                <label for="telephone">Téléphone</label>
            </div>

            <!-- Avatar (Image) -->
            <div class="login__box">
                <input type="file" id="avatar" name="avatar" accept="image/*">
                <label for="avatar">Avatar</label>
            </div>

            <!-- Notifications Actives -->
            <div class="login__box">
                <label for="notificationsActives">
                    <input type="checkbox" id="notificationsActives" name="notificationsActives" checked>
                    Activer les notifications
                </label>
            </div>

            <!-- Bouton de soumission -->
            <button type="submit" name="register" class="login__button">Créer un compte</button>
        </form>

        <div class="login__switch">
            <p>Déjà inscrit ? <a href="index.php">Connectez-vous ici</a></p>
        </div>
    </div>
</body>
</html>