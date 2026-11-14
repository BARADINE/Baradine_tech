<?php
session_start();
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'formateur') {
  //  header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$cours_id = isset($_GET['cours_id']) ? intval($_GET['cours_id']) : 0;

$verif = $pdo->prepare("
    SELECT q.id FROM questions q
    JOIN cours c ON q.cours_id = c.id
    WHERE q.id = ? AND c.formateur_id = ?
");
$verif->execute([$id, $_SESSION['user_id']]);

if ($verif->rowCount() > 0) {
    $pdo->prepare("DELETE FROM questions WHERE id = ?")->execute([$id]);
}

header("Location: gerer_quiz.php?cours_id=" . $cours_id);
exit;
?>

