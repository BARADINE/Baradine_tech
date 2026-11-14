<?php
session_start();
//if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: ../auth/login.php"); exit; }
require '../config/db.php';

$cours = $pdo->query("SELECT c.*, u.nom AS formateur_nom FROM cours c LEFT JOIN utilisateurs u ON c.formateur_id = u.id ORDER BY c.date_creation DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Cours | Baradine Tech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <div class="header"><h1>Gestion des Cours</h1></div>
    <div class="nav">
        <a href="dashboard.php">← Retour</a>
        <a href="ajouter_cours.php" class="btn btn-success btn-sm">Ajouter un cours</a>
    </div>
    <div class="card">
        <table>
            <tr><th>Titre</th><th>Formateur</th><th>Prix</th><th>Date</th><th>Actions</th></tr>
            <?php foreach($cours as $c): ?>
            <tr>
                <td><?= $c['titre'] ?></td>
                <td><?= $c['formateur_nom'] ?? 'Non assigné' ?></td>
                <td><?= $c['prix'] ?> FCFA</td>
                <td><?= date('d/m/Y', strtotime($c['date_creation'])) ?></td>
                <td>
                    <a href="modifier_cours.php?id=<?= $c['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                    <a href="supprimer_cours.php?id=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce cours ?')">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
</body>
</html>

