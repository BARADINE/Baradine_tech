<?php
session_start();
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'apprenant') {
    //header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';

$cours_id = isset($_GET['cours_id']) ? intval($_GET['cours_id']) : 0;

if ($cours_id <= 0) {
    die(" Cours invalide.");
}


$req = $pdo->prepare("SELECT id, bonne_reponse FROM questions WHERE cours_id = ?");
$req->execute([$cours_id]);
$questions = $req->fetchAll(PDO::FETCH_ASSOC);

if (empty($questions)) {
    die(" Aucune question trouvée pour ce quiz.");
}


$total = count($questions);
$bonnes_reponses = 0;

foreach ($questions as $q) {
    $nom_champ = "q_" . $q['id'];
    $reponse = isset($_POST[$nom_champ]) ? strtoupper(trim($_POST[$nom_champ])) : "";
    if ($reponse === $q['bonne_reponse']) {
        $bonnes_reponses++;
    }
}

$score = round(($bonnes_reponses / $total) * 100);
$reussi = ($score >= 50);


$enreg = $pdo->prepare("
    INSERT INTO progression (utilisateur_id, cours_id, score, date_passage)
    VALUES (?, ?, ?, NOW())
    ON DUPLICATE KEY UPDATE score = ?, date_passage = NOW()
");
$enreg->execute([$_SESSION['user_id'], $cours_id, $score, $score]);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultat du Quiz</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial, sans-serif; }
        .header { background:#004080; color:white; padding:18px 25px; font-size:22px; }
        .container { max-width:700px; margin:40px auto; padding:0 20px; text-align:center; }
        .box { padding:30px; border-radius:8px; margin-bottom:20px; }
        .reussi { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
        .echec { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
        .score { font-size:40px; font-weight:bold; margin:20px 0; }
        .btn { display:inline-block; padding:12px 25px; background:#004080; color:white; text-decoration:none; border-radius:4px; }
        .btn:hover { background:#002d5c; }
    </style>
</head>
<body>

<div class="header">
    <a href="cours.php" style="color:white; text-decoration:none;">← Retour aux cours</a>
    &nbsp; | &nbsp; Résultat du Quiz
</div>

<div class="container">
    <div class="box <?= $reussi ? 'reussi' : 'echec' ?>">
        <h2><?= $reussi ? " C'est réussi !" : " À refaire" ?></h2>
        <div class="score"><?= $score ?> %</div>
        <p>Vous avez <?= $bonnes_reponses ?> bonne(s) réponse(s) sur <?= $total ?> questions.</p>
    </div>
    <a href="cours.php" class="btn">Retour à mes cours</a>
</div>

</body>
</html>

