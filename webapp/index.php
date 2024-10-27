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

// Récupérer les ouvrages à afficher sur la page d'accueil depuis la base de données
$sql_accueil = "SELECT * FROM Ouvrage";
$stmt_accueil = $bdd->query($sql_accueil);

// Vérifier si la requête a réussi
if ($stmt_accueil) {
    // Récupération des ouvrages à afficher sur la page d'accueil
    $ouvrages_accueil = $stmt_accueil->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Erreur lors de la récupération des ouvrages pour la page d'accueil.";
    $ouvrages_accueil = array(); // Définition d'une liste vide en cas d'erreur
}

// Vérifier si le formulaire d'ajout d'ouvrage a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Récupérer les données du formulaire
    $nom_ouvrage = $_POST['nom_ouvrage'];
    $public_cible = $_POST['public_cible'];
    $num_campagne_de_voeux = $_POST['num_campagne_de_voeux'];
    $code_auteur = $_POST['code_auteur'];

    // Préparer la requête d'insertion
    $sql = "INSERT INTO Ouvrage (nom_ouvrage, public_cible, num_campagne_de_voeux, code_auteur) 
            VALUES (:nom_ouvrage, :public_cible, :num_campagne_de_voeux, :code_auteur)";
    $stmt = $bdd->prepare($sql);

    // Liaison des paramètres
    $stmt->bindParam(':nom_ouvrage', $nom_ouvrage);
    $stmt->bindParam(':public_cible', $public_cible);
    $stmt->bindParam(':num_campagne_de_voeux', $num_campagne_de_voeux);
    $stmt->bindParam(':code_auteur', $code_auteur);

    // Exécution de la requête
    try {
        $stmt->execute();
        echo "Données insérées avec succès.";
    } catch (PDOException $e) {
        echo "Erreur lors de l'insertion des données : " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styles CSS supplémentaires */
                /* Styles CSS supplémentaires */
                body {
                    background: linear-gradient(to bottom, #000000 20%, #ffffff);
            color: #333333;
        }

        .jumbotron {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('livres.jpg') no-repeat center center;
            background-size: cover;
            color: orange;
            text-align: center;
            padding: 100px 0;
            margin-bottom: 0;
        }

        .jumbotron h1 {
            font-size: 3em;
            margin-bottom: 20px;
        }

        .jumbotron p {
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        .featurette-divider {
            margin: 80px 0;
        }

        .featurette-heading {
            font-size: 50px;
            line-height: 1;
            font-weight: 500;
            color: #333;
        }

        .lead {
            font-size: 24px;
            line-height: 1.5;
            color: #666;
        }

        .card {
            border: 1px solid #333;
            border-radius: 8px;
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 20px;
        }

        .card-title {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 10px;
            color: orange;
        }

        .card-text {
            color: #fff;
        }

    </style>
</head>
<body>

    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center w-100">
                <a class="navbar-brand" href="#">Festival littéraire international</a>
                <div class="search-bar">
                    <input type="text" name="menu" class="search-input" placeholder="Search menu..">
                    <button type="button" id="search-button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                        </svg>
                    </button>
                    <!-- Lien avec l'icône -->
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
                <li class="nav-item active">
                    <!-- Lien vers la page d'accueil -->
                    <a class="nav-link" href="index.php">Acceuil<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-door-fill" viewBox="0 0 16 16">
                        <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5"/>
                    </svg></a>
                </li>
                <li class="nav-item">
                    <!-- Condition PHP pour afficher le lien de déconnexion si l'utilisateur est connecté -->
                    <?php if(isset($_SESSION['username'])) { ?>
                        <a class="nav-link" href="logout.php">Déconnexion
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M12.354 7.354a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0 .5.5 0 0 1 0-.708L10.793 8H4.5a.5.5 0 0 1 0-1h6.293l-2.147-2.146a.5.5 0 0 1 .708-.708l3 3a.5.5 0 0 1 0 .708z"/>
                                <path fill-rule="evenodd" d="M3.5 2A1.5 1.5 0 0 1 5 0h6a1.5 1.5 0 0 1 1.5 1.5v4a.5.5 0 0 1-1 0V1.5A.5.5 0 0 0 11 1H5a.5.5 0 0 0-.5.5v11a.5.5 0 0 0 .5.5h6a.5.5 0 0 0 .5-.5V11a.5.5 0 0 1 1 0v2a1.5 1.5 0 0 1-1.5 1.5h-6A1.5 1.5 0 0 1 3 13V2z"/>
                            </svg>
                        </a>
                    <!-- Sinon, afficher le lien d'inscription/connexion -->
                    <?php } else { ?>
                        <a class="nav-link" href="inscription.php">Inscription/Connexion
                            <!-- Icône d'inscription/connexion -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard2-fill" viewBox="0 0 16 16">
                                <path d="M9.5 0a.5.5 0 0 1 .5.5.5.5 0 0 0 .5.5.5.5 0 0 1 .5.5V2a.5.5 0 0 1-.5.5h-5A.5.5 0 0 1 5 2v-.5a.5.5 0 0 1 .5-.5.5.5 0 0 0 .5-.5.5.5 0 0 1 .5-.5z"/>
                                <path d="M3.5 1h.585A1.5 1.5 0 0 0 4 1.5V2a1.5 1.5 0 0 0 1.5 1.5h5A1.5 1.5 0 0 0 12 2v-.5q-.001-.264-.085-.5h.585A1.5 1.5 0 0 1 14 2.5v12a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-12A1.5 1.5 0 0 1 3.5 1"/>
                            </svg>
                        </a>
                    <?php } ?>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Bannière avec l'image -->
    <header>
        <div class="jumbotron">
            <div class="container">
                <h1 class="display-4">Bienvenue au Festival du Livre</h1>
                <p class="lead">Le plus grand festival littéraire français</p>
            </div>
        </div>
    </header>


    <!-- Section principale -->
    <div class="container">
        <!-- Événements à venir -->
        <section id="events" class="my-5">
            <h2 class="text-center mb-4">Événements à venir</h2>
            <div class="row">
                <!-- Vos cartes d'événements ici -->
            </div>
        </section>

        <!-- À propos du festival -->
        <hr class="featurette-divider">
        <section id="about" class="my-5">
            <!-- Votre contenu sur le festival -->
        </section>

        <!-- Informations pratiques -->
        <hr class="featurette-divider">
        <section id="info" class="my-5">
            <!-- Votre contenu sur les informations pratiques -->
        </section>

        <!-- Liste des Ouvrages pour la page d'accueil -->
        <h2>Ouvrages à découvrir</h2>
        <div class="row">
            <?php foreach ($ouvrages_accueil as $ouvrage): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $ouvrage['nom_ouvrage']; ?></h5>
                            <p class="card-text">Code Auteur: <?php echo $ouvrage['code_auteur']; ?></p>
                            <!-- Ajoutez d'autres détails de l'ouvrage si nécessaire -->
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- À propos du festival -->
        <hr class="featurette-divider">
        <section id="about" class="my-5">
            <div class="row featurette">
                <div class="col-md-7">
                    <h2 class="featurette-heading">À propos du Festival du Livre</h2>
                    <p class="lead">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce mollis quam nec erat malesuada, et posuere magna pretium.</p>
                </div>
                <div class="col-md-5">
                    <img src="livre.jpg" alt="À propos du festival" class="img-fluid rounded">
                </div>
            </div>
        </section>

        <!-- Informations pratiques -->
        <hr class="featurette-divider">
        <section id="info" class="my-5">
            <h2 class="text-center mb-4">Informations pratiques</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Lieu</h5>
                            <p class="card-text">Adresse du festival</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Dates</h5>
                            <p class="card-text">Dates du festival</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Contact</h5>
                            <p class="card-text">Email / Téléphone</p>
                        </div>
                    </div>
                </div>
                <!-- Ajouter la carte Google Maps ici -->
                <div class="col-md-12">
                    <div id="map" style="height: 400px;"></div>
                </div>
            </div>
        </section>


        <!-- Footer -->
        <footer class="bg-dark text-white text-center py-4">
            &copy; <?php echo date("Y"); ?> Festival du Livre. Tous droits réservés.
        </footer>

        <!-- Scripts JavaScript -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>


        <script>
          // Fonction pour initialiser la carte Leaflet
          function initMap() {
                // Coordonnées de Paris
                var paris = [48.8566, 2.3522];

                // Créer une carte avec le centre sur Paris
                var map = L.map('map').setView(paris, 12);

                // Ajouter une couche de tuiles OpenStreetMap
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                }).addTo(map);

                // Ajouter un marqueur pour l'emplacement du festival
                L.marker(paris).addTo(map)
                    .bindPopup('Festival du Livre - Paris')
                    .openPopup();
            }

            // Appel de la fonction initMap après le chargement de la page
            window.onload = function() {
                initMap();
            };

            // JavaScript pour animer les cartes d'ouvrages
            document.addEventListener("DOMContentLoaded", function () {
                const cards = document.querySelectorAll(".card");

                cards.forEach(function (card) {
                    card.addEventListener("mouseenter", function () {
                        card.style.transform = "scale(1.05)";
                    });

                    card.addEventListener("mouseleave", function () {
                        card.style.transform = "scale(1)";
                    });
                });
            });
        </script>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>

</html>