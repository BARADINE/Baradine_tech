<?php
session_start();
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'formateur') {
  //  header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';

$formateur_id = $_SESSION['user_id'];
$cours_id = isset($_GET['cours_id']) ? intval($_GET['cours_id']) : 0;


$verif = $pdo->prepare("SELECT id, titre FROM cours WHERE id = ? AND formateur_id = ?");
$verif->execute([$cours_id, $formateur_id]);
$cours = $verif->fetch(PDO::FETCH_ASSOC);
if (!$cours) die("❌ Cours interdit ou inexistant.");


if (isset($_GET['supprimer'])) {
    $mod_id = intval($_GET['supprimer']);
   
    $v = $pdo->prepare("SELECT m.id, m.fichier_pdf FROM modules m JOIN cours c ON m.cours_id = c.id WHERE m.id = ? AND c.formateur_id = ?");
    $v->execute([$mod_id, $formateur_id]);
    $mod = $v->fetch();
    if ($mod) {
        
        if (!empty($mod['fichier_pdf']) && file_exists("../".$mod['fichier_pdf'])) {
            unlink("../".$mod['fichier_pdf']);
        }
        $pdo->prepare("DELETE FROM modules WHERE id = ?")->execute([$mod_id]);
        $message = "<p style='color:green;'> Module supprimé.</p>";
    }
}


$modules = $pdo->prepare("SELECT * FROM modules WHERE cours_id = ? ORDER BY ordre_affichage");
$modules->execute([$cours_id]);
$liste_mod = $modules->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les modules</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial, sans-serif; }
        .container { max-width:1000px; margin:30px auto; padding:0 25px; }
        table { width:100%; border-collapse:collapse; background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.1); border-radius:6px; overflow:hidden; }
        th, td { padding:15px; text-align:left; border-bottom:1px solid #eee; }
        th { background:#f2f5f9; color:#004080; }
        .btn { padding:7px 12px; border-radius:4px; color:white; text-decoration:none; font-size:13px; }
        .btn-modif { background:#f39c12; }
        .btn-suppr { background:#e74c3c; }
        .btn-ajout { background:#27ae60; display:inline-block; margin-bottom:15px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Modules du cours : <?= htmlspecialchars($cours['titre']) ?></h2>
    <?= $message ?? '' ?>
    <a href="ajouter_module.php?cours_id=<?= $cours_id ?>" class="btn btn-ajout">+ Ajouter un module</a>

    <?php if (empty($liste_mod)): ?>
        <p>Aucun module pour ce cours.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Ordre</th>
                    <th>Titre</th>
                    <th>Contenu</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($liste_mod as $m): ?>
                <tr>
                    <td><?= $m['ordre_affichage'] ?></td>
                    <td><?= htmlspecialchars($m['titre']) ?></td>
                    <td>
                        <?php
                        $types = [];
                        if (!empty($m['contenu_texte'])) $types[] = "Texte";
                        if (!empty($m['fichier_pdf'])) $types[] = "PDF";
                        if (!empty($m['lien_video'])) $types[] = "Vidéo";
                        echo implode(" + ", $types);
                        ?>
                    </td>
                    <td>
                        <a href="modifier_module.php?id=<?= $m['id'] ?>" class="btn btn-modif">Modifier</a>
                        <a href="?supprimer=<?= $m['id'] ?>" class="btn btn-suppr" onclick="return confirm('Supprimer ce module ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <br><a href="gerer_cours.php">← Retour à mes cours</a>
</div>
</body>
</html>

