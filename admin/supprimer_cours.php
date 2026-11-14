<?php
session_start();
//if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  //  header("Location: ../auth/login.php");
    //exit;
//}
require '../config/db.php';

// Récupérer l'ID du cours
$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    try {
       
        $delete = $pdo->prepare("DELETE FROM cours WHERE id = ?");
        $delete->execute([$id]);
        header("Location: cours.php?supprime=1");
        exit;
    } catch (Exception $e) {
        die("Erreur lors de la suppression : " . $e->getMessage());
    }
} else {
    header("Location: cours.php");
    exit;
}
?>

