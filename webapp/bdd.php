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

// Traitement de la suppression de l'utilisateur
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    try {
        $bdd->beginTransaction();

        // Suppression des enregistrements de la table "auteur" faisant référence à cet utilisateur
        $query_delete_auteur = "DELETE FROM Auteur WHERE user_id = :user_id";
        $stmt_delete_auteur = $bdd->prepare($query_delete_auteur);
        $stmt_delete_auteur->bindParam(':user_id', $user_id);
        $stmt_delete_auteur->execute();

        // Suppression de l'interprète associé à l'utilisateur
        $query_delete_interprete = "DELETE FROM Interprete WHERE user_id = :user_id";
        $stmt_delete_interprete = $bdd->prepare($query_delete_interprete);
        $stmt_delete_interprete->bindParam(':user_id', $user_id); // Utilisation de user_id pour l'interprète
        $stmt_delete_interprete->execute();

        // Suppression de l'établissement associé à l'utilisateur
        $query_delete_etablissement = "DELETE FROM Etablissement WHERE user_id = :user_id";
        $stmt_delete_etablissement = $bdd->prepare($query_delete_etablissement);
        $stmt_delete_etablissement->bindParam(':user_id', $user_id);
        $stmt_delete_etablissement->execute();

        // Suppression de l'accompagnateur associé à l'utilisateur
        $query_delete_accompagnateur = "DELETE FROM Accompagnateur WHERE user_id = :user_id";
        $stmt_delete_accompagnateur = $bdd->prepare($query_delete_accompagnateur);
        $stmt_delete_accompagnateur->bindParam(':user_id', $user_id);
        $stmt_delete_accompagnateur->execute();

        // Suppression de l'adresse associée à l'utilisateur
        $query_delete_adresse = "DELETE FROM Adresse WHERE user_id = :user_id";
        $stmt_delete_adresse = $bdd->prepare($query_delete_adresse);
        $stmt_delete_adresse->bindParam(':user_id', $user_id);
        $stmt_delete_adresse->execute();

        // Récupération de l'identifiant du compte associé à l'utilisateur
        $query_get_compte_id = "SELECT compte_login FROM Utilisateur WHERE user_id = :user_id";
        $stmt_get_compte_id = $bdd->prepare($query_get_compte_id);
        $stmt_get_compte_id->bindParam(':user_id', $user_id);
        $stmt_get_compte_id->execute();
        $compte_id = $stmt_get_compte_id->fetchColumn();

        // Suppression de l'utilisateur dans la table Utilisateur
        $query_delete_user = "DELETE FROM Utilisateur WHERE user_id = :user_id";
        $stmt_delete_user = $bdd->prepare($query_delete_user);
        $stmt_delete_user->bindParam(':user_id', $user_id);
        $stmt_delete_user->execute();

        // Suppression du compte associé dans la table Compte
        $query_delete_compte = "DELETE FROM Compte WHERE compte_login = :compte_login";
        $stmt_delete_compte = $bdd->prepare($query_delete_compte);
        $stmt_delete_compte->bindParam(':compte_login', $compte_id);
        $stmt_delete_compte->execute();

        $bdd->commit();
        
        echo "L'utilisateur et son compte associé ont été supprimés avec succès.";
    } catch (PDOException $e) {
        $bdd->rollBack();
        echo "Erreur lors de la suppression de l'utilisateur : " . $e->getMessage();
    }
}



?>
<!DOCTYPE html>
<html>
<head>
    <title>Gestion des utilisateurs</title>
    <meta charset="utf-8">
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

    <!-- Tableau pour afficher la liste des utilisateurs -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Liste des utilisateurs</h2>
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Nom</th>
                    <th scope="col">Prénom</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Récupération de la liste des utilisateurs depuis la base de données
                $query_users = "SELECT * FROM Utilisateur";
                $stmt_users = $bdd->query($query_users);
                // Affichage des utilisateurs dans le tableau
                while ($row = $stmt_users->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . $row['nom'] . "</td>";
                    echo "<td>" . $row['prenom'] . "</td>";
                    echo "<td>";
                    // Formulaire pour supprimer un utilisateur
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='user_id' value='" . $row['user_id'] . "'>";
                    echo "<button type='submit' name='delete_user' class='btn btn-danger'>Supprimer</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>
