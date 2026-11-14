<?php
session_start();
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'apprenant') {
  //  header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';
$cours_id = isset($_GET['cours_id']) ? intval($_GET['cours_id']) : 0;
if ($cours_id <= 0) die("Cours invalide.");

$ajout = $pdo->prepare("INSERT IGNORE INTO inscriptions (utilisateur_id, cours_id) VALUES (?, ?)");
$ajout->execute([$_SESSION['user_id'], $cours_id]);

header("Location: cours.php?success=1");
exit;

