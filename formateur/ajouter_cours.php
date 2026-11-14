<?php
session_start();
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'formateur') {
  //  header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $prix = floatval($_POST['prix']);
    $formateur_id = $_SESSION['user_id'];

    if (!empty($titre)) {
        $req = $pdo->prepare("
            INSERT INTO cours (titre, description, prix, formateur_id, date_creation)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $req->execute([$titre, $description, $prix, $formateur_id]);
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
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial, sans-serif; }
        .header { background:#004080; color:white; padding:18px 25px; font-size:22px; }
        .container { max-width:700px; margin:30px auto; padding:0 25px; }
        .form-group { margin-bottom:18px; }
        label { display:block; margin-bottom:6px; font-weight:bold; }
        input, textarea { width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; font-size:15px; }
        button { background:#004080; color:white; border:none; padding:12px 22px; border-radius:4px; cursor:pointer; font-size:16px; }
        button:hover { background:#002d5c; }
    </style>
</head>
<body>
    <div class="header">Baradine Tech - Espace Formateur</div>
    <div class="container">
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
    </div>
</body>
</html>

