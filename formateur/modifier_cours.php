<?php
session_start();
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'formateur') {
  //  header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';

$formateur_id = $_SESSION['user_id'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;


$cours = $pdo->prepare("SELECT * FROM cours WHERE id = ? AND formateur_id = ?");
$cours->execute([$id, $formateur_id]);
$donnees = $cours->fetch(PDO::FETCH_ASSOC);
if (!$donnees) {
    die(" Cours introuvable ou accès interdit.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $prix = floatval($_POST['prix']);

    if (!empty($titre)) {
        $maj = $pdo->prepare("UPDATE cours SET titre=?, description=?, prix=? WHERE id=?");
        $maj->execute([$titre, $description, $prix, $id]);
        header("Location: gerer_cours.php?success=1");
        exit;
    }
    $message = "<p style='color:red;'> Le titre est obligatoire.</p>";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le cours</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial, sans-serif; }
        .container { max-width:700px; margin:30px auto; padding:0 25px; }
        .form-group { margin-bottom:18px; }
        label { display:block; margin-bottom:6px; font-weight:bold; }
        input, textarea { width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; }
        button { background:#004080; color:white; border:none; padding:12px 22px; border-radius:4px; cursor:pointer; }
    </style>
</head>
<body>
<div class="container">
    <h2>Modifier le cours</h2>
    <?= $message ?? '' ?>
    <form method="post">
        <div class="form-group">
            <label>Titre :</label>
            <input type="text" name="titre" value="<?= htmlspecialchars($donnees['titre']) ?>" required>
        </div>
        <div class="form-group">
            <label>Description :</label>
            <textarea name="description" rows="5"><?= htmlspecialchars($donnees['description']) ?></textarea>
        </div>
        <div class="form-group">
            <label>Prix (FCFA) :</label>
            <input type="number" step="0.01" name="prix" value="<?= $donnees['prix'] ?>">
        </div>
        <button type="submit">Enregistrer les modifications</button>
        <br><br>
        <a href="gerer_cours.php">← Retour à la liste</a>
    </form>
</div>
</body>
</html>

