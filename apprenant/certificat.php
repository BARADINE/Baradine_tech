<?php
session_start();
//if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'apprenant') {
  //  header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';

$certificats = $pdo->prepare("SELECT c.titre, ce.date_obtention, ce.chemin_pdf
                              FROM certificats ce
                              JOIN cours c ON ce.cours_id = c.id
                              WHERE ce.apprenant_id = ?
                              ORDER BY ce.date_obtention DESC");
$certificats->execute([$_SESSION['user_id']]);
$liste = $certificats->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes certificats | Baradine Tech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <div class="header"><h1>Mes certificats obtenus</h1></div>
    <div class="nav"><a href="dashboard.php">← Retour</a></div>
    <div class="card">
        <?php if(empty($liste)): ?>
            <p>Vous n'avez pas encore obtenu de certificat.</p>
        <?php else: ?>
            <table>
                <tr><th>Cours</th><th>Date d'obtention</th><th>Action</th></tr>
                <?php foreach($liste as $ce): ?>
                <tr>
                    <td><?= $ce['titre'] ?></td>
                    <td><?= date('d/m/Y à H:i', strtotime($ce['date_obtention'])) ?></td>
                    <td>
                        <a href="../<?= $ce['chemin_pdf'] ?>" target="_blank" class="btn btn-sm btn-primary">Voir / Imprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

