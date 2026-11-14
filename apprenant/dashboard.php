<?php
session_start();
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'apprenant') {
  //  header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';

$req = $pdo->prepare("
    SELECT c.titre, p.score, p.date_passage
    FROM progression p
    JOIN cours c ON p.cours_id = c.id
    WHERE p.utilisateur_id = ?
    ORDER BY p.date_passage DESC
");
$req->execute([$_SESSION['user_id']]);
$progressions = $req->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - Apprenant</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial, sans-serif; }
        .header { background:#004080; color:white; padding:18px 25px; font-size:22px; }
        .nav { background:#0059b3; padding:12px 25px; }
        .nav a { color:white; text-decoration:none; padding:8px 15px; margin-right:8px; border-radius:4px; }
        .nav a:hover { background:#003366; }
        .container { max-width:1000px; margin:30px auto; padding:0 25px; }
        h2 { color:#004080; margin-bottom:20px; }
        table { width:100%; border-collapse:collapse; background:white; box-shadow:0 2px 8px rgba(0,0,0,0.1); border-radius:6px; overflow:hidden; }
        th, td { padding:15px; text-align:left; border-bottom:1px solid #eee; }
        th { background:#f2f5f9; color:#004080; }
        .aucun { padding:40px; text-align:center; color:#666; font-style:italic; }
        .reussi { color:green; font-weight:bold; }
        .echec { color:red; font-weight:bold; }
    </style>
</head>
<body>

<div class="header">Baradine Tech - Espace Apprenant</div>

<div class="nav">
    <a href="dashboard.php">Tableau de bord</a>
    <a href="cours.php">Mes cours</a>
    <a href="progression.php">Ma progression</a>
    <a href="certificat.php">Mes certificat</a>
    <a href="../auth/logout.php" style="background:#dc3545; float:right;">Déconnexion</a>
</div>

<div class="container">
    <h2>Bienvenue, <?= htmlspecialchars($_SESSION['nom']) ?></h2>

    <h3>Vos derniers résultats</h3>

    <?php if (empty($progressions)): ?>
        <div class="aucun">Aucun résultat de quiz pour le moment.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Cours</th>
                    <th>Score obtenu</th>
                    <th>Date</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($progressions as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['titre']) ?></td>
                        <td><?= $p['score'] ?> %</td>
                        <td><?= date('d/m/Y H:i', strtotime($p['date_passage'])) ?></td>
                        <td class="<?= $p['score'] >= 50 ? 'reussi' : 'echec' ?>">
                            <?= $p['score'] >= 50 ? '✅ Réussi' : '❌ Non réussi' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>

