<?php
session_start();
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'formateur') {
  //  header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';


$cours = $pdo->prepare("SELECT id, titre FROM cours WHERE formateur_id = ? ORDER BY titre");
$cours->execute([$_SESSION['user_id']]);
$liste_cours = $cours->fetchAll(PDO::FETCH_ASSOC);

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cours_id = intval($_POST['cours_id']);
    $titre = trim($_POST['titre']);
    $contenu_texte = trim($_POST['contenu_texte']);
    $lien_video = trim($_POST['lien_video']);
    $ordre = intval($_POST['ordre_affichage']);
    $chemin_pdf = "";

    
    $verif = $pdo->prepare("SELECT id FROM cours WHERE id = ? AND formateur_id = ?");
    $verif->execute([$cours_id, $_SESSION['user_id']]);
    if ($verif->rowCount() === 0) {
        die("❌ Ce cours ne vous appartient pas.");
    }

   
    if (!empty($_FILES['fichier_pdf']['name'])) {
        $ext = strtolower(pathinfo($_FILES['fichier_pdf']['name'], PATHINFO_EXTENSION));
        if ($ext === 'pdf') {
            $nom_fichier = time() . "_" . uniqid() . ".pdf";
            $chemin = "../uploads/pdf/" . $nom_fichier;
            move_uploaded_file($_FILES['fichier_pdf']['tmp_name'], $chemin);
            $chemin_pdf = "uploads/pdf/" . $nom_fichier;
        } else {
            $message = "<p style='color:red;'> Seuls les fichiers PDF sont autorisés.</p>";
        }
    }

    if (empty($message) && !empty($titre)) {
        $req = $pdo->prepare("
            INSERT INTO modules (cours_id, titre, contenu_texte, fichier_pdf, lien_video, ordre_affichage)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $req->execute([$cours_id, $titre, $contenu_texte, $chemin_pdf, $lien_video, $ordre]);
        $message = "<p style='color:green;'> Module ajouté avec succès !</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Module</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial, sans-serif; }
        .header { background:#004080; color:white; padding:18px 25px; font-size:22px; }
        .container { max-width:700px; margin:30px auto; padding:0 25px; }
        .form-group { margin-bottom:18px; }
        label { display:block; margin-bottom:6px; font-weight:bold; }
        input, textarea, select { width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; font-size:15px; }
        button { background:#004080; color:white; border:none; padding:12px 22px; border-radius:4px; cursor:pointer; font-size:16px; }
        button:hover { background:#002d5c; }
    </style>
</head>
<body>
    <div class="header">Baradine Tech - Espace Formateur</div>
    <div class="container">
        <h2>Ajouter un module / contenu</h2>
        <?= $message ?>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Choisir votre cours :</label>
                <select name="cours_id" required>
                    <option value="">-- Sélectionner --</option>
                    <?php foreach ($liste_cours as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['titre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Titre du module :</label>
                <input type="text" name="titre" required>
            </div>
            <div class="form-group">
                <label>Contenu texte :</label>
                <textarea name="contenu_texte" rows="5"></textarea>
            </div>
            <div class="form-group">
                <label>Fichier PDF :</label>
                <input type="file" name="fichier_pdf" accept=".pdf">
            </div>
            <div class="form-group">
                <label>Lien vidéo (YouTube / autre) :</label>
                <input type="url" name="lien_video" placeholder="https://www.youtube.com/watch?v=...">
            </div>
            <div class="form-group">
                <label>Ordre d'affichage :</label>
                <input type="number" name="ordre_affichage" value="1" min="1">
            </div>
            <button type="submit">Enregistrer le module</button>
            <br><br>
            <a href="dashboard.php">← Retour au tableau de bord</a>
        </form>
    </div>
</body>
</html>

