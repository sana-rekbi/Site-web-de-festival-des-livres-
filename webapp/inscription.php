<?php
session_start();

// Connexion à la base de données
$dsn = "pgsql:host=localhost;port=5432;dbname=CSI;user=postgres;password=Minouche57";
try {
    $bdd = new PDO($dsn);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Votre code de requête SQL ici...
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}

// Vérification si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['form_type'] == "register") {
        // Récupération du type de compte
        $compte_type = $_POST['compte_type'];

        // Vérification de tous les champs du formulaire
        $champs_formulaire = ['nom', 'prenom', 'adresse_mail', 'mot_de_passe', 'confirm_password', 'num_tel', 'num_departement', 'compte_type', 'nom_ville'];
        $champs_vide = array_filter($champs_formulaire, function($champ) {
            return empty($_POST[$champ]);
        });

        if (!empty($champs_vide)) {
            $erreur_inscription = "Veuillez remplir tous les champs du formulaire.";
        } else {
            // Vérification si les mots de passe correspondent
            if ($_POST['mot_de_passe'] !== $_POST['confirm_password']) {
                $erreur_inscription = "Les mots de passe ne correspondent pas.";
            } else {
                // Récupération des données du formulaire
                $nom = $_POST['nom'];
                $prenom = $_POST['prenom'];
                $adresse_mail = $_POST['adresse_mail'];
                $mot_de_passe = $_POST['mot_de_passe']; // Mot de passe non hashé

                // Récupération de tous les mots de passe hachés de la base de données
                $query_all_passwords = "SELECT compte_mdp FROM Compte";
                $stmt_all_passwords = $bdd->query($query_all_passwords);
                $hashed_passwords_db = $stmt_all_passwords->fetchAll(PDO::FETCH_COLUMN);

                // Vérification si le mot de passe fourni existe déjà dans la base de données
                foreach ($hashed_passwords_db as $hashed_password_db) {
                    if (password_verify($mot_de_passe, $hashed_password_db)) {
                        // Mot de passe trouvé dans la base de données, affichez un message d'erreur
                        $erreur_inscription = "Le mot de passe est déjà utilisé par un autre utilisateur. Veuillez choisir un autre mot de passe.";
                        break; // Sortir de la boucle dès qu'une correspondance est trouvée
                    }
                }

                // Si aucun mot de passe correspondant n'est trouvé dans la base de données, continuez le processus d'inscription
                if (!isset($erreur_inscription)) {
                    // Hashage du mot de passe
                    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

                    // Autres données du formulaire
                    $num_tel = $_POST['num_tel'];
                    $num_departement = $_POST['num_departement'];
                    $compte_type = $_POST['compte_type'];
                    $nom_ville = $_POST['nom_ville'];
                    $nom_rue = $_POST['nom_rue'];
                    $X_latitude = $_POST['X_latitude'];
                    $Y_longitude = $_POST['Y_longitude'];

                    // Récupération des données spécifiques à l'établissement à partir du formulaire
                    $type_etablissement = $_POST['type_etablissement']; // Utilisez un autre nom de variable pour éviter les conflits

                    // Vérification du numéro de téléphone
                    if (!preg_match('/^\d{10}$/', $num_tel)) {
                        $erreur_inscription = "Le numéro de téléphone doit contenir exactement 10 chiffres.";
                    } else {
                        $dsn = "pgsql:host=localhost;port=5432;dbname=CSI;user=postgres;password=Minouche57";
                        try {
                            $bdd = new PDO($dsn);
                            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                            // Début de la transaction
                            $bdd->beginTransaction();

                            // Insertion des données du compte dans la table Compte
                            $query_compte = "INSERT INTO Compte (compte_mdp, compte_dateInscription, compte_Type, compte_nombre_intervention, compte_last_login)
                            VALUES (:compte_mdp, CURRENT_DATE, :compte_Type, 0, NULL) RETURNING compte_login";
                            $stmt_compte = $bdd->prepare($query_compte);
                            $stmt_compte->bindParam(':compte_mdp', $mot_de_passe_hash); // Mot de passe hashé
                            $stmt_compte->bindParam(':compte_Type', $compte_type);
                            $stmt_compte->execute();
                            $compte_login = $stmt_compte->fetchColumn(); // Récupération du compte_login généré

                            // Insertion des données de l'utilisateur dans la table Utilisateur
                            $query_utilisateur = "INSERT INTO Utilisateur (nom, prenom, adresse_mail, num_tel, num_departement, compte_login)
                                     VALUES (:nom, :prenom, :adresse_mail, :num_tel, :num_departement, :compte_login)";
                            $stmt_utilisateur = $bdd->prepare($query_utilisateur);
                            $stmt_utilisateur->bindParam(':nom', $nom);
                            $stmt_utilisateur->bindParam(':prenom', $prenom);
                            $stmt_utilisateur->bindParam(':adresse_mail', $adresse_mail);
                            $stmt_utilisateur->bindParam(':num_tel', $num_tel);
                            $stmt_utilisateur->bindParam(':num_departement', $num_departement);
                            $stmt_utilisateur->bindParam(':compte_login', $compte_login); // Utilisez le compte_login généré
                            $stmt_utilisateur->execute();

                            // Récupérer l'ID de l'utilisateur inséré
                            $user_id = $bdd->lastInsertId();

                            // Insertion des données d'adresse dans la table Adresse
                            $query_adresse = "INSERT INTO Adresse (nom_ville, num_departement, nom_rue, X_latitude, Y_longitude, user_id)
                            VALUES (:nom_ville, :num_departement, :nom_rue, :X_latitude, :Y_longitude, :user_id)";
                            $stmt_adresse = $bdd->prepare($query_adresse);
                            $stmt_adresse->bindParam(':nom_ville', $nom_ville);
                            $stmt_adresse->bindParam(':num_departement', $num_departement);
                            $stmt_adresse->bindParam(':nom_rue', $nom_rue);
                            $stmt_adresse->bindParam(':X_latitude', $X_latitude); // Ajoutez cette ligne pour lier X_latitude
                            $stmt_adresse->bindParam(':Y_longitude', $Y_longitude); // Ajoutez cette ligne pour lier Y_longitude
                            $stmt_adresse->bindParam(':user_id', $user_id);
                            $stmt_adresse->execute();

                            // Après l'insertion des données de l'utilisateur dans la table Utilisateur
                            // Récupérer num_adresse à partir de la table Adresse
                            $query_num_adresse = "SELECT num_adresse FROM Adresse WHERE user_id = :user_id";
                            $stmt_num_adresse = $bdd->prepare($query_num_adresse);
                            $stmt_num_adresse->bindParam(':user_id', $user_id);
                            $stmt_num_adresse->execute();
                            $num_adresse_row = $stmt_num_adresse->fetch(PDO::FETCH_ASSOC);
                            $num_adresse = $num_adresse_row['num_adresse'];

                            // Insérer les données spécifiques selon le type de compte
                            if ($compte_type === 'Auteur') {
                                // Vérifier si des langues ont été sélectionnées
                                if (isset($_POST['langues']) && !empty($_POST['langues'])) {
                                    // Récupérer les langues sélectionnées
                                    $langues = $_POST['langues'];

                                    // Convertir les langues en une chaîne JSON
                                    $langues_json = json_encode($langues);

                                    // Récupération des données spécifiques de l'auteur depuis le formulaire
                                    $nombre_ouvrage = $_POST['nombre_ouvrage'];

                                    // Requête d'insertion dans la table Auteur avec num_adresse récupéré
                                    $query_auteur = "INSERT INTO Auteur (nombre_ouvrage, num_adresse, langues, user_id)
                                                     VALUES (:nombre_ouvrage, :num_adresse, :langues, :user_id)";
                                    $stmt_auteur = $bdd->prepare($query_auteur);
                                    $stmt_auteur->bindParam(':nombre_ouvrage', $nombre_ouvrage);
                                    $stmt_auteur->bindParam(':num_adresse', $num_adresse);
                                    $stmt_auteur->bindParam(':langues', $langues_json, PDO::PARAM_STR); // Utiliser PDO::PARAM_STR pour une chaîne JSON
                                    $stmt_auteur->bindParam(':user_id', $user_id);
                                    $stmt_auteur->execute();
                                } else {
                                    // Aucune langue sélectionnée, afficher un message d'erreur ou prendre une autre action appropriée
                                }
                            } elseif ($compte_type === 'Etablissement') {
                                // Récupérer le type d'établissement depuis le formulaire
                                $type_etablissement = $_POST['type_etablissement'];

                                // Requête d'insertion dans la table Etablissement
                                $query_etablissement = "INSERT INTO Etablissement (type_etablissement, user_id, num_adresse)
                                                        VALUES (:type_etablissement, :user_id, :num_adresse)";
                                $stmt_etablissement = $bdd->prepare($query_etablissement);
                                $stmt_etablissement->bindParam(':type_etablissement', $type_etablissement);
                                $stmt_etablissement->bindParam(':user_id', $user_id);
                                $stmt_etablissement->bindParam(':num_adresse', $num_adresse); // Ajout de num_adresse récupéré
                                $stmt_etablissement->execute();
                            } elseif ($compte_type === 'Accompagnateur') {
                                // Insérer les données spécifiques dans la table Accompagnateur
                                $date_debut = $_POST['date_debut'];
                                $date_fin = $_POST['date_fin'];
                                $code_auteur = $_POST['code_auteur'];

                                $query_accompagnateur = "INSERT INTO Accompagnateur (Date_debut, Date_fin, user_id, code_auteur)
                                                         VALUES (:date_debut, :date_fin, :user_id, :code_auteur)";
                                $stmt_accompagnateur = $bdd->prepare($query_accompagnateur);
                                $stmt_accompagnateur->bindParam(':date_debut', $date_debut);
                                $stmt_accompagnateur->bindParam(':date_fin', $date_fin);
                                $stmt_accompagnateur->bindParam(':user_id', $user_id); // Utilisez le user_id récupéré
                                $stmt_accompagnateur->bindParam(':code_auteur', $code_auteur);
                                $stmt_accompagnateur->execute();
                            } elseif ($compte_type === 'Interprete') {
                                // Vérifier si des langues ont été sélectionnées
                                if (isset($_POST['langues']) && !empty($_POST['langues'])) {
                                    // Récupérer les langues sélectionnées
                                    $langues = $_POST['langues'];

                                    // Convertir les langues en une chaîne JSON
                                    $langues_json = json_encode($langues);

                                    // Insérer les données dans la table Interprete
                                    $query_interprete = "INSERT INTO Interprete (langues, user_id, interv_num)
                                                         VALUES (:langues, :user_id, :interv_num)";
                                    $stmt_interprete = $bdd->prepare($query_interprete);
                                    $stmt_interprete->bindValue(':langues', $langues_json, PDO::PARAM_STR); // Utiliser PDO::PARAM_STR pour une chaîne JSON
                                    $stmt_interprete->bindValue(':user_id', $user_id, PDO::PARAM_INT); // Utiliser la valeur de compte_login générée précédemment
                                    $stmt_interprete->bindValue(':interv_num', $interv_num, PDO::PARAM_INT); // Lien vers l'intervention
                                    $stmt_interprete->execute();
                                } else {
                                    // Aucune langue sélectionnée, afficher un message d'erreur ou prendre une autre action appropriée
                                }
                            }

                            // Validation de la transaction
                            $bdd->commit();

                            echo "Inscription réussie !";
                        } catch (PDOException $e) {
                            // En cas d'erreur, annuler la transaction
                            $bdd->rollBack();
                            echo "Erreur d'inscription : " . $e->getMessage();
                        }
                    }
                }
            }
        }
    } elseif ($_POST['form_type'] == "login") {
        // Récupération des identifiants de connexion saisis par l'utilisateur
        $prenom = $_POST["compte_login"]; // Modification ici pour utiliser le prénom comme nom d'utilisateur
        $password = $_POST["compte_mdp"];

        // Vérification spéciale pour l'administrateur
        if ($prenom === "admin" && $password === "admin") {
            // Redirection vers la page d'administration
            header("Location: administration.php");
            exit;
        }

        // Connexion à la base de données
        $dsn = "pgsql:host=localhost;port=5432;dbname=CSI;user=postgres;password=Minouche57";
        try {
            $bdd = new PDO($dsn);
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Requête pour vérifier les identifiants dans la base de données
            $query = "SELECT C.*, U.* FROM Compte C INNER JOIN Utilisateur U ON C.compte_login = U.compte_login WHERE U.prenom = :prenom";
            $stmt = $bdd->prepare($query);
            $stmt->bindParam(":prenom", $prenom, PDO::PARAM_STR);
            $stmt->execute();

            // Vérification si l'utilisateur existe
            if ($stmt->rowCount() === 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $stored_password = $row['compte_mdp']; // Récupérer le mot de passe hashé depuis la base de données
                $compte_type = $row['compte_type']; // Récupérer le type de compte de l'utilisateur

                // Vérification du mot de passe
                if (password_verify($password, $stored_password)) {
                    // Mot de passe correct, démarrage de la session
                    $_SESSION["username"] = $prenom; // Utilisation du prénom comme nom d'utilisateur
                    // Redirection en fonction du type de compte
                    if ($compte_type === 'Etablissement') {
                        header("Location: voeux.php");
                        exit;
                    } elseif ($compte_type === 'Auteur') {
                        header("Location: auteur.php");
                        exit;
                    } elseif ($compte_type === 'Interprete') {
                        header("Location: interprete.php");
                        exit;
                    } elseif ($compte_type === 'Accompagnateur') {
                        header("Location: accompagnateur.php");
                        exit;
                    } else {
                        // Redirection vers une autre page si nécessaire
                        header("Location: index.php");
                        exit;
                    }
                } else {
                    // Mot de passe incorrect, affiche un message d'erreur
                    $error_message = "Identifiants invalides. Veuillez réessayer.";
                }
            } else {
                // Utilisateur non trouvé, affiche un message d'erreur
                $error_message = "Identifiants invalides. Veuillez réessayer.";
            }
        } catch (PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
        }
    }
}
?>  <!-- Balise de fermeture PHP -->



