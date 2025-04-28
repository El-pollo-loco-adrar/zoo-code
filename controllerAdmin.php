<?php
session_start();


include "./utils/function.php";
include "./model/model_admin.php";

$bdd = DBconnect();
$bdd->exec("SET NAMES 'utf8mb4'");
$message ="";
$messageSupp ="";
$messageListe = "";

//!Affiche la liste des utilisateurs
if(isset($_GET['action']) && $_GET['action'] === 'utilisateurs'){
    $message = readUsers($bdd);
}

//!Supprimer un utilisateur
if(isset($_POST['deleteUser'])){
    if(isset($_POST['id_user']) && !empty($_POST['id_user'])){
        
        $user_id = $_POST['id_user'];
        $bdd = DBconnect();
        $messageSupp = deleteUser($bdd, $user_id);
        header("Location: ./controllerAdmin.php?action=utilisateurs");
    }else{
        $messageSupp = "Erreur: l'ID de l'utilisateur est manquant";
    }
}

//!Affiche la liste des animaux
if(isset($_GET['action']) && $_GET['action'] === 'animal'){
    $messageListe = readAnimal($bdd);
}





//!Afficher menu quizz      OK
//? (renderChoixQuizz)             
if(isset($_GET['action']) && $_GET['action'] === 'quizz'){
    $message = renderChoixQuizz();
}

//!Choix de l'action dans le menu quizz     OK
//? (renderFormulaireCreationQuizz)  (renderListeQuizz)          
if(isset($_GET['action'])){
    switch ($_GET['action']){
        case'creer_quizz':
            $message = renderFormulaireCreationQuizz();
            break;
        case'liste_des_quizz':
            $message = renderListeQuizz($bdd);
            break;
    }
}

//!Ajoute nouveau quizz à la BDD        OK
//? (addQuizz)  (checkNameQuizz)        
if(isset($_POST['valider_creation_quizz'])){
    //Vérifier les champs vides
    if(isset($_POST['nom_quizz']) && !empty($_POST['nom_quizz']) &&
    isset($_POST['description_quizz']) && !empty($_POST['description_quizz']) &&
    isset($_POST['date']) && !empty($_POST['date'])){

        //Nettoyer les données
        $nomQuizz = sanitize($_POST['nom_quizz']);
        $descriptionQuizz = sanitize($_POST['description_quizz']);
        $date = sanitize($_POST['date']);

        //Verifier que le nom n'existe pas déjà
        if(checkNameQuizz($bdd, $nomQuizz)){
            $message = "<p style='color:red;background-color: white; text-align: center; margin: 10px;'>Le nom du quizz existe déjà</p>";

        }else{
            //Ajouter le quizz à la BDD
            $result = addQuizz($bdd, $nomQuizz, $descriptionQuizz, $date);

            $message = $result;
            // if(is_numeric($result)){
            //     $idQuizz = $result;
            //     $message = renderFormulaireCreationQuestion($bdd, $idQuizz);
            // }else{
                
            // }
        }

    }else{
        $message = "Veuillez remplir tous les champs pour créer un quizz";
    }
}

//!Action de supprimer un quizz     OK
//? (deleteQuizzByID)        
if(isset($_POST['supprimer_ce_quizz']) && isset($_POST['id_jeu_de_piste'])){
    if(!empty($_POST['id_jeu_de_piste'])){
        
        $idQuizz = (int)$_POST['id_jeu_de_piste'];
        $message = deleteQuizzByID($bdd, $idQuizz);
        
        header("Location: ./controllerAdmin.php?action=liste_des_quizz");
            exit;
    }
}

//!Action de sélectionner un quizz
if(isset($_POST['set_quizz_actif']) && !empty($_POST['quizz_actif'])){
    $idActif = intval($_POST['quizz_actif']);

    //Je désactive les quizz
    $bdd->exec("UPDATE jeu_de_piste SET actif_jeu_de_piste = 0");

    //J'active le quizz sélectionné
    $req = $bdd->prepare("UPDATE jeu_de_piste SET actif_jeu_de_piste = 1 WHERE id_jeu_de_piste = ?");
    $req->execute([$idActif]);
    $message = "<p style='color:green;background-color: white; text-align: center; margin: 10px;'>le quizz n°$idActif est maintenant actif.";
}

//!Modifier un quizz existant       OK 
//? (renderEditionQuizz)             
if(isset($_GET['action']) && $_GET['action'] ==='modifier_quizz'){     
    if(isset($_POST['modifier_ce_quizz']) && isset($_POST['id_jeu_de_piste'])){
        $idQuizz = $_POST['id_jeu_de_piste'];
        $message = renderEditionQuizz($bdd, $idQuizz);
    }
}

//!Accès au formulaire pour ajouter une nouvelle question au quizz sélectionné      OK
//? (renderFormulaireCreationQuestion)
if(isset($_POST['ajouter_question']) && isset($_POST['id_jeu_de_piste'])){
    $idQuizz = $_POST['id_jeu_de_piste'];
    $message = renderFormulaireCreationQuestion($bdd, $idQuizz);
}

