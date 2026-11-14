<?php
session_start();
//if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: ../auth/login.php"); exit; }
require '../config/db.php';

$quiz = $pdo->query("SELECT q.*, m.titre AS module, c.titre AS cours FROM quiz q JOIN modules m ON q.module_id = m.id JOIN cours c ON m.cours_id = c.id ORDER BY q.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Quiz | Baradine Tech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <div class="header"><h1>Liste des Quiz</h1></div>
    <div class="nav"><a href="dashboard.php">← Retour</a></div>
    <div class="card">
        <table>
            <tr><th>Quiz</th><th>Module</th><th>Cours</th><th>Note minimale</th></tr>
            <?php foreach($quiz as $q): ?>
            <tr>
                <td><?= $q['titre'] ?></td>
                <td><?= $q['module'] ?></td>
                <td><?= $q['cours'] ?></td>
                <td><?= $q['note_minimale'] ?> / 20</td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
</body>
</html>

