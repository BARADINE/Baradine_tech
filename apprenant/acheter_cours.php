<?php
session_start();
//if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'apprenant') { header("Location: ../auth/login.php"); exit; }
require '../config/db.php';

$cours_id = intval($_GET['id'] ?? 0);
$apprenant_id = $_SESSION['user_id'];


$check = $pdo->prepare("SELECT * FROM progression WHERE apprenant_id = ? AND cours_id = ?");
$check->execute([$apprenant_id, $cours_id]);

if ($check->rowCount() === 0) {
   
    $stmt = $pdo->prepare("INSERT INTO progression (apprenant_id, cours_id, pourcentage, termine) VALUES (?, ?, 0, 0)");
    $stmt->execute([$apprenant_id, $cours_id]);
}

header("Location: module.php?cours_id=$cours_id");
exit;
?>

