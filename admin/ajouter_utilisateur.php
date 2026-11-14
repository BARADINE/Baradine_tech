<?php
session_start();
//if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: ../auth/login.php"); exit; }
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mdp = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $role_id = $_POST['role_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nom, $email, $mdp, $role_id]);
        $succes = "Utilisateur ajouté avec succès !";
    } catch (Exception $e) {
        $erreur = "Erreur : " . $e->getMessage();
    }
}

$roles = $pdo->query("SELECT * FROM roles")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Utilisateur | Baradine Tech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <div class="header"><h1>Ajouter un utilisateur</h1></div>
    <div class="nav"><a href="utilisateurs.php">← Retour à la liste</a></div>
    <div class="card" style="max-width:600px; margin:0 auto;">
        <?php if(isset($succes)) echo "<p style='color:green;'>$succes</p>"; ?>
        <?php if(isset($erreur)) echo "<p style='color:red;'>$erreur</p>"; ?>
        <form method="post">
            <label>Nom complet :</label>
            <input type="text" name="nom" required>

            <label>Email :</label>
            <input type="email" name="email" required>

            <label>Mot de passe :</label>
            <input type="password" name="mot_de_passe" required>

            <label>Rôle :</label>
            <select name="role_id" required>
                <?php foreach($roles as $r): ?>
                    <option value="<?= $r['id'] ?>"><?= ucfirst($r['nom']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn-success">Enregistrer</button>
        </form>
    </div>
</div>
</body>
</html>

