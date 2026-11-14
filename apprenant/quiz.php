<?php
session_start();
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'apprenant') {
  //  header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';

$cours_id = isset($_GET['cours_id']) ? intval($_GET['cours_id']) : 0;


$questions = $pdo->prepare("SELECT * FROM questions WHERE cours_id = ?");
$questions->execute([$cours_id]);
$liste = $questions->fetchAll(PDO::FETCH_ASSOC);

if (empty($liste)) {
    die("<div style='text-align:center; margin-top:50px;'>Aucun quiz disponible pour ce cours.<br><br><a href='mes_cours.php'>Retour aux cours</a></div>");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Quiz - Baradine Tech</title>
    <style>
        .container { max-width:800px; margin:30px auto; padding:20px; }
        .question { margin:20px 0; padding:15px; border:1px solid #eee; border-radius:6px; }
        button { background:#004080; color:white; border:none; padding:12px 25px; border-radius:4px; cursor:pointer; font-size:16px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Évaluation du cours</h2>
    <form method="post" action="correction_quiz.php?cours_id=<?= $cours_id ?>">
        <?php foreach ($liste as $q): ?>
            <div class="question">
                <p><strong><?= htmlspecialchars($q['question']) ?></strong></p>
                <p><label><input type="radio" name="q_<?= $q['id'] ?>" value="A" required> <?= htmlspecialchars($q['reponse_a']) ?></label></p>
                <p><label><input type="radio" name="q_<?= $q['id'] ?>" value="B"> <?= htmlspecialchars($q['reponse_b']) ?></label></p>
                <p><label><input type="radio" name="q_<?= $q['id'] ?>" value="C"> <?= htmlspecialchars($q['reponse_c']) ?></label></p>
                <p><label><input type="radio" name="q_<?= $q['id'] ?>" value="D"> <?= htmlspecialchars($q['reponse_d']) ?></label></p>
            </div>
        <?php endforeach; ?>
        <button type="submit">Valider mes réponses</button>
    </form>
</div>
</body>
</html>

