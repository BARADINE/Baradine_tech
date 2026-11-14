<?php
session_start();
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'apprenant') {
  //  header("Location: ../auth/login.php");
  //  exit;
//}
require '../config/db.php';

$user_id = $_SESSION['user_id'];
$cours_id = isset($_GET['cours_id']) ? intval($_GET['cours_id']) : 0;

// Vérification accès + récupération des données (sans colonne prenom)
$verif = $pdo->prepare("
    SELECT u.nom, c.titre, p.date_reussite
    FROM progression p
    JOIN utilisateurs u ON p.utilisateur_id = u.id
    JOIN cours c ON p.cours_id = c.id
    WHERE p.utilisateur_id = ? AND p.cours_id = ? AND p.certificat_deverrouille = 1
");
$verif->execute([$user_id, $cours_id]);
$data = $verif->fetch(PDO::FETCH_ASSOC);

// Si pas le droit d'accéder
if (!$data) {
    die("<h3 style='text-align:center; color:red; margin-top:50px;'>❌ Certificat verrouillé. Il faut obtenir au moins 80% pour y accéder.<br><br><a href='cours.php'>Retour aux cours</a></h3>");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Certificat - Baradine Tech</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Times New Roman', serif; }
        body {
            width: 297mm; height: 210mm;
            margin: 0 auto;
            padding: 30mm 20mm;
            background: #ffffff;
            border: 20px solid #004080;
            position: relative;
        }
        .entete {
            position: absolute;
            top: 20px; left: 50%;
            transform: translateX(-50%);
            font-size: 26px; font-weight: bold;
            color: #004080;
            text-align: center;
        }
        h1 {
            text-align: center; font-size: 38px;
            color: #004080; margin-bottom: 40px;
            text-transform: uppercase; letter-spacing: 2px;
        }
        p {
            text-align: center; font-size: 22px;
            margin: 20px 0; color: #333333;
        }
        .nom {
            font-size: 32px; font-weight: bold;
            color: #2980b9; margin: 30px 0;
            text-transform: uppercase;
        }
        .formation {
            font-size: 28px; font-weight: bold;
            color: #004080; margin: 30px 0;
        }
        .signature {
            margin-top: 70px; text-align: right;
            font-size: 18px; border-top: 1px solid #000000;
            width: 320px; margin-left: auto; padding-top: 10px;
        }
        .boutons {
            position: fixed; bottom: 20px; left: 50%;
            transform: translateX(-50%);
            text-align: center;
        }
        .btn {
            padding: 12px 25px; background: #004080;
            color: white; border: none; border-radius: 4px;
            font-size: 16px; cursor: pointer; margin: 0 10px;
        }
        .btn:hover { background: #002d5c; }

        
        @media print {
            .boutons { display: none; }
            body { border: none; padding: 10mm; }
        }
        .nom{
            text-align: center;
            width: 100%;
            display: block;
        }
    </style>
</head>
<body>
    <div class="entete">
        BARADINE TECH<br>
        UNIVERSITÉ DE N'DJAMENA
    </div>

    <h1>CERTIFICAT DE RÉUSSITE</h1>

    <p>Il est certifié que :</p>
    <div class="nom"><?= htmlspecialchars($data['nom']) ?></div>

    <p>a suivi et validé avec succès la formation :</p>
    <div class="formation"><?= htmlspecialchars($data['titre']) ?></div>

    <p>Score obtenu : <strong>≥ 80%</strong><br>
    Date de validation : <strong><?= date('d/m/Y', strtotime($data['date_reussite'])) ?></strong></p>

    <div class="signature">
        Le Directeur des Études<br>
        Université de N'Djamena<br>
        Baradine Tech
    </div>

    <div class="boutons">
        <button class="btn" onclick="window.print()">🖨️ Imprimer / Enregistrer en PDF</button>
        <br><br>
        <a href="cours.php" style="color:#004080; text-decoration:none; font-weight:bold;">← Retour à mes cours</a>
    </div>
</body>
</html>

