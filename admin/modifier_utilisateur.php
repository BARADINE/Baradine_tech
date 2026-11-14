<?php
session_start();
//if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: ../auth/login.php"); exit; }
require '../config/db.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$utilisateur = $stmt->fetch();
if (!$utilisateur) { die("Utilisateur introuvable"); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $role_id = $_POST['role_id'];
    $mdp = !empty($_POST['mot_de_passe']) ? password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT) : $utilisateur['mot_de_passe'];

    try {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET nom=?, email=?, mot_de_passe=?, role_id=? WHERE id=?");
        $stmt->execute([$nom, $email, $mdp, $role_id, $id]);
        $succes = "Modification enregistrée !";
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
    <title>Modifier Utilisateur | Baradine Tech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <div class="header"><h1>Modifier l'utilisateur</h1></div>
    <div class="nav"><a href="utilisateurs.php">← Retour</a></div>
    <div class="card" style="max-width:600px; margin:0 auto;">
        <?php if(isset($succes)) echo "<p style='color:green;'>$succes</p>"; ?>
        <?php if(isset($erreur)) echo "<p style='color:red;'>$erreur</p>"; ?>
        <form method="post">
            <label>Nom complet :</label>
            <input type="text" name="nom" value="<?= $utilisateur['nom'] ?>" required>

            <label>Email :</label>
            <input type="email" name="email" value="<?= $utilisateur['email'] ?>" required>

            <label>Nouveau mot de passe (laisser vide pour ne pas changer) :</label>
            <input type="password" name="mot_de_passe">

            <label>Rôle :</label>
            <select name="role_id" required>
                <?php foreach($roles as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= $r['id'] == $utilisateur['role_id'] ? 'selected' : '' ?>>
                        <?= ucfirst($r['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>
</div>
</body>
</html>

