<?php
session_start();
// Vérification : seul l'administrateur peut accéder
//if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  //  header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';

// Récupérer l'ID du cours
$id = intval($_GET['id'] ?? 0);

// Récupérer les informations du cours
$stmt = $pdo->prepare("SELECT * FROM cours WHERE id = ?");
$stmt->execute([$id]);
$cours = $stmt->fetch();

if (!$cours) {
    die("Cours introuvable.");
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $prix = floatval($_POST['prix']);
    $formateur_id = !empty($_POST['formateur_id']) ? intval($_POST['formateur_id']) : null;

    try {
        $update = $pdo->prepare("UPDATE cours SET titre = ?, description = ?, prix = ?, formateur_id = ? WHERE id = ?");
        $update->execute([$titre, $description, $prix, $formateur_id, $id]);
        $succes = "Cours modifié avec succès !";
        // Actualiser les données affichées
        $cours = array_merge($cours, $_POST);
    } catch (Exception $e) {
        $erreur = "Erreur lors de la modification : " . $e->getMessage();
    }
}

// Récupérer la liste des formateurs pour le menu déroulant
$formateurs = $pdo->query("SELECT id, nom FROM utilisateurs WHERE role_id = 2 ORDER BY nom ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un cours | Baradine Tech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Modifier le cours</h1>
        <p>Administration - Baradine Tech</p>
    </div>

    <div class="nav">
        <a href="cours.php">← Retour à la liste des cours</a>
    </div>

    <div class="card" style="max-width:750px; margin: 0 auto;">
        <?php if(isset($succes)): ?>
            <p style="color: green; padding: 10px; background: #e7f9ed; border-radius: 4px; margin-bottom:15px;"><?= $succes ?></p>
        <?php endif; ?>
        <?php if(isset($erreur)): ?>
            <p style="color: red; padding: 10px; background: #fceff4; border-radius: 4px; margin-bottom:15px;"><?= $erreur ?></p>
        <?php endif; ?>

        <form method="post">
            <label>Titre du cours :</label>
            <input type="text" name="titre" value="<?= htmlspecialchars($cours['titre']) ?>" required>

            <label>Description :</label>
            <textarea name="description" rows="6" required><?= htmlspecialchars($cours['description']) ?></textarea>

            <label>Prix (en FCFA) :</label>
            <input type="number" step="0.01" min="0" name="prix" value="<?= $cours['prix'] ?>" required>

            <label>Formateur assigné :</label>
            <select name="formateur_id">
                <option value="">-- Aucun formateur --</option>
                <?php foreach($formateurs as $f): ?>
                    <option value="<?= $f['id'] ?>" <?= ($cours['formateur_id'] == $f['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($f['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn-primary" style="margin-top:15px;">Enregistrer les modifications</button>
        </form>
    </div>
</div>
</body>
</html>

