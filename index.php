<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baradine Tech - Université de N'Djamena</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            background: url('assets/images/logo_univ_ndjamena.png') center no-repeat;
            background-size: cover;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .menu {
            background: rgba(255, 255, 255, 0.88);
            padding: 25px 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            text-align: center;
        }
        .menu a {
            display: inline-block;
            margin: 10px 15px;
            padding: 12px 25px;
            background-color: #004080;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background 0.3s;
        }
        .menu a:hover { background-color: #002d5c; }
        .deconnexion { background-color: #dc3545 !important; }
        .deconnexion:hover { background-color: #b02a37 !important; }
    </style>
</head>
<body>

<div class="menu">
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="auth/login.php">Se connecter</a>
        <a href="auth/register.php">S'inscrire</a>
    <?php else: ?>
        <h3>Bienvenue, <?= htmlspecialchars($_SESSION['nom']) ?></h3>
        <?php if ($_SESSION['role'] === 'apprenant'): ?>
            <a href="apprenant/dashboard.php">Mon espace Apprenant</a>
        <?php elseif ($_SESSION['role'] === 'formateur'): ?>
            <a href="formateur/dashboard.php">Mon espace Formateur</a>
        <?php elseif ($_SESSION['role'] === 'admin'): ?>
            <a href="admin/dashboard.php">Mon espace Administrateur</a>
        <?php endif; ?>
        <a href="auth/logout.php" class="deconnexion">Se déconnecter</a>
    <?php endif; ?>
</div>

</body>
</html>

