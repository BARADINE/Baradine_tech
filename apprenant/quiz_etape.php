<?php
session_start();
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'apprenant') {
   // header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';

$user_id = $_SESSION['user_id'];
$cours_id = isset($_GET['cours_id']) ? intval($_GET['cours_id']) : 0;
$etape = isset($_GET['etape']) ? intval($_GET['etape']) : 1;

if ($cours_id <= 0) die("Cours invalide.");


$verif = $pdo->prepare("
    SELECT p.module_termine FROM progression p
    JOIN inscriptions i ON p.utilisateur_id = i.utilisateur_id AND p.cours_id = i.cours_id
    WHERE i.utilisateur_id = ? AND i.cours_id = ?
");
$verif->execute([$user_id, $cours_id]);
$d = $verif->fetch(PDO::FETCH_ASSOC);
if (!$d || $d['module_termine'] != 1) {
    die("❌ Examen verrouillé : terminez d'abord le module. <a href='module.php?cours_id=$cours_id'>Retour</a>");
}

if (!isset($_SESSION['questions_quiz'][$cours_id])) {
    $questions = $pdo->prepare("SELECT id, question, reponse_a, reponse_b, reponse_c, reponse_d FROM questions WHERE cours_id = ? ORDER BY RAND() LIMIT 50");
    $questions->execute([$cours_id]);
    $_SESSION['questions_quiz'][$cours_id] = $questions->fetchAll(PDO::FETCH_ASSOC);
    $_SESSION['reponses_quiz'][$cours_id] = [];
}

$liste = $_SESSION['questions_quiz'][$cours_id];
$total = count($liste);
if ($total === 0) die("Aucune question disponible.");


if ($_SERVER['REQUEST_METHOD'] === 'POST' && $etape > 1) {
    $rep = isset($_POST['reponse']) ? strtoupper(trim($_POST['reponse'])) : "";
    $_SESSION['reponses_quiz'][$cours_id][$etape - 2] = $rep;
}

if ($etape > $total) {
    $bonnes = 0;
    foreach ($liste as $index => $q) {
        $r = $pdo->prepare("SELECT bonne_reponse FROM questions WHERE id = ?");
        $r->execute([$q['id']]);
        $bonne = $r->fetchColumn();
        if (isset($_SESSION['reponses_quiz'][$cours_id][$index]) && $_SESSION['reponses_quiz'][$cours_id][$index] === $bonne) {
            $bonnes++;
        }
    }
    $score = round(($bonnes / $total) * 100);
    $reussi = ($score >= 80);

    $maj = $pdo->prepare("
        INSERT INTO progression (utilisateur_id, cours_id, score, certificat_deverrouille, date_reussite)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            score = VALUES(score),
            certificat_deverrouille = VALUES(certificat_deverrouille),
            date_reussite = VALUES(date_reussite)
    ");
    $maj->execute([
        $user_id,
        $cours_id,
        $score,
        $reussi ? 1 : 0,
        $reussi ? date("Y-m-d H:i:s") : null
    ]);


    unset($_SESSION['questions_quiz'][$cours_id], $_SESSION['reponses_quiz'][$cours_id]);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultat Examen</title>
    <style>
        body { font-family:Arial; max-width:600px; margin:50px auto; padding:20px; text-align:center; }
        .ok { background:#d4edda; color:#155724; padding:30px; border-radius:8px; }
        .ko { background:#f8d7da; color:#721c24; padding:30px; border-radius:8px; }
        .btn { display:inline-block; margin:15px; padding:10px 20px; background:#004080; color:white; text-decoration:none; border-radius:4px; }
    </style>
</head>
<body>
    <?php if ($reussi): ?>
        <div class="ok">
            <h2>✅ Félicitations !</h2>
            <p>Votre score : <strong><?= $score ?> %</strong></p>
            <p>Vous avez réussi l'examen. Votre certificat est maintenant déverrouillé.</p>
            <a href="generer_certificat.php?cours_id=<?= $cours_id ?>" class="btn" target="_blank">📜 Télécharger le certificat</a>
        </div>
    <?php else: ?>
        <div class="ko">
            <h2> Redoublant</h2>
            <p>Votre score : <strong><?= $score ?> %</strong></p>
            <p>Seuil de réussite : 80% minimum.</p>
            <p>Vous pouvez repasser l'examen autant de fois que vous voulez.</p>
        </div>
    <?php endif; ?>
    <a href="cours.php" class="btn">Retour à mes cours</a>
</body>
</html>
<?php
    exit;
}


$q = $liste[$etape - 1];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Examen - Question <?= $etape ?> / <?= $total ?></title>
    <style>
        body { font-family:Arial; max-width:700px; margin:40px auto; padding:20px; }
        .progression { text-align:right; color:#666; margin-bottom:20px; font-weight:bold; }
        .question { background:#f9fafc; padding:25px; border-radius:8px; margin-bottom:20px; border-left:4px solid #004080; }
        .reponse { margin:12px 0; font-size:16px; }
        button { background:#004080; color:white; border:none; padding:12px 25px; border-radius:4px; font-size:16px; cursor:pointer; }
    </style>
</head>
<body>
    <div class="progression">Question <?= $etape ?> sur <?= $total ?></div>
    <form method="post" action="quiz_etape.php?cours_id=<?= $cours_id ?>&etape=<?= $etape + 1 ?>">
        <div class="question">
            <h3><?= htmlspecialchars($q['question']) ?></h3>
            <div class="reponse"><label><input type="radio" name="reponse" value="A" required> <?= htmlspecialchars($q['reponse_a']) ?></label></div>
            <div class="reponse"><label><input type="radio" name="reponse" value="B" required> <?= htmlspecialchars($q['reponse_b']) ?></label></div>
            <div class="reponse"><label><input type="radio" name="reponse" value="C" required> <?= htmlspecialchars($q['reponse_c']) ?></label></div>
            <div class="reponse"><label><input type="radio" name="reponse" value="D" required> <?= htmlspecialchars($q['reponse_d']) ?></label></div>
        </div>
        <button type="submit">Question suivante </button>
    </form>
</body>
</html>