//!Ajoute une nouvelle question + reponses + QR code généré automatiquement à la BDD
//? (addQuestion) (addReponses) (addQRCode) (genererQRCode) (associerQRCodeQuestion) (associerQRCodeJeuDePiste)
if(isset($_POST['submitQuestion'])){
    //Vérifier les champs vides
    if(isset($_POST['question']) && !empty($_POST['question']) &&
    isset($_POST['reponses']) && !empty($_POST['reponses']) &&
    isset($_POST['bonne_reponse']) && !empty($_POST['bonne_reponse']) &&
    // isset($_POST['qr_code']) && !empty($_POST['qr_code']) &&
    isset($_POST['id_quizz']) && !empty($_POST['id_quizz'])&&
    isset($_POST['id_animal']) && !empty($_POST['id_animal'])){

        //Nettoyer les données
        $question = sanitize($_POST['question']);
        $reponses = array_map('sanitize', $_POST['reponses']);
        $bonneReponse = sanitize($_POST['bonne_reponse']);
        $qrContent = uniqid('qr_',true);
        $idJeuDePiste = sanitize($_POST['id_quizz']);
        $idAnimal = (int)$_POST['id_animal'];
        
        //Ajouter la question à la BDD
        $idQuestion = addQuestion($bdd, $question);
        

        if($idQuestion){
            //ajouter les réponses
            foreach($reponses as $index => $reponse){
                $isBonneReponse = ($index +1) == $bonneReponse ? 1 : 0;
                addReponses($bdd, $idQuestion, $reponse, $isBonneReponse);
            }

            //Générer le QR code
            $nomFichier = $qrContent;
            $cheminQRCode = genererQRCode($qrContent, $nomFichier);
            //Ajouter le QR code à la BDD
            $idQRCode = addQRCode($bdd, $qrContent,0);

            if(is_numeric($idQRCode)){
                //Associer le QR code à la question
                associerQRCodeQuestion($bdd, $idQRCode, $idQuestion);
                //Associer le QR code au quizz
                associerQRCodeJeuDePiste($bdd, $idQRCode, $idJeuDePiste);
                //Associer le QR code à l'animal
                rattacherQRCodeAnimal($bdd, $idQRCode, $idAnimal);

            $message = "<p style = 'text-align: center; color: green;'>Question et réponses ajoutées avec succès</p>";
            }else{
                $message = "<p style = 'text-align: center; color: red;'>Erreur lors de l'ajout du QR code</p>";
            }
        }else{
            $message = "<p style = 'text-align: center; color: red;'>Erreur lors de l'ajout de la question</p>";
        }
    }else{
        $message = "<p style = 'text-align: center; color: red;'>Veuillez remplir tous les champs pour créer une question</p>";
    }
}


//!Mise à jour d'une question
//? (updateQuestion)
if(isset($_POST['update_question']) && isset($_POST['id_question']) && isset($_POST['titre_question']) && isset($_POST['texte_question'])) {
    $idQuestion = intval($_POST['id_question']);
    $titreQuestion = sanitize($_POST['titre_question']);
    $texteQuestion = sanitize($_POST['texte_question']);

    $message = updateQuestion($bdd, $idQuestion, $titreQuestion, $texteQuestion);
}

//!Mise à jour des réponses d'une question
if(isset($_POST['update_reponses_question']) && isset($_POST['id_reponse']) && isset($_POST['id_question']) && isset($_POST['texte_reponse']) && isset($_POST['valid_reponse_radio']) ){

    $idQuestion = intval($_POST['id_question']);
    $idReponses = $_POST['id_reponse'];
    $texteReponse = sanitizeArray($_POST['texte_reponse']);
    $idBonneReponse = $_POST['valid_reponse_radio'][$idQuestion];

    foreach($idReponses as $index => $idReponse){
        $idReponse = intval($idReponse);
        $texte = sanitize($texteReponse[$index]);
        $isValid = ($idReponse == $idBonneReponse) ? 1 : 0;

        updateReponse($bdd, $idReponse, $texte, $isValid);
    }
    $message = alert("Réponses mises à jour avec succès");
    
    header("Location: ./controllerAdmin.php?action=liste_des_quizz&modifier_quizz=");
    exit;
    
}

//!Mise à jour de l'animal dans la question
if(isset($_POST['update_animal'])){
    $idQRCode = sanitize($_POST['id_qr_code']);
    $idAnimal = sanitize($_POST['id_animal']);

    //supprimme l'association déjà existante
    deleteQRCodeAnimal($bdd, $idQRCode, $idAnimal);

    //ajoute la nouvelle association
    $result = rattacherQRCodeAnimal($bdd, $idQRCode, $idAnimal);
    if($result){
        $message = "<p style = 'text-align: center; color: green;'>L'animal a été mis à jour avec succès</p>";
}else{
        $message = "<p style = 'text-align: center; color: red;'>Erreur lors de la mise à jour de l'animal</p>";
    }
}


include "./view/admin/headerAdmin.php";
include "./view/admin/view_admin.php";
include "./view/footer.php";