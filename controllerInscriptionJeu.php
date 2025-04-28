<?php
session_start();

include "./utils/function.php";
include "./model/model_user.php";

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['inscriptionButton'])) {
    $message = "Erreur lors de l'envoie du formulaire d'inscription";
}

if(isset($_POST["inscriptionButton"])){

    //!1ère étape : Vérifier les champs vides
    if(isset($_POST["pseudo"]) && !empty($_POST["pseudo"]) &&
    isset($_POST["email"]) && !empty($_POST["email"]) &&
    isset($_POST["password"]) && !empty($_POST["password"]) &&
    isset($_POST["confirmPassword"]) && !empty($_POST["confirmPassword"])){

        //! 2eme étape: vérifier format
        if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){

            //!3eme étape : vérifier si les mdp correspondent
            if($_POST["password"] == $_POST["confirmPassword"]){

            //! 4eme étape: nettoyer les données
            $pseudo = sanitize($_POST["pseudo"]);
            $email = sanitize($_POST["email"]);
            $password = sanitize($_POST["password"]);

            //!Chiffrage MDP
            $password = password_hash($password, PASSWORD_BCRYPT);

            $bdd = DBconnect();
            $message = addUser($bdd, $pseudo, $email, $password);

            if (strpos($message, "Inscription") !== false) {  
                header('Location:controllerCompteUtilisateur.php');
                exit();
            }

            }else{
                $message = "<p style='color: red;'>Les mots de passe ne correspondent pas</p>";
            }
        }else{
            $message = "<p style='color: red;'>Email non valide</p>";
        }

    }else{
        $message = "<p style='color: red;'>Veuillez remplir tous les champs pour s'inscrire</p>";
    }

    }

include "./view/jeu/header_jeu.php";
include "./view/jeu/view_inscriptionJeu.php";
include "./view/footer.php";
?>
<script src="src\script\script.js"></script>
