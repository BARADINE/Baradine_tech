<?php
session_start();
//if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'formateur') {
  //  header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';

$id = intval($_GET['id'] ?? 0);
$formateur_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("DELETE FROM cours WHERE id = ? AND formateur_id = ?");
$stmt->execute([$id, $formateur_id]);

header("Location: dashboard.php?supprime=1");
exit;
?>

