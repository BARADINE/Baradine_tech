<?php
session_start();
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'apprenant') {
  //  header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';

$user_id = $_SESSION['user_id'];
$cours_id = isset($_GET['cours_id']) ? intval($_GET['cours_id']) : 0;
if ($cours_id <= 0) die("Cours invalide.");


$inscrit = $pdo->prepare("SELECT id FROM inscriptions WHERE utilisateur_id=? AND cours_id=?");
$inscrit->execute([$user_id, $cours_id]);
if ($inscrit->rowCount() === 0) {
    die("❌ Vous n'êtes pas inscrit à ce cours. <a href='cours.php'>Retour</a>");
}


$cours = $pdo->prepare("SELECT * FROM cours WHERE id=?");
$cours->execute([$cours_id]);
$cours = $cours->fetch(PDO::FETCH_ASSOC);


$modules = $pdo->prepare("SELECT * FROM modules WHERE cours_id=? ORDER BY ordre_affichage");
$modules->execute([$cours_id]);
$liste_modules = $modules->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['terminer'])) {
    $maj = $pdo->prepare("
        INSERT INTO progression (utilisateur_id, cours_id, module_termine)
        VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE module_termine=1
    ");
    $maj->execute([$user_id, $cours_id]);
    $msg = " Module terminé ! Vous pouvez passer l'examen.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Module - <?= htmlspecialchars($cours['titre']) ?></title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial, sans-serif; }
        .header { background:#004080; color:white; padding:18px 25px; font-size:22px; }
        .container { max-width:900px; margin:30px auto; padding:0 25px; }
        .module { background:#f9fafc; padding:20px; margin-bottom:25px; border-radius:6px; border-left:4px solid #004080; }
        .btn-terminer { background:#28a745; color:white; border:none; padding:12px 25px; border-radius:4px; font-size:16px; cursor:pointer; }
        .pdf-link { display:inline-block; margin:10px 0; padding:8px 15px; background:#e7f0fa; color:#004080; border-radius:4px; text-decoration:none; }
        .video-container { margin:15px 0; position:relative; padding-bottom:56.25%; height:0; overflow:hidden; }
        .video-container iframe { position:absolute; top:0; left:0; width:100%; height:100%; border:0; }
    </style>
</head>
<body>
    <div class="header"> <?= htmlspecialchars($cours['titre']) ?></div>
    <div class="container">
        <?php if (!empty($msg)): ?><p style="color:green; font-weight:bold; margin-bottom:20px;"><?= $msg ?></p><?php endif; ?>

        <?php if (empty($liste_modules)): ?>
            <p>Aucun contenu n'a encore été ajouté pour ce cours.</p>
        <?php else: ?>
            <?php foreach ($liste_modules as $mod): ?>
                <div class="module">
                    <h3><?= htmlspecialchars($mod['titre']) ?></h3>
                    <br>

                    <!-- Texte -->
                    <?php if (!empty($mod['contenu_texte'])): ?>
                        <p><?= nl2br(htmlspecialchars($mod['contenu_texte'])) ?></p>
                        <br>
                    <?php endif; ?>

                    <!-- PDF -->
                    <?php if (!empty($mod['fichier_pdf'])): ?>
                        <a href="../<?= $mod['fichier_pdf'] ?>" target="_blank" class="pdf-link"> Ouvrir / Télécharger le PDF</a>
                        <br>
                    <?php endif; ?>

                    <!-- Vidéo -->
                    <?php if (!empty($mod['lien_video'])): ?>
                        <h4>Vidéo de formation :</h4>
                        <div class="video-container">
                            <?php
                           
                            $lien = $mod['lien_video'];
                            if (strpos($lien, 'youtube.com/watch?v=') !== false) {
                                $lien = str_replace('watch?v=', 'embed/', $lien);
                            } elseif (strpos($lien, 'youtu.be/') !== false) {
                                $lien = str_replace('youtu.be/', 'youtube.com/embed/', $lien);
                            }
                            ?>
                            <iframe src="<?= htmlspecialchars($lien) ?>" allowfullscreen></iframe>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <form method="post" onsubmit="return confirm('Confirmer avoir terminé tout le contenu ?')">
                <button type="submit" name="terminer" class="btn-terminer"> J'ai terminé le module</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