<?php if (isset($erreur_connexion)) : ?>
    <div class="alert alert-danger" role="alert">
        <?php echo $erreur_connexion; ?>
    </div>
<?php endif; ?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
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

    <nav class="navbar navbar-expand-md navbar-light bg-light flex-column" >
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mx-auto flex-wrap">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Acceuil<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-door-fill" viewBox="0 0 16 16">
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

                
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="text-center mb-4">
            <button class="btn btn-primary" id="btnRegister">S'inscrire</button>
            <button class="btn btn-primary hidden" id="btnLogin">Se connecter</button>
        </div>
<!-- Formulaire de connexion -->
<form class="styled-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="formLogin">
<input type="hidden" name="form_type" value="login"> <!-- Champ pour le formulaire de connexion -->
    <!-- Votre code de formulaire de connexion ici -->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="compte_login" class="styled-label">Prenom :</label>
                <input type="text" class="form-control styled-input" name="compte_login" placeholder="Nom d'utilisateur">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="compte_mdp" class="styled-label">Mot de passe :</label>
                <input type="password" class="form-control styled-input" name="compte_mdp" placeholder="Mot de passe">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary btn-block styled-button">Se connecter</button>
        </div>
    </div>
</form>

   <!-- Formulaire d'inscription -->
<form class="styled-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="formRegister">
<input type="hidden" name="form_type" value="register"> <!-- Champ pour le formulaire d'inscription -->
    <!-- Votre code de formulaire d'inscription ici -->
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="compte_type" class="styled-label">Type de compte :</label>
                <select class="form-control styled-input" id="compte_type" name="compte_type" required>
                    <option value="Auteur">Auteur</option>
                    <option value="Etablissement">Etablissement</option>
                    <option value="Accompagnateur">Accompagnateur</option>
                    <option value="Interprete">Interprète</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nom" class="styled-label">Nom :</label>
                <input type="text" class="form-control styled-input" id="nomRegister" name="nom" placeholder="Entrez votre nom">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="prenom" class="styled-label">Prénom :</label>
                <input type="text" class="form-control styled-input" id="prenomRegister" name="prenom" placeholder="Entrez votre prénom">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="email" class="styled-label">Adresse email :</label>
                <input type="email" class="form-control styled-input" id="emailRegister" name="adresse_mail" placeholder="Entrez votre adresse email">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="password" class="styled-label">Mot de passe :</label>
                <input type="password" class="form-control styled-input" id="passwordRegister" name="mot_de_passe" placeholder="Entrez votre mot de passe">
            </div>
        </div>
    </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="confirm_password" class="styled-label">Confirmez le mot de passe :</label>
                        <input type="password" class="form-control styled-input" id="confirm_password" name="confirm_password" placeholder="Confirmez votre mot de passe">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="num_tel" class="styled-label">Numéro de téléphone :</label>
                        <input type="text" class="form-control styled-input" id="num_tel" name="num_tel" placeholder="Entrez votre numéro de téléphone">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="num_departement" class="styled-label">Numéro de département :</label>
                        <input type="text" class="form-control styled-input" id="num_departement" name="num_departement" placeholder="Entrez votre numéro de département" required>
                    </div>
                </div>
            </div>
            <div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="nom_ville" class="styled-label">Nom de la ville :</label>
            <input type="text" class="form-control styled-input" id="nom_ville" name="nom_ville" placeholder="Entrez le nom de la ville">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="nom_rue" class="styled-label">Nom de la rue :</label>
            <input type="text" class="form-control styled-input" id="nom_rue" name="nom_rue" placeholder="Entrez le nom de la rue">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="X_latitude" class="styled-label">Latitude :</label>
            <input type="number" class="form-control styled-input" id="X_latitude" name="X_latitude" placeholder="Entrez la latitude">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="Y_longitude" class="styled-label">Longitude :</label>
            <input type="number" class="form-control styled-input" id="Y_longitude" name="Y_longitude" placeholder="Entrez la longitude">
        </div>
    </div>
