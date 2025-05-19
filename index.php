<?php
include "./utils/function.php";
include "./model/model_user.php";
$message ="";

//Envoie de l'adresse mail pour la newsletter
if(isset($_POST['newsLetterSubmit'])){

    //! 1er étape: vérifier les champs vides
    if(isset($_POST['newsLetterInscriptionMail']) && !empty($_POST['newsLetterInscriptionMail'])){

        //! 2eme étape: vérifier format
        if(filter_var($_POST['newsLetterInscriptionMail'], FILTER_VALIDATE_EMAIL)){

            //! 3eme étape: nettoyer les données
            $email = sanitize($_POST['newsLetterInscriptionMail']);

            $bdd = DBconnect();

            $data = readNewsLetter($bdd, $email);

            if(!$data){
                $req = $bdd->prepare("INSERT INTO newsletter(email_newsletter) VALUES (?)");
                $req->bindParam(1, $email, PDO::PARAM_STR);
                $req->execute();
                $message='Enregistrment à la newsLetter réussi';
            }else{
                $message = "Le mail est déjà enregistré";
            }
        }else{
            $message = 'Email non valide';
        }
    }else{
        $message = 'Veuillez renseigner votre email';
    }
}

//Appel API
if (isset($_GET['action']) && $_GET['action'] ==='new_cat'){
    header('Content-Type: application/json');
    try{
        $reponse = file_get_contents("https://api.thecatapi.com/v1/images/search");
        if($reponse !== false){
            $animalData = json_decode($reponse, true);
            echo json_encode((['url' => $animalData[0]['url']]));
            exit;
        }
    }catch (Exception $e){
        $animalData = null;
    }
}

$animalData = null;
$image_url = null;
try{
    $reponse = file_get_contents("https://api.thecatapi.com/v1/images/search");
    if($reponse !== false){
        $animalData = json_decode($reponse, true);
        $image_url = $animalData[0]['url'];
    }
}catch (Exception $e){
    $animalData = null;
}

include "./view/header.php";
include "./view/view_index.php";
include "./view/footer.php";