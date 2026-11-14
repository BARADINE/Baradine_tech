<?php
session_start();
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'apprenant') {
  //  header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';
$user_id = $_SESSION['user_id'];

$req = $pdo->prepare("
    SELECT 
        c.id AS cours_id,
        c.titre AS cours_titre,
        i.date_inscription,
        p.module_termine,
        p.score,
        p.certificat_deverrouille,
        p.date_reussite
    FROM inscriptions i
    JOIN cours c ON i.cours_id = c.id
    LEFT JOIN progression p ON i.utilisateur_id = p.utilisateur_id AND i.cours_id = p.cours_id
    WHERE i.utilisateur_id = ?
    ORDER BY i.date_inscription DESC
");
$req->execute([$user_id]);
$liste_progression = $req->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ma Progression</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial, sans-serif; }
        .header { background:#004080; color:white; padding:18px 25px; font-size:22px; }
        .nav { background:#0059b3; padding:12px 25px; }
        .nav a { color:white; text-decoration:none; padding:8px 15px; margin-right:8px; border-radius:4px; }
        .container { max-width:1100px; margin:30px auto; padding:0 25px; }
        table { width:100%; border-collapse:collapse; background:white; box-shadow:0 2px 8px rgba(0,0,0,0.1); border-radius:6px; }
        th, td { padding:15px; text-align:left; border-bottom:1px solid #eee; }
        .statut { padding:6px 10px; border-radius:4px; font-weight:bold; }
        .verrouille { background:#e9ecef; color:#6c757d; }
        .termine { background:#d4edda; color:#155724; }
        .reussi { background:#cfe2ff; color:#084298; }
        .echec { background:#f8d7da; color:#721c24; }
        .btn { padding:6px 10px; border-radius:4px; color:white; text-decoration:none; font-size:13px; }
        .btn-module { background:#004080; }
        .btn-quiz { background:#8e44ad; }
        .btn-certif { background:#2980b9; }
        .btn-desactive { background:#6c757d; cursor:not-allowed; }
    </style>
</head>
<body>
<div class="header">Baradine Tech - Ma Progression</div>
<div class="nav">
    <a href="dashboard.php">Tableau de bord</a>
    <a href="cours.php">Mes cours</a>
    <a href="progression.php">Ma progression</a>
    <a href="../auth/logout.php" style="background:#dc3545; float:right;">Déconnexion</a>
</div>
<div class="container">
    <h2>État de mes formations</h2>
    <?php if (empty($liste_progression)): ?>
        <p class="aucun">Vous n'êtes inscrit à aucun cours.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>Cours</th><th>Module</th><th>Examen</th><th>Certificat</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($liste_progression as $prog):
                $cours_id = $prog['cours_id'];
                $module_ok = !empty($prog['module_termine']) && $prog['module_termine'] == 1;
                $score = $prog['score'] ?? null;
                $reussi = ($score !== null && $score >= 80);
                $certif_ok = !empty($prog['certificat_deverrouille']) && $prog['certificat_deverrouille'] == 1;
            ?>
                <tr>
                    <td><?= htmlspecialchars($prog['cours_titre']) ?></td>
                    <td><?= $module_ok ? '<span class="statut termine">✅ Terminé</span>' : '<span class="statut verrouille">📘 En cours</span>' ?></td>
                    <td>
                        <?php if (!$module_ok): ?>
                            <span class="statut verrouille">🔒 Verrouillé</span>
                        <?php elseif ($score === null): ?>
                            <span class="statut verrouille">⏳ Non passé</span>
                        <?php elseif ($reussi): ?>
                            <span class="statut reussi">✅ <?= $score ?> %</span>
                        <?php else: ?>
                            <span class="statut echec">❌ <?= $score ?> %</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $certif_ok ? '<span class="statut termine">📜 Disponible</span>' : '<span class="statut verrouille">🔒 Verrouillé</span>' ?></td>
                    <td>
                        <a href="module.php?cours_id=<?= $cours_id ?>" class="btn btn-module">Module</a>
                        <?php if ($module_ok): ?>
                            <a href="quiz_etape.php?cours_id=<?= $cours_id ?>" class="btn btn-quiz">Examen</a>
                        <?php else: ?>
                            <span class="btn btn-desactive">Examen</span>
                        <?php endif; ?>
                        <?php if ($certif_ok): ?>
                            <a href="generer_certificat.php?cours_id=<?= $cours_id ?>" class="btn btn-certif" target="_blank">Certificat</a>
                        <?php else: ?>
                            <span class="btn btn-desactive">Certificat</span>
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

