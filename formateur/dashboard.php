<?php
session_start();
// Vérification de la connexion et du rôle
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'formateur') {
  //  header("Location: ../auth/login.php");
    //exit;
//}

require '../config/db.php';

// Récupération des cours créés par ce formateur
$req = $pdo->prepare("
    SELECT id, titre, description, prix, date_creation 
    FROM cours 
    WHERE formateur_id = ? 
    ORDER BY date_creation DESC
");
$req->execute([$_SESSION['user_id'] ?? 1]);
$cours = $req->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baradine Tech - Espace Formateur</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        /* En-tête principal */
        .header {
            background-color: #004080;
            color: white;
            padding: 18px 25px;
            font-size: 24px;
            font-weight: bold;
        }

        /* Barre de navigation */
        .nav {
            background-color: #0059b3;
            padding: 12px 25px;
        }

        .nav a {
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            margin-right: 8px;
            display: inline-block;
            border-radius: 4px;
            font-weight: 500;
        }

        .nav a:hover {
            background-color: #003366;
        }

        .nav a.deconnexion {
            background-color: #dc3545;
            float: right;
        }

        .nav a.deconnexion:hover {
            background-color: #b02a37;
        }

        /* Contenu principal */
        .container {
            max-width: 1150px;
            margin: 35px auto;
            padding: 0 25px;
        }

        h2 {
            color: #004080;
            margin-bottom: 20px;
            font-size: 22px;
        }

        /* Tableau des cours */
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 6px;
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #f2f5f9;
            font-weight: bold;
            color: #004080;
        }

        tr:hover {
            background-color: #f8fafc;
        }

        /* Boutons d'action */
        .btn {
            padding: 7px 12px;
            border: none;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            display: inline-block;
            margin-right: 5px;
        }

        .btn-modifier { background-color: #f39c12; }
        .btn-modules  { background-color: #2980b9; }
        .btn-quiz     { background-color: #8e44ad; }
        .btn-supprimer{ background-color: #e74c3c; }

        .btn:hover {
            opacity: 0.9;
        }

        /* Message si aucun cours */
        .aucun {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>

    <!-- En-tête -->
    <div class="header">Baradine Tech - Espace Formateur</div>

    <!-- Navigation -->
    <div class="nav">
        <a href="dashboard.php">Mes cours</a>
        <a href="../auth/logout.php" class="deconnexion">Déconnexion</a>
    </div>

  
    <div class="container">
        <h2>Mes cours créés</h2>

        <?php if (empty($cours)): ?>
            <div class="aucun">Vous n'avez pas encore créé de cours.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Titre du cours</th>
                        <th>Prix</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cours as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['titre']) ?></td>
                            <td><?= number_format($c['prix'], 2) ?> FCFA</td>
                            <td><?= date('d/m/Y', strtotime($c['date_creation'])) ?></td>
                            <td>
                                <a href="ajouter_cours.php" class="btn btn-modules">ajouter cours</a>
                                <a href="modifier_cours.php?id=<?= $c['id'] ?>" class="btn btn-modifier">Modifier</a>
                                <a href="ajouter_module.php" class="btn btn-modules">Modules</a>
                                <a href="gerer_quiz.php?cours_id=<?= $c['id'] ?>" class="btn btn-quiz">Quiz / QCM</a>
                                <a href="supprimer_cours.php?id=<?= $c['id'] ?>" class="btn btn-supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce cours ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</body>
</html>

