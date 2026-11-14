<?php
session_start();
//if (isset($_SESSION['user_id'])) {
  //  header("Location: ../index.php");
    //exit;
//}

require '../config/db.php';

$erreur = "";
$succes = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mot_de_passe = password_hash(trim($_POST['mot_de_passe']), PASSWORD_DEFAULT);
    $role_id = isset($_POST['role_id']) ? intval($_POST['role_id']) : 0;

   
    if (!in_array($role_id, [1, 2, 3])) {
        $erreur = "Veuillez choisir un profil valide.";
    } else {
       
        $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $check->execute([$email]);
        if ($check->rowCount() > 0) {
            $erreur = "Cet email est déjà utilisé.";
        } else {
            // Insertion adaptée à votre structure
            $req = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role_id) VALUES (?, ?, ?, ?)");
            if ($req->execute([$nom, $email, $mot_de_passe, $role_id])) {
                $succes = "✅ Inscription réussie ! Vous pouvez vous connecter.";
            } else {
                $erreur = "❌ Erreur lors de l'inscription.";
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
    <title>S'inscrire - Baradine Tech</title>
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
            max-width: 420px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }
        h2 { text-align: center; color: #004080; margin-bottom: 20px; }
        input, select {
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
        .succes { background: #d4edda; color: #155724; }
        .lien { text-align: center; margin-top: 15px; display: block; color: #004080; text-decoration: none; }
        .lien:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="form-box">
    <h2>Créer un compte</h2>

    <?php if ($erreur): ?><div class="msg erreur"><?= $erreur ?></div><?php endif; ?>
    <?php if ($succes): ?><div class="msg succes"><?= $succes ?></div><?php endif; ?>

    <form method="post" action="">
        <input type="text" name="nom" placeholder="Votre nom complet" required>
        <input type="email" name="email" placeholder="Votre adresse email" required>
        <input type="password" name="mot_de_passe" placeholder="Choisissez un mot de passe" required>

        <!--  Liste adaptée à vos ID : 3=apprenant, 2=formateur, 1=admin -->
        <select name="role_id" required>
            <option value="">-- Choisir votre profil --</option>
            <option value="3">Apprenant</option>
            <option value="2">Formateur</option>
            <option value="1">Administrateur</option>
        </select>

        <button type="submit">S'inscrire</button>
    </form>

    <a href="login.php" class="lien">Déjà un compte ? Se connecter</a>
</div>
</body>
</html>

