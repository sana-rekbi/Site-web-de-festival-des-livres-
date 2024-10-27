<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affichage des interventions</title>
    <!-- Inclure Bootstrap pour le style -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
     <!-- Barre de navigation principale -->
     <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center w-100">
                <!-- Marque de la navbar -->
                <a class="navbar-brand" href="#">Festival littéraire international</a>
                <!-- Barre de recherche -->
                <div class="search-bar">
                    <input type="text" name="menu" class="search-input" placeholder="Search menu..">
                    <button type="button" id="search-button">
                        <!-- Icône de recherche -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                        </svg>
                    </button>
                    <!-- Icône utilisateur -->
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

    <!-- Barre de navigation pour les écrans plus petits -->
    <nav class="navbar navbar-expand-md navbar-light bg-light flex-column">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mx-auto flex-wrap">
                <!-- Lien vers la page d'accueil -->
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Accueil
                        <!-- Icône d'accueil -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-door-fill" viewBox="0 0 16 16">
                            <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5"/>
                        </svg>
                    </a>   
                </li>
                <!-- Liens vers les différentes pages -->
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
                    <a class="nav-link" href="intervention.php">Intervention</a> <!-- Lien vers la page intervention.php -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="res_intervention.php">Liste Intervention</a> <!-- Lien vers la page intervention.php -->
                </li>
                <!-- Lien de déconnexion -->
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
    <div class="container mt-5">
        <h2 class="mb-4">Liste des interventions</h2>
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Numéro</th>
                    <th scope="col">Durée</th>
                    <th scope="col">Date Dernière Modification</th>
                    <th scope="col">Date</th>
                    <th scope="col">État</th>
                    <th scope="col">Type</th>
                    <th scope="col">Compteur</th>
                    <th scope="col">Date de début</th>
                    <th scope="col">Date de fin</th>
                    <th scope="col">Adresse</th>
                    <th scope="col">Accompagnateur</th>
                    <th scope="col">Établissement</th>
                    <th scope="col">Auteur</th>
                    <th scope="col">Interprète</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Connexion à la base de données PostgreSQL
                $dsn = "pgsql:host=localhost;port=5432;dbname=CSI;user=postgres;password=Minouche57";
                try {
                    $bdd = new PDO($dsn);
                    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    echo "Erreur de connexion : " . $e->getMessage();
                    exit;
                }

                // Requête SQL pour récupérer les interventions
                $sql = "SELECT * FROM Intervention";

                try {
                    // Préparation de la requête
                    $stmt = $bdd->prepare($sql);

                    // Exécution de la requête
                    $stmt->execute();

                    // Récupération des résultats
                    $interventions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Affichage des résultats dans le tableau
                    foreach ($interventions as $intervention) {
                        echo "<tr>";
                        echo "<td>" . (isset($intervention['interv_num']) ? $intervention['interv_num'] : '') . "</td>";
                        echo "<td>" . (isset($intervention['interv_duree']) ? $intervention['interv_duree'] : '') . "</td>";
                        echo "<td>" . (isset($intervention['interv_dateDerniereModif']) ? $intervention['interv_dateDerniereModif'] : '') . "</td>";
                        echo "<td>" . (isset($intervention['inter_Date']) ? $intervention['inter_Date'] : '') . "</td>";
                        echo "<td>" . (isset($intervention['interv_Etat']) ? $intervention['interv_Etat'] : '') . "</td>";
                        echo "<td>" . (isset($intervention['interv_type']) ? $intervention['interv_type'] : '') . "</td>";
                        echo "<td>" . (isset($intervention['interv_Compteur']) ? $intervention['interv_Compteur'] : '') . "</td>";
                        echo "<td>" . (isset($intervention['interv_dateDebut']) ? $intervention['interv_dateDebut'] : '') . "</td>";
                        echo "<td>" . (isset($intervention['interv_dateFin']) ? $intervention['interv_dateFin'] : '') . "</td>";
                        echo "<td>" . (isset($intervention['num_adresse']) ? $intervention['num_adresse'] : '') . "</td>";
                        echo "<td>" . (isset($intervention['code_accompagnateur']) ? $intervention['code_accompagnateur'] : '') . "</td>";
                        echo "<td>" . (isset($intervention['code_etablissement']) ? $intervention['code_etablissement'] : '') . "</td>";
                        echo "<td>" . (isset($intervention['code_auteur']) ? $intervention['code_auteur'] : '') . "</td>";
                        echo "<td>" . (isset($intervention['code_interprete']) ? $intervention['code_interprete'] : '') . "</td>";
                        echo "</tr>";
                    }
                } catch (PDOException $e) {
                    echo "Erreur lors de l'exécution de la requête : " . $e->getMessage();
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Écouter les événements de saisie dans la barre de recherche
            document.querySelector('.search-input').addEventListener('input', function(event) {
                // Récupérer le texte saisi dans la barre de recherche
                const searchText = event.target.value.trim().toLowerCase();
                // Récupérer toutes les lignes du tableau
                const rows = document.querySelectorAll('tbody tr');

                // Parcourir toutes les lignes du tableau
                rows.forEach(function(row) {
                    // Récupérer le contenu de chaque ligne
                    const rowData = row.textContent.toLowerCase();
                    // Vérifier si le contenu de la ligne contient le texte de recherche
                    if (rowData.includes(searchText)) {
                        // Afficher la ligne si elle correspond au critère de recherche
                        row.style.display = '';
                    } else {
                        // Masquer la ligne sinon
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>
