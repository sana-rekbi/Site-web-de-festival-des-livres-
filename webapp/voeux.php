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

// Vérification de la réussite de la connexion à la base de données
if (!$bdd) {
    echo "Erreur de connexion à la base de données.";
    exit; // Arrêt de l'exécution du script en cas d'échec de connexion
}

// Initialisation de la variable de session si elle n'existe pas encore ou si elle n'est pas un tableau
if (!isset($_SESSION['selected_books']) || !is_array($_SESSION['selected_books'])) {
    $_SESSION['selected_books'] = [];
}

// Initialisation de la variable $ouvrages
$ouvrages = [];

// Requête SQL pour récupérer tous les ouvrages
$sql = "SELECT * FROM Ouvrage";
$stmt = $bdd->query($sql);

// Vérifier si la requête a réussi
if ($stmt) {
    // Récupération de tous les ouvrages sous forme de tableau associatif
    $ouvrages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Erreur lors de la récupération des ouvrages.";
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Si des ouvrages sont sélectionnés à ajouter
    if (isset($_POST["selection"])) {
        // Récupérer les valeurs des cases à cocher envoyées via le formulaire
        $selection = $_POST["selection"];
        // Vérifier si le nombre total d'ouvrages sélectionnés ne dépasse pas 3
        if (count($_SESSION['selected_books']) + count($selection) <= 3) {
            // Ajouter les ouvrages sélectionnés à la session
            foreach ($selection as $index => $selected_book) {
                // Vérifier si l'ouvrage est déjà dans la liste des ouvrages sélectionnés
                if (!in_array($selected_book, $_SESSION['selected_books'])) {
                    // Ajouter l'ouvrage à la liste des ouvrages sélectionnés
                    // et l'associer à son ordre choisi par l'utilisateur
                    $_SESSION['selected_books'][$selected_book] = $_POST["order_$index"];
                }
            }
        } else {
            // Afficher un message d'erreur si le nombre maximum d'ouvrages est dépassé
            echo "Vous ne pouvez pas sélectionner plus de 3 ouvrages.";
        }
    }
    // Si des ouvrages sont désélectionnés
    if (isset($_POST["deselect"])) {
        $deselected_book = $_POST["deselect"];
        // Supprimer l'ouvrage de la liste des ouvrages sélectionnés
        unset($_SESSION['selected_books'][$deselected_book]);
    }
    // Si tous les ouvrages doivent être désélectionnés
    if (isset($_POST["deselect_all"])) {
        // Vider la liste des ouvrages sélectionnés
        $_SESSION['selected_books'] = [];
    }
    // Si le bouton "Valider la sélection" est cliqué
    if (isset($_POST["validate_selection"])) {
        // Insérer les ouvrages sélectionnés dans la table Voeu
        foreach ($_SESSION['selected_books'] as $selected_book => $order) {
            // Récupérer l'ordre de préférence pour cet ouvrage
            $selected_order = $order;
            // Préparer la requête d'insertion
            $insert_query = $bdd->prepare("INSERT INTO Voeu (description, ordre_preference, num_ouvrage) VALUES (:description, :ordre_preference, :num_ouvrage)");
            // Exécuter la requête en liant les valeurs
            $insert_query->execute(array(
                ":description" => "Description du voeu", // Remplacez par la description réelle
                ":ordre_preference" => $selected_order, // Utilisez l'ordre choisi par l'utilisateur
                ":num_ouvrage" => $selected_book // Numéro de l'ouvrage sélectionné
            ));
        }
        // Afficher un message de confirmation
        echo "Les ouvrages sélectionnés ont été ajoutés à la table Voeu avec succès.";
    }
}

