<?php
session_start();
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'formateur') {
  //  header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';

$formateur_id = $_SESSION['user_id'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;


$req = $pdo->prepare("
    SELECT m.* FROM modules m
    JOIN cours c ON m.cours_id = c.id
    WHERE m.id = ? AND c.formateur_id = ?
");
$req->execute([$id, $formateur_id]);
$mod = $req->fetch(PDO::FETCH_ASSOC);
if (!$mod) die(" Module interdit ou inexistant.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $texte = trim($_POST['contenu_texte']);
    $lien = trim($_POST['lien_video']);
    $ordre = intval($_POST['ordre_affichage']);
    $pdf_actuel = $mod['fichier_pdf'];

    
    if (!empty($_FILES['fichier_pdf']['name'])) {
        $ext = strtolower(pathinfo($_FILES['fichier_pdf']['name'], PATHINFO_EXTENSION));
        if ($ext === 'pdf') {
            
            if (!empty($pdf_actuel) && file_exists("../".$pdf_actuel)) unlink("../".$pdf_actuel);
            $nom = time() . "_" . uniqid() . ".pdf";
            $chemin = "uploads/pdf/" . $nom;
            move_uploaded_file($_FILES['fichier_pdf']['tmp_name'], "../".$chemin);
            $pdf_actuel = $chemin;
        } else {
            $message = "<p style='color:red;'>❌ Format accepté : PDF uniquement.</p>";
        }
    }

    if (empty($message) && !empty($titre)) {
        $maj = $pdo->prepare("
            UPDATE modules SET titre=?, contenu_texte=?, fichier_pdf=?, lien_video=?, ordre_affichage=?
            WHERE id=?
        ");
        $maj->execute([$titre, $texte, $pdf_actuel, $lien, $ordre, $id]);
        header("Location: gerer_modules.php?cours_id=".$mod['cours_id']."&success=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le module</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial, sans-serif; }
        .container { max-width:700px; margin:30px auto; padding:0 25px; }
        .form-group { margin-bottom:18px; }
        label { display:block; margin-bottom:6px; font-weight:bold; }
        input, textarea { width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Modifier le module</h2>
    <?= $message ?? '' ?>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Titre :</label>
            <input type="text" name="titre" value="<?= htmlspecialchars($mod['titre']) ?>" required>
        </div>
        <div class="form-group">
            <label>Contenu texte :</label>
            <textarea name="contenu_texte" rows="5"><?= htmlspecialchars($mod['contenu_texte']) ?></textarea>
        </div>
        <div class="form-group">
            <label>Fichier PDF (laisser vide pour conserver l'actuel) :</label>
            <input type="file" name="fichier_pdf" accept=".pdf">
            <?php if (!empty($mod['fichier_pdf'])): ?>
                <p>Fichier actuel : <a href="../<?= $mod['fichier_pdf'] ?>" target="_blank">Voir le PDF</a></p>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label>Lien vidéo :</label>
            <input type="url" name="lien_video" value="<?= htmlspecialchars($mod['lien_video']) ?>">
        </div>
        <div class="form-group">
            <label>Ordre d'affichage :</label>
            <input type="number" name="ordre_affichage" value="<?= $mod['ordre_affichage'] ?>" min="1">
        </div>
        <button type="submit" style="background:#004080; color:white; border:none; padding:10px 20px; border-radius:4px;">Enregistrer</button>
        <br><br>
        <a href="gerer_modules.php?cours_id=<?= $mod['cours_id'] ?>">← Retour</a>
    </form>
</div>
</body>
</html>

