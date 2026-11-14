<?php
session_start();
//if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'formateur') {
  //  header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';

$module_id = intval($_GET['module_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $note_min = intval($_POST['note_minimale']);
    $q = trim($_POST['question']);
    $a = trim($_POST['choix_a']);
    $b = trim($_POST['choix_b']);
    $c = trim($_POST['choix_c']);
    $d = trim($_POST['choix_d']);
    $bonne = $_POST['bonne_reponse'];

  
    $pdo->prepare("INSERT INTO quiz (module_id, titre, note_minimale) VALUES (?, ?, ?)")
        ->execute([$module_id, $titre, $note_min]);
    $quiz_id = $pdo->lastInsertId();

  
    $pdo->prepare("INSERT INTO questions (quiz_id, question, choix_a, choix_b, choix_c, choix_d, bonne_reponse)
                   VALUES (?, ?, ?, ?, ?, ?, ?)")
        ->execute([$quiz_id, $q, $a, $b, $c, $d, $bonne]);

    $succes = "Quiz ajouté avec succès !";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Quiz | Baradine Tech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <div class="header"><h1>Créer un Quiz</h1></div>
    <div class="nav"><a href="dashboard.php">← Retour</a></div>
    <div class="card" style="max-width:750px; margin:0 auto;">
        <?php if(isset($succes)) echo "<p style='color:green;'>$succes</p>"; ?>
        <form method="post">
            <label>Titre du quiz :</label>
            <input type="text" name="titre" required>
            <label>Note minimale pour valider (/20) :</label>
            <input type="number" name="note_minimale" value="10" min="0" max="20">
            <hr style="margin:15px 0;">
            <h4>Question 1</h4>
            <label>Énoncé :</label>
            <textarea name="question" rows="3" required></textarea>
            <label>Réponse A :</label>
            <input type="text" name="choix_a" required>
            <label>Réponse B :</label>
            <input type="text" name="choix_b" required>
            <label>Réponse C :</label>
            <input type="text" name="choix_c" required>
            <label>Réponse D :</label>
            <input type="text" name="choix_d" required>
            <label>Bonne réponse :</label>
            <select name="bonne_reponse" required>
                <option value="a">A</option>
                <option value="b">B</option>
                <option value="c">C</option>
                <option value="d">D</option>
            </select>
            <button type="submit" class="btn btn-success" style="margin-top:15px;">Enregistrer le quiz</button>
        </form>
    </div>
</div>
</body>
</html>