</div>

        

    <div class="row">
        <div class="col-md-12">
            <div class="form-group" id="additionalFields">
                <!-- Champs supplémentaires spécifiques à chaque type d'utilisateur seront ajoutés ici par JavaScript -->
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary btn-block styled-button">S'inscrire</button>
        </div>
    </div>
</form>
    </div>

    <!-- Votre contenu HTML ici -->

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function(){
            // Afficher le formulaire d'inscription lors du clic sur le bouton "S'inscrire"
            $("#btnRegister").click(function(){
                $("#formRegister").show();
                $("#formLogin").hide();
            });

            // Afficher le formulaire de connexion lors du clic sur le bouton "Se connecter"
            $("#btnLogin").click(function(){
                $("#formLogin").show();
                $("#formRegister").hide();
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("formRegister").style.display = "none";
    });

       // Afficher un message pop-up en JavaScript
       function afficherPopup(message) {
            // Créer un élément div pour la boîte de dialogue modale
            var popup = document.createElement('div');
            popup.className = 'modal';
            popup.innerHTML = `
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Message</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>${message}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            `;
            // Ajouter la boîte de dialogue modale à la page
            document.body.appendChild(popup);
            // Afficher la boîte de dialogue modale
            $(popup).modal('show');
        }

        // Afficher le message d'erreur approprié en cas d'échec de l'inscription
        <?php if (isset($erreur_inscription)) : ?>
            afficherPopup("<?php echo $erreur_inscription; ?>");
        <?php endif; ?>




            // Fonction pour afficher les champs supplémentaires en fonction du type d'utilisateur
    function afficherChampsSupplementaires(typeUtilisateur) {
        var champsSupplementaires = document.getElementById("additionalFields");
        champsSupplementaires.innerHTML = ""; // Effacer les champs précédemment affichés
        
        // Ajouter les champs supplémentaires en fonction du type d'utilisateur
        switch (typeUtilisateur) {
            case "Auteur":
                champsSupplementaires.innerHTML = `
                    <!-- Champs spécifiques à l'auteur -->
                    <div class="form-group">
                        <label for="nombre_ouvrage" class="styled-label">Nombre d'ouvrages :</label>
                        <input type="number" class="form-control styled-input" id="nombre_ouvrage" name="nombre_ouvrage" placeholder="Entrez le nombre d'ouvrages">
                    </div>

                    <div class="form-group">
            <label class="styled-label">Langues :</label><br>
            <input type="checkbox" id="francais" name="langues[]" value="Français">
            <label for="francais">Français</label><br>
            <input type="checkbox" id="anglais" name="langues[]" value="Anglais">
            <label for="anglais">Anglais</label><br>
            <input type="checkbox" id="italien" name="langues[]" value="Italien">
            <label for="italien">Italien</label><br>
            <input type="checkbox" id="espagnol" name="langues[]" value="Espagnol">
            <label for="espagnol">Espagnol</label><br>
            <input type="checkbox" id="arabe" name="langues[]" value="Arabe">
            <label for="arabe">Arabe</label><br>
            <!-- Ajoutez d'autres langues au besoin -->
        </div>
            
                    <!-- Ajoutez d'autres champs spécifiques à l'auteur ici si nécessaire -->
                `;
                break;
            case "Etablissement":
                champsSupplementaires.innerHTML = `
                    <!-- Champs spécifiques à l'établissement -->
                    <div class="form-group">
                        <label for="type_etablissement" class="styled-label">Type d'établissement :</label>
                        <select class="form-control styled-input" id="type_etablissement" name="type_etablissement">
                            <option value="université">Université</option>
                            <option value="lycée général">Lycée général</option>
                            <!-- Ajoutez d'autres options pour le type d'établissement ici si nécessaire -->
                        </select>
                    </div>
                    <!-- Ajoutez d'autres champs spécifiques à l'établissement ici si nécessaire -->
                `;
                break;
            case "Accompagnateur":
                champsSupplementaires.innerHTML = `
                    <!-- Champs spécifiques à l'accompagnateur -->
                    <div class="form-group">
                        <label for="disponibilites" class="styled-label">Date debut :</label>
                        <input type="date" class="form-control styled-input" id="disponibilites" name="date_debut">
                    </div>
                    <div class="form-group">
                        <label for="disponibilites" class="styled-label">Date fin :</label>
                        <input type="date" class="form-control styled-input" id="disponibilites" name="date_fin">
                    </div>
                    <!-- Ajoutez d'autres champs spécifiques à l'accompagnateur ici si nécessaire -->
                `;
                break;
                case "Interprete":
    champsSupplementaires.innerHTML = `
        <!-- Champs spécifiques à l'interprète -->
        <div class="form-group">
            <label class="styled-label">Langues :</label><br>
            <input type="checkbox" id="francais" name="langues[]" value="Français">
            <label for="francais">Français</label><br>
            <input type="checkbox" id="anglais" name="langues[]" value="Anglais">
            <label for="anglais">Anglais</label><br>
            <input type="checkbox" id="italien" name="langues[]" value="Italien">
            <label for="italien">Italien</label><br>
            <input type="checkbox" id="espagnol" name="langues[]" value="Espagnol">
            <label for="espagnol">Espagnol</label><br>
            <input type="checkbox" id="arabe" name="langues[]" value="Arabe">
            <label for="arabe">Arabe</label><br>
            <!-- Ajoutez d'autres langues au besoin -->
        </div>
        <!-- Ajoutez d'autres champs spécifiques à l'interprète ici si nécessaire -->
    `;
    break;
            default:
                // Aucun champ supplémentaire à afficher
                break;
        }
    }

    // Événement déclenché lors du changement de valeur dans le champ de sélection du type d'utilisateur
    document.getElementById("compte_type").addEventListener("change", function() {
        var typeUtilisateur = this.value; // Récupérer la valeur sélectionnée
        afficherChampsSupplementaires(typeUtilisateur); // Appeler la fonction pour afficher les champs supplémentaires
    });

    // Appel initial pour afficher les champs supplémentaires basé sur la valeur sélectionnée initialement
    afficherChampsSupplementaires(document.getElementById("compte_type").value);
    </script>

    <style>
/* Style pour le fond avec une couleur gris clair */
body {
    background-color: #f2f2f2; /* Couleur de fond gris clair */
    color: #333333; /* Couleur du texte */
}


    </style>
</body>
</html>
