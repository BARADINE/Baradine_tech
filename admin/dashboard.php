<?php
session_start();
//if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
   // header("Location: ../auth/login.php"); exit;
//}
require '../config/db.php';

$stats = [
    'utilisateurs' => $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn(),
    'cours' => $pdo->query("SELECT COUNT(*) FROM cours")->fetchColumn(),
    'certificats' => $pdo->query("SELECT COUNT(*) FROM certificats")->fetchColumn()
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord Admin | Baradine Tech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Baradine Tech - Administration</h1>
        <p>Bienvenue, <?= $_SESSION['nom'] ?></p>
    </div>
    <div class="nav">
        <a href="dashboard.php">Accueil</a>
        <a href="utilisateurs.php">Utilisateurs</a>
        <a href="cours.php">Cours</a>
        <a href="quiz.php">Quiz</a>
        <a href="certificats.php">Certificats</a>
        <a href="statistiques.php">Statistiques</a>
        <a href="../auth/logout.php" style="color:#ffd;">Déconnexion</a>
    </div>
    <div class="card">
        <h3>Vue d'ensemble</h3>
        <div style="display:flex; gap:20px; margin-top:20px;">
            <div style="background:#e7f0ff; padding:20px; border-radius:6px; flex:1; text-align:center;">
                <h4>Utilisateurs</h4>
                <p style="font-size:24px; font-weight:bold;"><?= $stats['utilisateurs'] ?></p>
            </div>
            <div style="background:#e7f9ed; padding:20px; border-radius:6px; flex:1; text-align:center;">
                <h4>Cours</h4>
                <p style="font-size:24px; font-weight:bold;"><?= $stats['cours'] ?></p>
            </div>
            <div style="background:#fffbe7; padding:20px; border-radius:6px; flex:1; text-align:center;">
                <h4>Certificats</h4>
                <p style="font-size:24px; font-weight:bold;"><?= $stats['certificats'] ?></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>

