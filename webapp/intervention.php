<?php
session_start();

// Connexion à la base de données PostgreSQL
$dsn = "pgsql:host=localhost;port=5432;dbname=CSI;user=postgres;password=Minouche57";
try {
    $bdd = new PDO($dsn);
    // Définir les options PDO ici si nécessaire
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Par exemple, pour activer le mode d'exception
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit; // Arrêt de l'exécution du script en cas d'échec de connexion
}

// Récupérer tous les voeux dans la base de données
$sql = "SELECT * FROM Voeu";
$stmt = $bdd->query($sql);

// Vérifier si la requête a réussi
if ($stmt) {
    // Récupération de tous les voeux sous forme de tableau associatif
    $voeux = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Erreur lors de la récupération des voeux.";
}

// Vérifier si la requête a été soumise
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si l'action est "accepter"
    if ($_POST["action"] == "accepter") {
        // Mettre à jour l'état du voeu dans la base de données avec "valide"
        $sql = "UPDATE Voeu SET etat_voeu = 'valide' WHERE num_voeu = :num_voeu";
    }
    // Vérifier si l'action est "refuser"
    elseif ($_POST["action"] == "refuser") {
        // Mettre à jour l'état du voeu dans la base de données avec "refuse"
        $sql = "UPDATE Voeu SET etat_voeu = 'refuse' WHERE num_voeu = :num_voeu";
    }

    // Exécuter la requête préparée
    $stmt = $bdd->prepare($sql);
    $stmt->bindParam(':num_voeu', $_POST['num_voeu']);
    $stmt->execute();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des interventions</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
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

    <nav class="navbar navbar-expand-md navbar-light bg-light flex-column">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mx-auto flex-wrap">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Accueil<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-door-fill" viewBox="0 0 16 16">
                        <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5"/>
                    </svg></a>   
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M12.354 7.354a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0 .5.5 0 0 1 0-.708L10.793 8H4.5a.5.5 0 0 1 0-1h6.293l-2.147-2.146a.5.5 0 0 1 .708-.708l3 3a.5.5 0 0 1 0 .708z"/>
                            <path fill-rule="evenodd" d="M3.5 2A1.5 1.5 0 0 1 5 0h6a1.5 1.5 0 0 1 1.5 1.5v4a.5.5 0 0 1-1 0V1.5A.5.5 0 0 0 11 1H5a.5.5 0 0 0-.5.5v11a.5.5 0 0 0 .5.5h6a.5.5 0 0 0 .5-.5V11a.5.5 0 0 1 1 0v2a1.5 1.5 0 0 1-1.5 1.5h-6A1.5 1.5 0 0 1 3 13V2z"/>
                        </svg>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <h1>Gestion des interventions</h1>
        <?php if (!empty($voeux)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Numéro de voeu</th>
                        <th>Description</th>
                        <th>Ordre de préférence</th>
                        <th>État du voeu</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($voeux as $voeu): ?>
                        <tr>
                            <td><?php echo $voeu['num_voeu']; ?></td>
                            <td><?php echo $voeu['description']; ?></td>
                            <td><?php echo $voeu['ordre_preference']; ?></td>
                            <td><?php echo $voeu['etat_voeu']; ?></td>
                            <td>
                                <form action="intervention.php" method="post">
                                    <input type="hidden" name="num_voeu" value="<?php echo $voeu['num_voeu']; ?>">
                                    <button type="submit" class="btn btn-success" name="action" value="accepter">Accepter</button>
                                    <button type="submit" class="btn btn-danger" name="action" value="refuser">Refuser</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun voeu trouvé.</p>
        <?php endif; ?>
    </div>
</body>
</html>
