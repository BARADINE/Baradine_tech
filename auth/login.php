<?php
session_start();
//if (isset($_SESSION['user_id'])) {
  //  header("Location: ../index.php");
    //exit;
//}

require '../config/db.php';

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    $req = $pdo->prepare("
        SELECT u.id, u.nom, u.mot_de_passe, r.nom AS role_nom
        FROM utilisateurs u
        JOIN roles r ON u.role_id = r.id
        WHERE u.email = ?
    ");
    $req->execute([$email]);
    $user = $req->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['role'] = $user['role_nom'];

        // ✅ Redirection selon le nom du rôle (fonctionne quel que soit l'ID)
        if ($user['role_nom'] === 'apprenant') {
            header("Location: ../apprenant/dashboard.php");
        } elseif ($user['role_nom'] === 'formateur') {
            header("Location: ../formateur/dashboard.php");
        } elseif ($user['role_nom'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        }
        exit;
    } else {
        $erreur = "❌ Email ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter - Baradine Tech</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body {
            background: url('../assets/images/logo_univ_ndjamena.png') center no-repeat;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .form-box {
            background: rgba(255, 255, 255, 0.92);
            padding: 30px 35px;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }
        h2 { text-align: center; color: #004080; margin-bottom: 20px; }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #004080;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover { background: #002d5c; }
        .msg { padding: 10px; margin: 10px 0; border-radius: 4px; text-align: center; }
        .erreur { background: #f8d7da; color: #721c24; }
        .lien { text-align: center; margin-top: 15px; display: block; color: #004080; text-decoration: none; }
        .lien:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="form-box">
    <h2>Se connecter</h2>

    <?php if ($erreur): ?><div class="msg erreur"><?= $erreur ?></div><?php endif; ?>

    <form method="post" action="">
        <input type="email" name="email" placeholder="Votre Email" required>
        <input type="password" name="mot_de_passe" placeholder="Votre Mot de passe" required>
        <button type="submit">Se connecter</button>
    </form>

    <a href="register.php" class="lien">Pas encore de compte ? S'inscrire</a>
</div>
</body>
</html>

