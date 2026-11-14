<?php
session_start();
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  //  header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = htmlspecialchars(trim($_POST['titre']));
    $description = htmlspecialchars(trim($_POST['description']));
    $prix = floatval($_POST['prix']);

    if (!empty($titre)) {
        $ajout = $pdo->prepare("INSERT INTO cours (titre, description, prix) VALUES (?, ?, ?)");
        $ajout->execute([$titre, $description, $prix]);
        $message = "<p style='color:green;'> Cours créé avec succès !</p>";
    } else {
        $message = "<p style='color:red;'> Le titre est obligatoire.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Cours</title>
    <style>
        body { font-family: Arial; max-width: 700px; margin: 30px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #004080; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h2>Ajouter un nouveau cours</h2>
    <?= $message ?>
    <form method="post">
        <div class="form-group">
            <label>Titre du cours :</label>
            <input type="text" name="titre" required>
        </div>
        <div class="form-group">
            <label>Description :</label>
            <textarea name="description" rows="4"></textarea>
        </div>
        <div class="form-group">
            <label>Prix (FCFA) :</label>
            <input type="number" step="0.01" name="prix" value="0">
        </div>
        <button type="submit">Enregistrer le cours</button>
        <br><br>
        <a href="dashboard.php">← Retour au tableau de bord</a>
    </form>
</body>
</html>

