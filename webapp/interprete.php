<?php
session_start();

// Connexion à la base de données PostgreSQL
$dsn = "pgsql:host=localhost;port=5432;dbname=CSI;user=postgres;password=Minouche57";
try {
    $bdd = new PDO($dsn);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
}

// Requête SQL pour récupérer les données des auteurs
$sql = "SELECT * FROM auteur";
$stmt = $bdd->query($sql);

// Vérifier si la requête a réussi
if ($stmt) {
    // Récupération de tous les auteurs sous forme de tableau associatif
    $auteurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Erreur lors de la récupération des auteurs.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Liste des Auteurs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        li:last-child {
            border-bottom: none;
        }
        .code {
            font-weight: bold;
            color: #007bff;
        }
        .dates {
            font-style: italic;
            color: #888;
        }
        .user {
            color: #555;
        }
        .code-auteur {
            color: #555;
        }
        .btn-disabled {
            pointer-events: none;
            opacity: 0.5;
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
    <nav class="navbar navbar-expand-md navbar-light bg-light flex-column" >
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mx-auto flex-wrap">
                
                <li class="nav-item">
    <?php if(isset($_SESSION['username'])) { ?>
        <a class="nav-link" href="logout.php">Déconnexion
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M12.354 7.354a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0 .5.5 0 0 1 0-.708L10.793 8H4.5a.5.5 0 0 1 0-1h6.293l-2.147-2.146a.5.5 0 0 1 .708-.708l3 3a.5.5 0 0 1 0 .708z"/>
                <path fill-rule="evenodd" d="M3.5 2A1.5 1.5 0 0 1 5 0h6a1.5 1.5 0 0 1 1.5 1.5v4a.5.5 0 0 1-1 0V1.5A.5.5 0 0 0 11 1H5a.5.5 0 0 0-.5.5v11a.5.5 0 0 0 .5.5h6a.5.5 0 0 0 .5-.5V11a.5.5 0 0 1 1 0v2a1.5 1.5 0 0 1-1.5 1.5h-6A1.5 1.5 0 0 1 3 13V2z"/>
            </svg>
        </a>
    <?php } else { ?>
        <a class="nav-link" href="inscription.php">Inscription/Connexion
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard2-fill" viewBox="0 0 16 16">
                <path d="M9.5 0a.5.5 0 0 1 .5.5.5.5 0 0 0 .5.5.5.5 0 0 1 .5.5V2a.5.5 0 0 1-.5.5h-5A.5.5 0 0 1 5 2v-.5a.5.5 0 0 1 .5-.5.5.5 0 0 0 .5-.5.5.5 0 0 1 .5-.5z"/>
                <path d="M3.5 1h.585A1.5 1.5 0 0 0 4 1.5V2a1.5 1.5 0 0 0 1.5 1.5h5A1.5 1.5 0 0 0 12 2v-.5q-.001-.264-.085-.5h.585A1.5 1.5 0 0 1 14 2.5v12a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-12A1.5 1.5 0 0 1 3.5 1"/>
            </svg>
        </a>
    <?php } ?>
</li>


                <li class="nav-item">
                    <a class="nav-link" href="accompagnateur.php">auteurs<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-layout-text-window-reverse" viewBox="0 0 16 16">
                        <path d="M13 6.5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 .5-.5m0 3a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 .5-.5m-.5 2.5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1 0-1z"/>
                        <path d="M14 0a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2zM2 1a1 1 0 0 0-1 1v1h14V2a1 1 0 0 0-1-1zM1 4v10a1 1 0 0 0 1 1h2V4zm4 0v11h9a1 1 0 0 0 1-1V4z"/>
                    </svg></a>
                </li>
            </ul>
        </div>
    </nav>
     <!-- Contenu principal -->
     <div class="container">
        <h1>Liste des Auteurs</h1>
        <form id="selectionForm" action="selectionner_utilisateurs.php" method="POST">
            <button type="submit" id="submitButton" class="btn-disabled">Valider</button>
            <ul>
            <?php foreach ($auteurs as $auteur): ?>
    <li>
        <input type="checkbox" name="selected_users[]" class="userCheckbox" value="<?php echo $auteur['code_auteur']; ?>">
        <span class="code">Code: <?php echo $auteur['code_auteur']; ?></span> | 
        <span class="dates">Nombre d'ouvrages: <?php echo isset($auteur['nombre_ouvrage']) ? $auteur['nombre_ouvrage'] : 'N/A'; ?></span> | 
        <span class="dates">Numéro d'adresse: <?php echo isset($auteur['num_adresse']) ? $auteur['num_adresse'] : 'N/A'; ?></span> | 
        <span class="user">User ID: <?php echo isset($auteur['user_id']) ? $auteur['user_id'] : 'N/A'; ?></span> | 
        <span class="langues">Langues: <?php echo isset($auteur['langues']) ? $auteur['langues'] : 'N/A'; ?></span>
    </li>
<?php endforeach; ?>

            </ul>
        </form>
    </div>


    <script>
      const checkboxes = document.querySelectorAll('.userCheckbox');
        const submitButton = document.getElementById('submitButton');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    if (cb !== checkbox) {
                        cb.disabled = checkbox.checked;
                    }
                });
                submitButton.disabled = false;
                if (checkbox.checked) {
                    submitButton.classList.remove('btn-disabled');
                } else {
                    const checked = document.querySelector('.userCheckbox:checked');
                    if (!checked) {
                        submitButton.disabled = true;
                        submitButton.classList.add('btn-disabled');
                    }
                }
            });
        });
    </script>
</body>
</html>
