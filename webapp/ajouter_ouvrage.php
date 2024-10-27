<?php
session_start();

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Récupérer les données du formulaire
    $nom_ouvrage = $_POST['nom_ouvrage'];
    $public_cible = $_POST['public_cible'];
    $num_campagne_de_voeux = $_POST['num_campagne_de_voeux'];
    $code_auteur = $_POST['code_auteur'];

    // Connexion à la base de données PostgreSQL
    $dsn = "pgsql:host=localhost;port=5432;dbname=CSI;user=postgres;password=Minouche57";
    try {
        $bdd = new PDO($dsn);
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Erreur de connexion : " . $e->getMessage();
        exit;
    }

    // Vérifier si le numéro de campagne de voeux existe dans la base de données
    $query_check_campagne = "SELECT COUNT(*) FROM Campagne_voeux WHERE num_campagne_de_voeux = :num_campagne_de_voeux";
    $stmt_check_campagne = $bdd->prepare($query_check_campagne);
    $stmt_check_campagne->bindParam(':num_campagne_de_voeux', $num_campagne_de_voeux);
    $stmt_check_campagne->execute();
    $campagne_count = $stmt_check_campagne->fetchColumn();

    // Vérifier si le code_auteur existe dans la base de données
    $query_check_auteur = "SELECT COUNT(*) FROM Auteur WHERE code_auteur = :code_auteur";
    $stmt_check_auteur = $bdd->prepare($query_check_auteur);
    $stmt_check_auteur->bindParam(':code_auteur', $code_auteur);
    $stmt_check_auteur->execute();
    $auteur_count = $stmt_check_auteur->fetchColumn();

    // Vérification des conditions d'existence du numéro de campagne de voeux et du code de l'auteur
    if ($campagne_count == 0) {
        echo "Erreur : le numéro de campagne de voeux spécifié n'existe pas.";
    } elseif ($auteur_count == 0) {
        echo "Erreur : le code de l'auteur spécifié n'existe pas.";
    } else {
        // Préparation de la requête d'insertion
        $query = "INSERT INTO Ouvrage (nom_ouvrage, public_cible, num_campagne_de_voeux, code_auteur) VALUES (:nom_ouvrage, :public_cible, :num_campagne_de_voeux, :code_auteur)";
        $stmt = $bdd->prepare($query);

        // Liaison des paramètres
        $stmt->bindParam(':nom_ouvrage', $nom_ouvrage);
        $stmt->bindParam(':public_cible', $public_cible);
        $stmt->bindParam(':num_campagne_de_voeux', $num_campagne_de_voeux);
        $stmt->bindParam(':code_auteur', $code_auteur);

        // Exécution de la requête
        try {
            $stmt->execute();
            echo "L'ouvrage a été ajouté avec succès.";
        } catch (PDOException $e) {
            echo "Erreur lors de l'ajout de l'ouvrage : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Ajouter un ouvrage</title>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #ff6600;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: transparent;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'%3E%3Cpolygon points='480 64 32 256 480 448 480 64' fill='%23333'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position-x: calc(100% - 15px);
            background-position-y: center;
            cursor: pointer;
        }

        button {
            background-color: #ff6600;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #ff7f00;
        }
    </style>
</head>
<body>
    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <!-- Brand -->
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center w-100">
                <a class="navbar-brand" href="#">Festival littéraire international</a>
                <!-- Search bar -->
                <div class="search-bar">
                    <input type="text" name="menu" class="search-input" placeholder="Search menu..">
                    <button type="button" id="search-button">
                        <!-- Search icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                        </svg>
                    </button>
                    <!-- User icon -->
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

    <!-- Navigation bar for smaller screens -->
    <nav class="navbar navbar-expand-md navbar-light bg-light flex-column">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mx-auto flex-wrap">
                <!-- List items -->
                <li class="nav-item active">
                    <!-- Home link -->
                    <a class="nav-link" href="index.php">Accueil
                        <!-- Home icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-door-fill" viewBox="0 0 16 16">
                            <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5"/>
                        </svg>
                    </a>   
                </li>
                <!-- Other navigation links -->
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
                        <!-- Logout icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M12.354 7.354a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0 .5.5 0 0 1 0-.708L10.793 8H4.5a.5.5 0 0 1 0-1h6.293l-2.147-2.146a.5.5 0 0 1 .708-.708l3 3a.5.5 0 0 1 0 .708z"/>
                            <path fill-rule="evenodd" d="M3.5 2A1.5 1.5 0 0 1 5 0h6a1.5 1.5 0 0 1 1.5 1.5v4a.5.5 0 0 1-1 0V1.5A.5.5 0 0 0 11 1H5a.5.5 0 0 0-.5.5v11a.5.5 0 0 0 .5.5h6a.5.5 0 0 0 .5-.5V11a.5.5 0 0 1 1 0v2a1.5 1.5 0 0 1-1.5 1.5h-6A1.5 1.5 0 0 1 3 13V2z"/>
                        </svg>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Form to add a book -->
    <div class="container">
        <h2>Ajouter un ouvrage</h2>
        <form id="ajouterOuvrageForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <!-- Book title -->
            <label for="nom_ouvrage">Nom de l'ouvrage :</label>
            <input type="text" id="nom_ouvrage" name="nom_ouvrage" required>
            
            <!-- Target audience -->
            <label for="public_cible">Public cible :</label>
            <select id="public_cible" name="public_cible" required>
                <option value="" disabled selected>Sélectionnez le public cible</option>
                <option value="Enfant">Enfant</option>
                <option value="Adulte">Adulte</option>
            </select>
            
            <!-- Campaign number -->
            <label for="num_campagne_de_voeux">Numéro de campagne de voeux :</label>
            <input type="number" id="num_campagne_de_voeux" name="num_campagne_de_voeux" required>
            
            <!-- Author code -->
            <label for="code_auteur">Code de l'auteur :</label>
            <input type="number" id="code_auteur" name="code_auteur" required>
            
            <!-- Submit button -->
            <button type="submit" name="submit">Ajouter</button>

        </form>
    </div>

    <!-- JavaScript for form validation -->
    <script>
        function validateForm() {
            var nomOuvrage = document.getElementById("nom_ouvrage").value;
            var publicCible = document.getElementById("public_cible").value;
            var numCampagne = document.getElementById("num_campagne_de_voeux").value;
            var codeAuteur = document.getElementById("code_auteur").value;

            // Vérifier si tous les champs sont remplis
            if (nomOuvrage == "" || publicCible == "" || numCampagne == "" || codeAuteur == "") {
                alert("Veuillez remplir tous les champs du formulaire.");
                return;
            }

            // Vérifier si le numéro de campagne de voeux est un nombre
            if (isNaN(numCampagne)) {
                alert("Le numéro de campagne de voeux doit être un nombre.");
                return;
            }

            // Vérifier si le code de l'auteur est un nombre
            if (isNaN(codeAuteur)) {
                alert("Le code de l'auteur doit être un nombre.");
                return;
            }

            // Si tout est correct, soumettre le formulaire
            document.getElementById("ajouterOuvrageForm").submit();
        }
    </script>
</body>

</html>
