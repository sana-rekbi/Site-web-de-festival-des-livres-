<?php
session_start();
// Connexion à la base de données PostgreSQL
$dsn = "pgsql:host=localhost;port=5432;dbname=CSI;user=postgres;password=Minouche57";
try {
    $bdd = new PDO($dsn);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
}

// Vérification de la réussite de la connexion à la base de données
if (!$bdd) {
    echo "Erreur de connexion à la base de données.";
    exit; // Arrêt de l'exécution du script en cas d'échec de connexion
}

// Traitement du formulaire de création de campagne
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $date_limite = $_POST['date_limite'];
    $num_commission = $_POST['num_commission'];
    $num_edition = $_POST['num_edition'];

    try {
        // Préparation de la requête d'insertion
        $query = "INSERT INTO Campagne_voeux (date_limite, num_commission, num_edition) VALUES (:date_limite, :num_commission, :num_edition)";
        $stmt = $bdd->prepare($query);

        // Liaison des paramètres
        $stmt->bindParam(':date_limite', $date_limite);
        $stmt->bindParam(':num_commission', $num_commission);
        $stmt->bindParam(':num_edition', $num_edition);

        // Exécution de la requête
        $stmt->execute();

        echo "Campagne de voeux créée avec succès !";
    } catch (PDOException $e) {
        echo "Erreur lors de la création de la campagne de voeux : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création de campagne de voeux</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1 {
            color: #ff6600;
            text-align: center;
        }

        form {
            width: 50%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #333;
        }

        input[type="date"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #ff6600;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #ff7f00;
        }
    </style>
</head>
<body>
    
    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center w-100">
                <!-- Titre de la barre de navigation -->
                <a class="navbar-brand" href="#">Festival littéraire international</a>
                <div class="search-bar">
                    <!-- Barre de recherche -->
                    <input type="text" name="menu" class="search-input" placeholder="Search menu..">
                    <button type="button" id="search-button">
                        <!-- Icône de recherche -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                        </svg>
                    </button>
                    <!-- Lien avec l'icône de profil utilisateur -->
                    <a href="#" class="<?php echo (isset($_SESSION['username'])) ? 'blue-icon' : 'orange-icon'; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Menu de navigation -->
    <nav class="navbar navbar-expand-md navbar-light bg-light flex-column">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mx-auto flex-wrap">
                <!-- Liens du menu -->
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Accueil
                        <!-- Icône d'accueil -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-door-fill" viewBox="0 0 16 16">
                            <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5"/>
                        </svg>
                    </a>   
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="bdd.php">Bannir</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="ajouter_ouvrage.php">Ouvrage</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="ajouter_campagne.php">Campagne</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="intervention.php">Intervention</a> <!-- Ajout du lien vers intervention.php -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="res_intervention.php">Liste Intervention</a> <!-- Lien vers la page intervention.php -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Déconnexion
                        <!-- Icône de déconnexion -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M12.354 7.354a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0 .5.5 0 0 1 0-.708L10.793 8H4.5a.5.5 0 0 1 0-1h6.293l-2.147-2.146a.5.5 0 0 1 .708-.708l3 3a.5.5 0 0 1 0 .708z"/>
                            <path fill-rule="evenodd" d="M3.5 2A1.5 1.5 0 0 1 5 0h6a1.5 1.5 0 0 1 1.5 1.5v4a.5.5 0 0 1-1 0V1.5A.5.5 0 0 0 11 1H5a.5.5 0 0 0-.5.5v11a.5.5 0 0 0 .5.5h6a.5.5 0 0 0 .5-.5V11a.5.5 0 0 1 1 0v2a1.5 1.5 0 0 1-1.5 1.5h-6A1.5 1.5 0 0 1 3 13V2z"/>
                        </svg>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Contenu principal -->
    <h1>Création de campagne de voeux</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="date_limite">Date limite :</label>
        <input type="date" id="date_limite" name="date_limite" required><br><br>
        
        <label for="num_commission">Numéro de commission scolaire :</label>
        <input type="number" id="num_commission" name="num_commission" required><br><br>
        
        <label for="num_edition">Numéro d'édition :</label>
        <input type="number" id="num_edition" name="num_edition" required><br><br>
        
        <input type="submit" value="Créer la campagne de voeux">
    </form>
</body>

</html>