// Si des ouvrages sont sélectionnés
if (!empty($_SESSION['selected_books'])) {
    // Récupérer les détails de tous les ouvrages sélectionnés à partir de la base de données
    $selected_books_details = [];
    foreach ($_SESSION['selected_books'] as $selected_book => $order) {
        $selected_book_details = $bdd->prepare('SELECT * FROM Ouvrage WHERE num_ouvrage = :num_ouvrage');
        $selected_book_details->bindParam(':num_ouvrage', $selected_book, PDO::PARAM_INT);
        $selected_book_details->execute();
        $book_detail = $selected_book_details->fetch(PDO::FETCH_ASSOC);
        // Ajouter l'ordre de l'ouvrage dans les détails
        $book_detail['order'] = $order;
        $selected_books_details[] = $book_detail;
    }
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulation des Vœux</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center w-100">
                <a class="navbar-brand" href="#">Festival littéraire international</a>
                <div class="search-bar">
                    <input type="text" name="menu" class="search-input" placeholder="Rechercher dans le menu..">
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
                    <a class="nav-link" href="index.php">Accueil<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-door-fill" viewBox="0 0 16 16">
                        <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5"/>
                    </svg></a>   
                </li>
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
                    <a class="nav-link" href="voeux.php">Campagne de vœux <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-layout-text-window-reverse" viewBox="0 0 16 16">
                        <path d="M13 6.5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 .5-.5m0 3a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 .5-.5m-.5 2.5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1 0-1z"/>
                        <path d="M14 0a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2zM2 1a1 1 0 0 0-1 1v1h14V2a1 1 0 0 0-1-1zM1 4v10a1 1 0 0 0 1 1h2V4zm4 0v11h9a1 1 0 0 0 1-1V4z"/>
                    </svg></a>
                </li>
            </ul>
        </div>
    </nav>
<!-- Zone pour afficher les ouvrages sélectionnés -->
<h2>Ouvrages sélectionnés</h2>
<div id="selected-books-container">
    <?php if (!empty($selected_books_details)): ?>
        <div class="row">
            <?php foreach ($selected_books_details as $index => $selected_book): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $selected_book['nom_ouvrage']; ?></h5>
                            <p class="card-text">Code Auteur: <?php echo $selected_book['code_auteur']; ?></p>
                            <p class="card-text">Numéro Campagne: <?php echo $selected_book['num_campagne_de_voeux']; ?></p>
                            <p class="card-text">Numéro Ouvrage: <?php echo $selected_book['num_ouvrage']; ?></p>
                            <p class="card-text">Public Ciblé: <?php echo $selected_book['public_cible']; ?></p>
                            <!-- Utilisation de cases à cocher pour choisir l'ordre -->
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                <input type="hidden" name="num_ouvrage" value="<?php echo $selected_book['num_ouvrage']; ?>">
                                <label>Choisir l'ordre :</label><br>
                                <input type="checkbox" name="order_<?php echo $selected_book['num_ouvrage']; ?>[]" value="1er">
                                <label>1er</label><br>
                                <input type="checkbox" name="order_<?php echo $selected_book['num_ouvrage']; ?>[]" value="2eme">
                                <label>2eme</label><br>
                                <input type="checkbox" name="order_<?php echo $selected_book['num_ouvrage']; ?>[]" value="3eme">
                                <label>3eme</label><br>
                                <label for="description_<?php echo $selected_book['num_ouvrage']; ?>">Description:</label>
                                <textarea name="description[<?php echo $selected_book['num_ouvrage']; ?>]" id="description_<?php echo $selected_book['num_ouvrage']; ?>" rows="3" class="form-control"></textarea>
                                <!-- Bouton pour désélectionner l'ouvrage -->
                                <button type="submit" class="btn btn-danger mt-2" name="deselect" value="<?php echo $selected_book['num_ouvrage']; ?>">Désélectionner</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Bouton pour valider la sélection -->
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <button type="submit" class="btn btn-primary mt-4" name="validate_selection">Valider la sélection</button>
        </form>
    <?php else: ?>
        <p id="no-selected-books">Aucun ouvrage sélectionné.</p>
    <?php endif; ?>
</div>

        <!-- Liste des Ouvrages -->
        <h2>Liste des Ouvrages</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="row">
                <?php foreach ($ouvrages as $ouvrage): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $ouvrage['nom_ouvrage']; ?></h5>
                                <p class="card-text">Code Auteur: <?php echo $ouvrage['code_auteur']; ?></p>
                                <p class="card-text">Numéro Campagne: <?php echo $ouvrage['num_campagne_de_voeux']; ?></p>
                                <p class="card-text">Numéro Ouvrage: <?php echo $ouvrage['num_ouvrage']; ?></p>
                                <p class="card-text">Public Ciblé: <?php echo $ouvrage['public_cible']; ?></p>
                                <button type="submit" class="btn btn-primary" name="selection[]" value="<?php echo $ouvrage['num_ouvrage']; ?>">Sélectionner</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </form>
    </div>
    <script>
        // Fonction pour déplacer les ouvrages sélectionnés vers la section "Ouvrages sélectionnés"
        function moveSelectedBooks() {
            var selectedBooksContainer = document.getElementById('selected-books-container');
            <?php foreach ($_SESSION['selected_books'] as $selected_book): ?>
                // Récupérer la carte de l'ouvrage sélectionné
                var selectedBookCard = document.getElementById('selected-book-<?php echo $selected_book; ?>');
                // Vérifier si la carte existe
                if (selectedBookCard) {
                    // Déplacer la carte vers la section "Ouvrages sélectionnés"
                    selectedBooksContainer.appendChild(selectedBookCard);
                }
            <?php endforeach; ?>
        }

        // Appeler la fonction de déplacement au chargement de la page
        moveSelectedBooks();

// Fonction pour réactiver les boutons de l'ouvrage lorsqu'un bouton est décoché
function enableButtonsForBook(selectedBookId) {
    // Réactiver tous les boutons pour cet ouvrage
    document.querySelectorAll(`.card input[type="checkbox"][name="order_${selectedBookId}"]`).forEach(button => {
        button.disabled = false;
    });
}

// Fonction pour désactiver les boutons correspondants sur les autres ouvrages
function disableSelectedButtons(selectedBookId, selectedValue) {
    // Désactiver les boutons correspondants dans tous les ouvrages
    document.querySelectorAll('.card').forEach(card => {
        const ouvrageId = card.querySelector('input[type="checkbox"]').getAttribute('name').replace('order_', '');
        // Vérifier si l'ouvrage actuel correspond à celui sélectionné
        if (ouvrageId === selectedBookId) {
            // Désactiver les autres boutons de cet ouvrage
            card.querySelectorAll('input[type="checkbox"]').forEach(button => {
                if (button.value !== selectedValue) {
                    button.disabled = true;
                }
            });
        } else {
            // Désactiver les boutons correspondants dans les autres ouvrages
            card.querySelectorAll(`input[type="checkbox"][value="${selectedValue}"]`).forEach(button => {
                button.disabled = true;
            });
        }
    });
}

// Écouter les changements sur les boutons
document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', () => {
        const selectedBookId = checkbox.getAttribute('name').replace('order_', '');
        const selectedValue = checkbox.value;
        if (checkbox.checked) {
            disableSelectedButtons(selectedBookId, selectedValue);
        } else {
            // Réactiver les boutons de cet ouvrage lorsque le bouton est décoché
            enableButtonsForBook(selectedBookId);
        }
    });
});


    document.addEventListener("DOMContentLoaded", function() {
        // Écouter les événements de saisie dans la barre de recherche
        document.querySelector('.search-input').addEventListener('input', function(event) {
            // Récupérer le texte saisi dans la barre de recherche
            const searchText = event.target.value.trim().toLowerCase();
            // Récupérer toutes les cartes d'ouvrage
            const bookCards = document.querySelectorAll('.card');
            // Parcourir toutes les cartes d'ouvrage
            bookCards.forEach(function(card) {
                // Récupérer le nom de l'ouvrage de la carte
                const bookName = card.querySelector('.card-title').textContent.trim().toLowerCase();
                // Vérifier si le nom de l'ouvrage correspond au texte saisi
                if (bookName.includes(searchText)) {
                    // Afficher la carte si le nom correspond
                    card.style.display = 'block';
                } else {
                    // Masquer la carte si le nom ne correspond pas
                    card.style.display = 'none';
                }
            });
        });
    });

    </script>
    
