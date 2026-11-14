<?php
session_start();
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'apprenant') {
  //  header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';
$user_id = $_SESSION['user_id'];

$cours = $pdo->query("SELECT * FROM cours ORDER BY date_creation DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Cours - Baradine Tech</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial, sans-serif; }
        .header { background:#004080; color:white; padding:18px 25px; font-size:22px; }
        .nav { background:#0059b3; padding:12px 25px; }
        .nav a { color:white; text-decoration:none; padding:8px 15px; margin-right:8px; border-radius:4px; }
        .container { max-width:1100px; margin:30px auto; padding:0 25px; }
        table { width:100%; border-collapse:collapse; background:white; box-shadow:0 2px 8px rgba(0,0,0,0.1); border-radius:6px; }
        th, td { padding:15px; text-align:left; border-bottom:1px solid #eee; }
        .btn { padding:7px 12px; border-radius:4px; color:white; text-decoration:none; font-size:13px; border:none; cursor:pointer; }
        .btn-inscrire { background:#28a745; }
        .btn-module { background:#004080; }
        .btn-quiz { background:#8e44ad; }
        .btn-certif { background:#2980b9; }
        .verrouille { background:#6c757d; cursor:not-allowed; }
    </style>
</head>
<body>
<div class="header">Baradine Tech - Espace Apprenant</div>
<div class="nav">
    <a href="dashboard.php">Tableau de bord</a>
    <a href="cours.php">Mes cours</a>
    <a href="progression.php">Ma progression</a>
    <a href="../auth/logout.php" style="background:#dc3545; float:right;">Déconnexion</a>
</div>
<div class="container">
    <h2>Liste des cours</h2>
    <?php if (empty($cours)): ?>
        <p>Aucun cours disponible.</p>
    <?php else: ?>
        <table>
            <thead><tr><th>Titre</th><th>Prix</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($cours as $c):
                $cours_id = $c['id'];
                // Vérifier inscription
                $inscrit = $pdo->prepare("SELECT id FROM inscriptions WHERE utilisateur_id=? AND cours_id=?");
                $inscrit->execute([$user_id, $cours_id]);
                $est_inscrit = $inscrit->rowCount() > 0;
                // Vérifier progression
                $prog = $pdo->prepare("SELECT module_termine, score, certificat_deverrouille FROM progression WHERE utilisateur_id=? AND cours_id=?");
                $prog->execute([$user_id, $cours_id]);
                $p = $prog->fetch(PDO::FETCH_ASSOC);
                $module_ok = $p && $p['module_termine'] == 1;
                $certif_ok = $p && $p['certificat_deverrouille'] == 1;
            ?>
                <tr>
                    <td><?= htmlspecialchars($c['titre']) ?></td>
                    <td><?= number_format($c['prix'], 2) ?> FCFA</td>
                    <td>
                        <?php if (!$est_inscrit): ?>
                            <a href="inscrire.php?cours_id=<?= $cours_id ?>" class="btn btn-inscrire">S'inscrire</a>
                            <span class="btn verrouille">Module 🔒</span>
                            <span class="btn verrouille">Examen 🔒</span>
                            <span class="btn verrouille">Certificat 🔒</span>
                        <?php else: ?>
                            <a href="module.php?cours_id=<?= $cours_id ?>" class="btn btn-module">📘 Module</a>
                            <?php if ($module_ok): ?>
                                <a href="quiz_etape.php?cours_id=<?= $cours_id ?>" class="btn btn-quiz"> Passer l'Examen</a>
                            <?php else: ?>
                                <span class="btn verrouille">Examen 🔒</span>
                            <?php endif; ?>
                            <?php if ($certif_ok): ?>
                                <a href="generer_certificat.php?cours_id=<?= $cours_id ?>" class="btn btn-certif" target="_blank">📜 Certificat</a>
                            <?php else: ?>
                                <span class="btn verrouille">Certificat 🔒</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>

