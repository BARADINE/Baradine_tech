<?php
session_start();
//if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: ../auth/login.php"); exit; }
require '../config/db.php';

$certificats = $pdo->query("SELECT ce.*, u.nom AS apprenant, c.titre AS cours FROM certificats ce JOIN utilisateurs u ON ce.apprenant_id = u.id JOIN cours c ON ce.cours_id = c.id ORDER BY ce.date_obtention DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Certificats émis | Baradine Tech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <div class="header"><h1>Certificats délivrés</h1></div>
    <div class="nav"><a href="dashboard.php">← Retour</a></div>
    <div class="card">
        <table>
            <tr><th>Apprenant</th><th>Cours</th><th>Date</th><th>Fichier</th></tr>
            <?php foreach($certificats as $ce): ?>
            <tr>
                <td><?= $ce['apprenant'] ?></td>
                <td><?= $ce['cours'] ?></td>
                <td><?= date('d/m/Y H:i', strtotime($ce['date_obtention'])) ?></td>
                <td>
                    <a href="../<?= $ce['chemin_pdf'] ?>" target="_blank" class="btn btn-sm btn-primary">Voir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
</body>
</html>

