<?php
session_start();
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'formateur') {
  //  header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';

$formateur_id = $_SESSION['user_id'];
$message = "";


if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    $verif = $pdo->prepare("SELECT id FROM cours WHERE id = ? AND formateur_id = ?");
    $verif->execute([$id, $formateur_id]);
    if ($verif->rowCount() > 0) {
        $pdo->prepare("DELETE FROM cours WHERE id = ?")->execute([$id]);
        $message = "<p style='color:green;'>✅ Cours supprimé.</p>";
    } else {
        $message = "<p style='color:red;'>❌ Action interdite.</p>";
    }
}


$cours = $pdo->prepare("SELECT * FROM cours WHERE formateur_id = ? ORDER BY date_creation DESC");
$cours->execute([$formateur_id]);
$liste_cours = $cours->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer mes cours</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial, sans-serif; }
        .header { background:#004080; color:white; padding:18px 25px; font-size:22px; }
        .container { max-width:1000px; margin:30px auto; padding:0 25px; }
        table { width:100%; border-collapse:collapse; background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.1); border-radius:6px; overflow:hidden; }
        th, td { padding:15px; text-align:left; border-bottom:1px solid #eee; }
        th { background:#f2f5f9; color:#004080; }
        .btn { padding:7px 12px; border-radius:4px; color:white; text-decoration:none; font-size:13px; border:none; cursor:pointer; }
        .btn-modif { background:#f39c12; }
        .btn-suppr { background:#e74c3c; }
        .btn-ajout { background:#27ae60; display:inline-block; margin-bottom:15px; }
    </style>
</head>
<body>
<div class="header">Baradine Tech - Espace Formateur</div>
<div class="container">
    <h2>Mes cours</h2>
    <?= $message ?>
    <a href="ajouter_cours.php" class="btn btn-ajout">+ Ajouter un cours</a>

    <?php if (empty($liste_cours)): ?>
        <p>Vous n'avez pas encore créé de cours.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Prix</th>
                    <th>Date création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($liste_cours as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['titre']) ?></td>
                    <td><?= number_format($c['prix'], 0, ',', ' ') ?> FCFA</td>
                    <td><?= date('d/m/Y', strtotime($c['date_creation'])) ?></td>
                    <td>
                        <a href="modifier_cours.php?id=<?= $c['id'] ?>" class="btn btn-modif">Modifier</a>
                        <a href="gerer_modules.php?cours_id=<?= $c['id'] ?>" class="btn" style="background:#8e44ad;">Modules</a>
                        <a href="?supprimer=<?= $c['id'] ?>" class="btn btn-suppr" onclick="return confirm('Supprimer ce cours ? Tous ses modules seront aussi supprimés.')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <br><a href="dashboard.php">← Retour au tableau de bord</a>
</div>
</body>
</html>

