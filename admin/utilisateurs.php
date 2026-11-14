<?php
session_start();
//if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: ../auth/login.php"); exit; }
require '../config/db.php';
$utilisateurs = $pdo->query("SELECT u.*, r.nom as role FROM utilisateurs u JOIN roles r ON u.role_id = r.id ORDER BY u.date_inscription DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Utilisateurs | Baradine Tech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <div class="header"><h1>Gestion des Utilisateurs</h1></div>
    <div class="nav">
        <a href="dashboard.php">Retour</a>
        <a href="ajouter_utilisateur.php" class="btn btn-success btn-sm">Ajouter</a>
    </div>
    <div class="card">
        <table>
            <tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Date</th><th>Actions</th></tr>
            <?php foreach($utilisateurs as $u): ?>
            <tr>
                <td><?= $u['nom'] ?></td>
                <td><?= $u['email'] ?></td>
                <td><?= $u['role'] ?></td>
                <td><?= date('d/m/Y', strtotime($u['date_inscription'])) ?></td>
                <td>
                    <a href="modifier_utilisateur.php?id=<?= $u['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                    <a href="supprimer_utilisateur.php?id=<?= $u['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
</body>
</html>