</body>

<style>
/* Style pour les cartes des ouvrages */
.card {
    border: 1px solid #ccc; /* Bordure grise */
    border-radius: 10px; /* Coins arrondis */
    transition: box-shadow 0.3s ease; /* Animation de l'ombre lors du survol */
    background-color: #fff; /* Couleur de fond */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Ombre légère par défaut */
}

.card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Ombre plus prononcée au survol */
}


/* Style pour les titres des cartes */
.card-title {
    font-size: 1.2rem; /* Taille de la police du titre */
    color: #333; /* Couleur du texte du titre (noir ou autre couleur foncée) */
    margin-bottom: 0.5rem; /* Marge inférieure */
}
.card-text {
    color: #666; /* Couleur du texte */
    font-size: 0.9rem; /* Taille de la police du texte */
    line-height: 1.4; /* Hauteur de ligne */
}

.btn-primary {
    background-color: #ffa500; /* Couleur de fond du bouton Sélectionner */
    border: none; /* Suppression de la bordure */
    transition: background-color 0.3s ease; /* Animation de transition */
}

.btn-primary:hover {
    background-color: #ff8500; /* Changement de couleur au survol */
}

.btn-danger {
    background-color: #dc3545; /* Couleur de fond du bouton Désélectionner */
    border: none; /* Suppression de la bordure */
    transition: background-color 0.3s ease; /* Animation de transition */
}

.btn-danger:hover {
    background-color: #c82333; /* Changement de couleur au survol */
}


/* Style pour le fond avec une couleur gris clair */
body {
    background-color: #f2f2f2; /* Couleur de fond gris clair */
    color: #333333; /* Couleur du texte */
}

</style>
</html>


