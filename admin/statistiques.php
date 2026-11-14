<?php
session_start();
//if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: ../auth/login.php"); exit; }
require '../config/db.php';

$stats = [
    'apprenants' => $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role_id = 3")->fetchColumn(),
    'formateurs' => $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role_id = 2")->fetchColumn(),
    'cours' => $pdo->query("SELECT COUNT(*) FROM cours")->fetchColumn(),
    'certificats' => $pdo->query("SELECT COUNT(*) FROM certificats")->fetchColumn()
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques | Baradine Tech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <div class="header"><h1>Statistiques globales</h1></div>
    <div class="nav"><a href="dashboard.php">← Retour</a></div>
    <div class="card" style="display: grid; grid-template-columns: repeat(2, 1fr); gap:20px;">
        <div style="background:#e7f0ff; padding:25px; border-radius:8px; text-align:center;">
            <h3>Apprenants</h3>
            <p style="font-size:32px; font-weight:bold;"><?= $stats['apprenants'] ?></p>
        </div>
        <div style="background:#e7f9ed; padding:25px; border-radius:8px; text-align:center;">
            <h3>Formateurs</h3>
            <p style="font-size:32px; font-weight:bold;"><?= $stats['formateurs'] ?></p>
        </div>
        <div style="background:#fffbe7; padding:25px; border-radius:8px; text-align:center;">
            <h3>Cours</h3>
            <p style="font-size:32px; font-weight:bold;"><?= $stats['cours'] ?></p>
        </div>
        <div style="background:#fceff4; padding:25px; border-radius:8px; text-align:center;">
            <h3>Certificats</h3>
            <p style="font-size:32px; font-weight:bold;"><?= $stats['certificats'] ?></p>
        </div>
    </div>
</div>
</body>
</html>

