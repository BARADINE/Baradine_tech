<?php
session_start();
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'formateur') {
  //  header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';


$cours_id = isset($_GET['cours_id']) ? intval($_GET['cours_id']) : 0;


$verif_cours = $pdo->prepare("SELECT id, titre FROM cours WHERE id = ? AND formateur_id = ?");
$verif_cours->execute([$cours_id, $_SESSION['user_id']]);
$cours = $verif_cours->fetch(PDO::FETCH_ASSOC);

if (!$cours) {
    die("❌ Cours introuvable ou accès interdit.");
}

$message = "";

// Traitement de l'ajout de question
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['question']);
    $reponse_a = trim($_POST['reponse_a']);
    $reponse_b = trim($_POST['reponse_b']);
    $reponse_c = trim($_POST['reponse_c']);
    $reponse_d = trim($_POST['reponse_d']);
    $bonne_reponse = strtoupper(trim($_POST['bonne_reponse']));

    if (!empty($question) && !empty($reponse_a) && !empty($reponse_b) && !empty($reponse_c) && !empty($reponse_d) && in_array($bonne_reponse, ['A','B','C','D'])) {
        $req = $pdo->prepare("
            INSERT INTO questions (cours_id, question, reponse_a, reponse_b, reponse_c, reponse_d, bonne_reponse)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        if ($req->execute([$cours_id, $question, $reponse_a, $reponse_b, $reponse_c, $reponse_d, $bonne_reponse])) {
            $message = "Question ajoutée avec succès !";
        } else {
            $message = " Erreur lors de l'enregistrement.";
        }
    } else {
        $message = " Veuillez remplir tous les champs correctement.";
    }
}


$liste_questions = $pdo->prepare("SELECT * FROM questions WHERE cours_id = ? ORDER BY id DESC");
$liste_questions->execute([$cours_id]);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer le Quiz - <?= htmlspecialchars($cours['titre']) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        .header { background: #004080; color: white; padding: 18px 25px; font-size: 22px; }
        .container { max-width: 1000px; margin: 30px auto; padding: 0 25px; }
        .bloc { background: #f9fafc; padding: 25px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
        h2, h3 { color: #004080; margin-bottom: 20px; }
        .message { padding: 12px; border-radius: 5px; margin-bottom: 20px; }
        .succes { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .erreur { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        label { display: block; margin: 12px 0 5px; font-weight: 500; }
        input, textarea, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 15px; }
        button { background: #004080; color: white; border: none; padding: 12px 22px; border-radius: 4px; cursor: pointer; font-size: 16px; margin-top: 10px; }
        button:hover { background: #002d5c; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 14px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f2f5f9; color: #004080; }
        .btn-suppr { background: #e74c3c; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px; }
        .btn-suppr:hover { background: #c0392b; }
    </style>
</head>
<body>

<div class="header">
    <a href="dashboard.php" style="color: white; text-decoration: none; margin-right: 15px;">← Retour</a>
    Gestion du Quiz : <?= htmlspecialchars($cours['titre']) ?>
</div>

<div class="container">

    <?php if (!empty($message)): ?>
        <div class="message <?= str_contains($message, '✅') ? 'succes' : 'erreur' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <div class="bloc">
        <h3>Ajouter une nouvelle question</h3>
        <form method="post" action="">
            <label>Question :</label>
            <textarea name="question" rows="3" required></textarea>

            <label>Réponse A :</label>
            <input type="text" name="reponse_a" required>

            <label>Réponse B :</label>
            <input type="text" name="reponse_b" required>

            <label>Réponse C :</label>
            <input type="text" name="reponse_c" required>

            <label>Réponse D :</label>
            <input type="text" name="reponse_d" required>

            <label>Bonne réponse :</label>
            <select name="bonne_reponse" required>
                <option value="">-- Choisir --</option>
                <option value="A">Réponse A</option>
                <option value="B">Réponse B</option>
                <option value="C">Réponse C</option>
                <option value="D">Réponse D</option>
            </select>

            <button type="submit">Enregistrer la question</button>
        </form>
    </div>

    <div class="bloc">
        <h3>Questions existantes</h3>
        <?php if ($liste_questions->rowCount() === 0): ?>
            <p>Aucune question n'a encore été ajoutée pour ce cours.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Bonne réponse</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($q = $liste_questions->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= htmlspecialchars($q['question']) ?></td>
                            <td><?= $q['bonne_reponse'] ?></td>
                            <td>
                                <a href="supprimer_question.php?id=<?= $q['id'] ?>&cours_id=<?= $cours_id ?>" 
                                   class="btn-suppr" 
                                   onclick="return confirm('Supprimer cette question ?')">
                                   Supprimer
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</div>

</body>
</html>

