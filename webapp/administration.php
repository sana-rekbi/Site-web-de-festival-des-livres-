<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            text-align: center;
        }

        h1 {
            margin-top: 50px;
            color: #333;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
        }
        .links {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .links a {
            text-decoration: none;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .links a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div>
            <h1>Bienvenue sur la zone d'administration</h1>
            <div class="links">
                <a href="bdd.php">Afficher tous les membres</a>
                <a href="ajouter_ouvrage.php">Ajouter un ouvrage</a>
                <a href="ajouter_campagne.php">Créer une nouvelle campagne</a>
                <a href="intervention.php">Gestion des interventions</a> 
                <a href="res_intervention.php">Liste des interventions</a> 
            </div>
        </div>
    </div>
</body>
</html>
