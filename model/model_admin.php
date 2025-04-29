<?php
require_once __DIR__ . '/../libs/phpqrcode/qrlib.php';


//!Fonction qui affiche les utilisateurs
function readUsers($bdd){
    try{
        // $bdd = DBconnect();
        $req = $bdd->prepare("SELECT id_user, pseudo_user, mail_user, id_role FROM users");
        $req->execute();
        $data = $req->fetchAll(PDO::FETCH_ASSOC);

        $message= "
        <h1>Liste des utilisateurs</h1>";

        foreach($data as $utilisateurs){
            $message = $message. "
            <div style = 'border: solid black 2px; text-align: center; width: 60%; margin: 10px auto; padding: 1%; background-color : whitesmoke;'>
                <form method='POST' action='controllerAdmin.php' onsubmit='return confirm(\"Êtes-vous sûr de vouloir supprimer cet utilisateur ?\");'>                 
                    <p><strong>ID:</strong> {$utilisateurs['id_user']}</p>
                    <p><strong>Pseudo:</strong> {$utilisateurs['pseudo_user']}</p>
                    <p><strong>Email:</strong> {$utilisateurs['mail_user']}</p>
                    <p><strong>Rôle:</strong> {$utilisateurs['id_role']}</p>
                    <input type='hidden' name='id_user' value='{$utilisateurs['id_user']}'>
                    <button type='submit' name='deleteUser'>❌ Supprimer</button>
                </form>
            </div>
            <br>
            <br>";
        }
        return $message;
    }catch(Exception $e){
        return  $e->getMessage();
    }
}

//!Fonction qui supprime un utilisateur
function deleteUser($bdd, $id_user){
    try{
        $req = $bdd->prepare("DELETE FROM users WHERE id_user =?");
        $req->execute([$id_user]);
        return "L'utilisateur a été supprimé avec succès.";
    }catch(Exception $e){
        return $e->getMessage();
    }
}


//!Fonction qui ajoute un animal
function addAnimal($bdd, $nom, $espece, $description, $age, $image_url){
    try{
        
        $req = $bdd->prepare("INSERT INTO animal(nom_animal, espece_animal, description_animal, age_animal, image_url) VALUES (?,?,?,?,?)");

        $req->bindParam(1,$nom,PDO::PARAM_STR);
        $req->bindParam(2,$espece,PDO::PARAM_STR);
        $req->bindParam(3,$description,PDO::PARAM_STR);
        $req->bindParam(4,$age,PDO::PARAM_INT);
        $req->bindParam(5,$image_url,PDO::PARAM_STR);
        $req->execute();
        return "Ajout de $nom réussi.";

    }catch(Exception $e){
        return $e->getMessage();
    }
}

//!Fonction qui affiche les animaux pour l'admin
function readAnimal($bdd){
    try {
        $req = $bdd->prepare("SELECT id_animal, nom_animal, espece_animal,description_animal, age_animal, image_url FROM animal");
        $req->execute();
        $data = $req->fetchAll(PDO::FETCH_ASSOC);

        
        if(empty($data)){
            return "<p style='text-align: center; color:red;'>Aucun animal trouvé pour le moment.</p>";
        }
        $messageListe = "
        <h1>Liste des animaux</h1>";
        foreach ($data as $animal){
            $messageListe = $messageListe. "
            <div style = 'border: solid black 2px; text-align: center; width: 60%; margin: 10px auto; padding: 1%; background-color : whitesmoke;'>
                <form method='POST' action='controllerAjoutAnimauxAdmin.php' onsubmit='return confirm(\"Êtes-vous sûr de vouloir supprimer cet animal ?\");'> 
                    <p><strong>ID:</strong> {$animal['id_animal']}</p>
                    <p><strong>Nom:</strong> {$animal['nom_animal']}</p>
                    <p><strong>Espece:</strong> {$animal['espece_animal']}</p>
                    <p><strong>Description:</strong> {$animal['description_animal']}</p>
                    <p><strong>Age:</strong> {$animal['age_animal']}</p>
                    <img src ='{$animal['image_url']}' alt='Photo{$animal['nom_animal']}' style='max-width: 200px; max-height: 200px; object-fit: cover;'>
                    <br>
                    <input type='hidden' name='id_animal' value='{$animal['id_animal']}'>
                    <button type='submit' name='deleteAnimal'>❌ Supprimer l'animal de la base de données</button>
                </form>
            </div>
            <br>
            <br>";
        }

        return $messageListe;
    }catch(Exception $e){
        return $e->getMessage();
    }
}
//!Fonction qui supprime un animal
function deleteAnimal($bdd, $id_animal){
    try {
        $req = $bdd->prepare("SELECT image_url FROM animal WHERE id_animal = ?");
        $req->execute([$id_animal]);
        $animal = $req->fetch(PDO::FETCH_ASSOC);

        if($animal && !empty($animal['image_url']) && file_exists($animal['image_url'])){
            unlink($animal['image_url']);//Je supprime l'image du dossier
        }
        $req = $bdd->prepare('DELETE FROM animal WHERE id_animal =?');
        $req->execute([$id_animal]);

        return "Suppression de l'animal";

    }catch(Exception $e){
        return $e->getMessage();
    }
}




//!Fonction qui affiche le menu quizz               OK
function renderChoixQuizz(){
    return '
    <div style="text-align: center; padding: 2rem;">
        <h2>Gestion des Quizz</h2>

        <form method="POST" action="controllerAdmin.php?action=creer_quizz">
            <button type="submit" name="creer_nouveau_quizz">Créer un nouveau quizz</button>
        </form>
        <br>
        <form method="GET" action="controllerAdmin.php">
            <input type="hidden" name="action" value="liste_des_quizz">
            <button type="submit" name="modifier_quizz">Liste des quizz</button><br>
        </form>
        </div>
    ';
}
//!Fonction formulaire pour créer un nouveau quizz              OK
function renderFormulaireCreationQuizz(){
    return "
    <h1>Créer un nouveau quizz</h1>
    <div id='formulaireCreation'>
        <form method='POST' action='controllerAdmin.php'>
            <label for='nom_quizz'>Nom du quizz:</label><br>
            <input type='text' name='nom_quizz' id='nom_quizz' required><br>

            <label for='description_quizz'>Description du quizz:</label><br>
            <textarea name='description_quizz' id='description_quizz' required></textarea><br>

            <label for='date'>Date de création:</label><br>
            <input type='date' name='date' id='date' required><br>

            <input id='submitFormulaireCreation' type='submit' name='valider_creation_quizz' value='Créer le quizz'>
        </form>
    </div>
    ";
}
//!Fonction qui affiche la liste des quizz et qui permet de modifier, supprimer et choisir le quizz             OK
function renderListeQuizz($bdd){
    try{
        $req = $bdd->prepare("SELECT id_jeu_de_piste, name_jeu_de_piste, description_jeu_de_piste, date_jeu_de_piste, actif_jeu_de_piste FROM jeu_de_piste");
        $req->execute();
        $quizz = $req->fetchAll(PDO::FETCH_ASSOC);

        if(empty($quizz)){
            return "<p style='text-align: center; color:red;'>Aucun quizz trouvé pour le moment.</p>";
        }

        $messageListe = '
        <h2>Liste des quizz</h2>';


        $messageListe = $messageListe.'
        <table id="tableFormulaire">
            <thead>
                <tr style="border: solid black 1px;">
                    <th style="white-space: normal;background-color: #FFC0CB;">ID quizz</th>
                    <th style="border: solid black 1px; background-color: #ADD8E6;">Nom</th>
                    <th style="border: solid black 1px; background-color: #90EE90;">Déscription</th>
                    <th style="border: solid black 1px; background-color: #FFD700;">Date de création</th>
                    <th style="border: solid black 1px; background-color:hsl(351, 100.00%, 85.70%);">Supprimer</th>
                    <th style="border: solid black 1px; background-color:#c530ca;">Modifier</th>
                </tr>
            </thead>
            <tbody>
        ';

        $radioInputs = '';//boutons radio pour activer formulaire

        foreach($quizz as $qz){
            $id = (int)$qz['id_jeu_de_piste'];
            $nom = htmlspecialchars($qz['name_jeu_de_piste']);
            $description = htmlspecialchars($qz['description_jeu_de_piste']);
            $date = htmlspecialchars($qz['date_jeu_de_piste']);
            $actif = (int)$qz['actif_jeu_de_piste'];
            $checked = $actif ===1 ? 'checked' : '';

            $messageListe .= "
            <tr>
                <td style='border: 1px solid black;background-color: #FFC0CB;'>$id</td>
                <td style='border: 1px solid black;background-color: #ADD8E6;'>$nom</td>
                <td style='border: 1px solid black;background-color: #90ee90;'>$description</td>
                <td style='border: 1px solid black;background-color: #FFD700;'>$date</td>
                <td style='border: 1px solid black;background-color: #FFB6C1;'>
                                <!-- Formulaire de suppression -->
                    <form method='POST' action='controllerAdmin.php' id='form-delete-$id' onsubmit='return confirm(\"Êtes-vous sûr de vouloir supprimer ce quizz ?\");'>
                        <input type='hidden' name='id_jeu_de_piste' value='$id'>
                        <input type='hidden' name='supprimer_ce_quizz' value='1'>
                        <button type='button' onclick=\"confirmDeletion($id)\">❌</button>
                    </form>
                </td> 
                                    <!-- Formulaire de modification -->
                <td style='border: solid black 1px; background-color: #c530ca;'>                               
                    <form method='POST' action='controllerAdmin.php?action=modifier_quizz' id='form-edit-$id'>
                        <input type='hidden' name='id_jeu_de_piste' value='$id'>
                        <input type='hidden' name='modifier_ce_quizz' value='1'>
                        <button type='submit'>Modifier</button>
                    </form>
                </td>  
                
            </tr>
            <form id='form-$id' method='POST' action='controllerAdmin.php' style='display:none;'>
                <input type='hidden' name='id_jeu_de_piste' value='$id'>
                <input type='hidden' name='modifier_ce_quizz' value='1'>
            </form>

            <script>
                function confirmDeletion(id) {
                    if (confirm('Voulez-vous vraiment supprimer ce quizz ?')) {
                        document.getElementById('form-delete-' + id).submit();  // Soumettre le formulaire avec l'ID correspondant
                    }
                }
            </script>
        ";
        $radioInputs .="
            <label style ='margin: 0 10px;'>
                <input type='radio' name='quizz_actif' value = '$id' $checked>            
                Quizz n°$id :
                <br> $nom
            </label>
        ";
        }
        $messageListe .= '</tbody></table>';

        $messageListe .= '
        <form method="POST" action="controllerAdmin.php" style="text-align: center; margin-top: 20px;">
            <h3>Définir quizz actif :</h3>
            '.$radioInputs .'
            <br>
            <button type="submit" name="set_quizz_actif" style="margin-top: 10px;">Définir le quizz actif</button>
        </form>
        ';
        return $messageListe;
    }catch(Exception $e){
        return $e->getMessage();
    }
}
//!Fonction qui ajoute un quizz à la bdd                OK
function addQuizz ($bdd, $nomQuizz, $descriptionQuizz, $date){
    try{
        $req = $bdd->prepare("INSERT INTO jeu_de_piste (name_jeu_de_piste, description_jeu_de_piste, date_jeu_de_piste) VALUES (?, ?, ?)");

        $req->bindParam(1, $nomQuizz, PDO::PARAM_STR);
        $req->bindParam(2, $descriptionQuizz, PDO::PARAM_STR);
        $req->bindParam(3, $date, PDO::PARAM_STR);
        $req->execute();
        return "<p style = 'text-align:center; color: green; background-color: white;'>Création du quizz : $nomQuizz réussi.<br> Vous pouvez maintenant ajouter des questions à ce quizz.</p>";
    }catch(Exception $e){
        return $e->getMessage();
    }
}
//!Fonction qui vérifie si nom de quizz existe déjà en bdd          OK
function checkNameQuizz($bdd, $nomQuizz){
    try{
        $req = $bdd->prepare("SELECT name_jeu_de_piste from jeu_de_piste WHERE name_jeu_de_piste = ?");
        $req->bindParam(1, $nomQuizz, PDO::PARAM_STR);
        $req->execute();

        $result = $req->fetch(PDO::FETCH_ASSOC);
        if($result){
            return true;
        }else{
            return false;
        }

    }catch(Exception $e){
        return $e->getMessage();
    }
}
//!Fonction qui supprime un quizz
function deleteQuizzByID($bdd, $idQuizz){
    try{
        $req = $bdd->prepare("DELETE FROM jeu_de_piste WHERE id_jeu_de_piste = ?");
        $req->execute([$idQuizz]);
        return "<p style='color:green;'>Quizz supprimé avec succès !</p>";
    }catch(Exception $e){
        return $e->getMessage();
    }
}

//!Fonction pour afficher les questions, les réponses le qr code d'un quizz. Permet de modifier les questions, les réponses et changer l'animal associé. Permet de supprimer une question          OK
function renderEditionQuizz($bdd, $idQuizz){
    try{
        //recupération du nom d'un quizz
        $req = $bdd->prepare("SELECT id_jeu_de_piste, name_jeu_de_piste FROM jeu_de_piste WHERE id_jeu_de_piste =?");
        $req->execute([$idQuizz]);
        $quizz = $req->fetch(PDO::FETCH_ASSOC);

        if(!$quizz){
            return "<p>Quizz introuvable.</p>";
        }

        $output = "<h2>Modifier le quizz: {$quizz['name_jeu_de_piste']}</h2>";

        //recupération des questions d'un quizz
        $reqQuestions = $bdd->prepare("SELECT 
                q.id_question,
                q.titre_question,
                qr.id_qr_code,
                qr.code_qr_code,
                qr.position_qr_code
            FROM jeu_de_piste as jp
            JOIN contenir as c ON jp.id_jeu_de_piste = c.id_jeu_de_piste
            JOIN qr_code as qr ON c.id_qr_code = qr.id_qr_code
            JOIN associer as a ON qr.id_qr_code = a.id_qr_code
            JOIN question as q ON a.id_question = q.id_question
            WHERE jp.id_jeu_de_piste = :idQuizz
        ");
        $reqQuestions->execute([':idQuizz' => $idQuizz]);
        $questions = $reqQuestions->fetchAll(PDO::FETCH_ASSOC);

        

        if(empty($questions)){
            $output .= "<p style='text-align: center; color:red;'>Aucune question trouvée pour ce quizz.</p>";
        }else{
            foreach ($questions as $question) {

                    // Récupération des réponses associées à la question
            $reqReponses = $bdd->prepare("SELECT
                            id_reponse,
                            texte_reponse,
                            valid_reponse,
                            id_question
                        FROM reponse
                        WHERE id_question =?");
            $reqReponses->execute([$question["id_question"]]);
            $reponses = $reqReponses->fetchAll(PDO::FETCH_ASSOC);

                    //Récupération de l'animal lié au QR code
                    $reqAnimalQR = $bdd->prepare("SELECT a.id_animal FROM rattacher as r
                            JOIN animal as a ON r.id_animal = a.id_animal
                            WHERE r.id_qr_code = ?");
                    $reqAnimalQR->execute([$question["id_qr_code"]]);
                    $animalQR = $reqAnimalQR->fetch(PDO::FETCH_ASSOC);

                    //Récupération de tous les animaux
                    $reqAnimaux = $bdd->query("SELECT id_animal, nom_animal FROM animal ORDER BY nom_animal");
                    $animaux = $reqAnimaux->fetchAll(PDO::FETCH_ASSOC);

                    //Affichage de la question
                $output .= '
                <div id="modifierQuizz">

                    <form id="formQuestion" method="POST" action="controllerAdmin.php">
                        <input type="hidden" name="id_question" value="'.$question["id_question"].'">

                        <h3>Question:</h3>

                        <label for="titre_'.$question["id_question"].'">Titre de la question:</label><br>
                        <input type="text" id="titre_'.$question["id_question"].'" name="titre_question" value ="'.htmlspecialchars($question["titre_question"]).'" required><br>

                        <button type="submit" name="update_question">Modifier la question</button><br> 
                    </form>
                ';
                    $output .='    
                    <div class="separateModif"></div>
                    ';
                    // Affichage du Qr code
                    if (!empty($question['code_qr_code'])){
                        $qrImagePath = "public/qr_codes/" .$question["code_qr_code"] .".png";
                        $output .= "<div id='qrQuestion'>
                                        <h3>QR Code:</h3>
                                        <p><strong>Code :</strong> " .$question["code_qr_code"]. " Position :".$question["position_qr_code"]."</p>
                                        <strong>Qr code :</strong><br>
                                        <img src='$qrImagePath' alt='QR Code' style='max-width: 150px; height: auto; border: solid 1px black;'/>
                                        
                                    </div>";
                    }
                    $output .='    
                    <div class="separateModif"></div>
                    ';

                    // Affichage des réponses
                    $output .= "<h3>Réponses:</h3>
                                <form method='POST' action='controllerAdmin.php'>
                                    <input type='hidden' name='id_question' value='".$question['id_question']."'>
                                    <ul id='ulReponses'>
                                    ";
                                    foreach ($reponses as $reponse) {
                                        $valide = $reponse['valid_reponse'] ? "(✅ bonne réponse)" : "";
                                        $output .= "
                                            <div id='reponseModifierQuizz'>
                                                <li>
                                                    <input type='hidden' name='id_reponse[]' value='".$reponse['id_reponse']."'>
                                                    
                                                    <label for='reponse_".$reponse['id_reponse']."'>Réponse:</label><br>
                                                    <input type='text' id='reponse_".$reponse["id_reponse"]."' name='texte_reponse[]' value='".htmlspecialchars($reponse['texte_reponse'])."' required><br>

                                                    <label>Bonne réponse :</label><br>
                                                    <input type='radio' name='valid_reponse_radio[".$question['id_question']."]' value='".$reponse['id_reponse']."' ".($reponse['valid_reponse'] ? "checked" : "").">
                                                    <span>Cocher pour définir comme bonne réponse</span><br>
                                                    $valide
                                                </li>
                                            </div>";
                                    } 
                                    $output .= "</ul>
                                    <div id='submitReponse'>
                                        <button id='buttonReponse' type='submit' name='update_reponses_question'>Modifier toutes les réponses</button>
                                    </div>
                                </form>";
                    
                    $output .='    
                    <div class="separateModif"></div>
                    ';
                    //Modification de l'animal associé
                    $output .="
                    <form id='formAnimal' method='POST' action='controllerAdmin.php'>
                        <input type='hidden' name='id_qr_code' value='".$question['id_qr_code']."'>
                        <h3 for='animal_".$question["id_question"]."'>Animal associé: </h3><br>
                        <select name='id_animal' id='animal_".$question["id_question"]."'>";

                        foreach($animaux as $animal){
                            $selected = ($animalQR && $animalQR['id_animal'] == $animal['id_animal']) ? 'selected' : '';
                            $output .= "<option value='".$animal['id_animal']."' $selected>".$animal['nom_animal']."</option>";
                        }
                        $output .="</select>
                                <button type='submit' name='update_animal'>Modifier l'animal associé à la question</button>
                        </form>
                        
                </div>";
                                    //Suppression de la question
                $output .='
                <div id="formSuppressionQuestion">
                    <form method="POST" action="controllerAdmin.php" onsubmit="return confirm(\'Êtes-vous sûr de vouloir supprimer cette question ?\');">
                        <input type="hidden" name="id_question" value="'.$question["id_question"].'">
                        <button type="submit" name="delete_question" style="background-color: red; color: white; padding: 0.5rem; border-radius: 5px;">
                            🗑️ Supprimer cette question
                        </button>
                    </form>
                </div>
                <div class="separateModif"></div>
                ';
                
            }  
        }
        //Ajouter une nouvelle question au quizz
        $output .= "
        <div id='submitModifierQuizz'>
            <form method='POST' action='controllerAdmin.php'>
                <input type='hidden' name='id_jeu_de_piste' value='{$idQuizz}'>
                <button type='submit' name='ajouter_question'>Ajouter une nouvelle question</button><br>
            </form>
        </div>";
        return $output;
    }catch(Exception $e){
        return $e->getMessage();
    }
}

// //! Fonction pour créer un nouvelles question                OK
function renderFormulaireCreationQuestion($bdd, $idQuizz){
    try{
        $qrContent = uniqid("qrcode_");

        //Je récupère les animaux de la bdd
        $req = $bdd->query("SELECT id_animal, nom_animal FROM animal ORDER BY nom_animal ASC");
        $animaux = $req->fetchAll(PDO::FETCH_ASSOC);

        $options ='';
        foreach ($animaux as $animal){
            $idAnimal = (int)$animal['id_animal'];
            $nomAnimal = htmlspecialchars($animal['nom_animal']);
            $options .= "<option value='$idAnimal'>$nomAnimal</option>";
        }


        $formulaire = "
        <h2>Créer une nouvelle question pour le quizz '$idQuizz'</h2>
        <button onClick=\"toggleForm()\">Ajouter une question</button>

        <div id='formulaireCreation' style='display: none; margin-top: 20px;'>
            <form method='POST' action='controllerAdmin.php?action=ajouter_question'>
                <input type='hidden' name='id_quizz' value='$idQuizz'>
                <input type='hidden' name='qr_code' value='$qrContent'>

                <label for='question'>Question :</label><br>
                <textarea name='question' id='question' required></textarea><br>

                <label>Réponses :</label><br>
                <input type='text' name='reponses[]' placeholder='Réponse 1' required><br>
                <input type='text' name='reponses[]' placeholder='Réponse 2' required><br>
                <input type='text' name='reponses[]' placeholder='Réponse 3' required><br>
                <input type='text' name='reponses[]' placeholder='Réponse 4' required><br>

                <label for='bonne_reponse'>Numéro de la bonne réponse (1-4):</label><br>
                <input type='number' name='bonne_reponse' id='bonne_reponse' min='1' max='4' required><br>

                <label for='id_animal'>Associer un animal à la question</label><br>
                <select name='id_animal' id='id_animal' required>
                    <option value=''>--Sélectionner un animal --</option>
                    $options
                </select><br>

                <input type='submit' name='submitQuestion' value='Créer la question'>
            </form>
        </div>

        <script>
            function toggleForm(){
                const form = document.getElementById('formulaireCreation');
                form.style.display = form.style.display === 'none' ? 'block' : 'none';
            }
        </script>
        ";

        return $formulaire;

    }catch(Exception $e){
        return $e->getMessage();
    }
}

//!Fonction mise à jour dans la bdd d'une question (titre + texte)
function updateQuestion($bdd, $idQuestion, $titreQuestion, $texteQuestion){
    try{
        $req= $bdd->prepare("UPDATE question SET titre_question = ?, texte_question = ? WHERE id_question = ?");
        $req->bindParam(1, $titreQuestion, PDO::PARAM_STR);
        $req->bindParam(2, $texteQuestion, PDO::PARAM_STR);
        $req->bindParam(3, $idQuestion, PDO::PARAM_INT);

        $req->execute();

        return "<p style='color:green;'>Question mise à jour avec succès ! </p>";

    }catch(Exception $e){
        return $e->getMessage();
    }
}

//!Fonction mise à jour dans la bdd d'une réponse (texte + validité)
function updateReponse($bdd, $idReponse,$texteReponse, $validReponse){
    try {
        $req= $bdd->prepare("UPDATE reponse SET texte_reponse=?, valid_reponse=? WHERE id_reponse =?");
        $req->bindParam(1, $texteReponse, PDO::PARAM_STR);
        $req->bindParam(2, $validReponse, PDO::PARAM_BOOL);
        $req->bindParam(3, $idReponse, PDO::PARAM_INT);

        $req->execute();
        return "<p style='color:green; text-align: center; background-color: white;'>Réponse mise à jour avec succès !</p>";
    }catch(Exception $e){
        return $e->getMessage();
    }
}


//!Fonction qui ajoute une question à la bdd            OK
function addQuestion($bdd, $titre){
    try{
        $req = $bdd->prepare("INSERT INTO question (titre_question) VALUES (?)");
        $req->bindParam(1, $titre, PDO::PARAM_STR);
        $req->execute();
        return $bdd->lastInsertId(); // Retourne l'ID de la dernière question insérée
    }catch(Exception $e){
        return $e->getMessage();
    }
}

//! Fonction qui ajoute les réponses à la bdd           OK
function addReponses($bdd, $idQuestion, $texteReponse, $isBonneReponse){
    try{
        $req = $bdd->prepare("INSERT INTO reponse(texte_reponse, valid_reponse, id_question) VALUES (?, ?, ?)");
        $req->bindParam(1, $texteReponse, PDO::PARAM_STR);
        $req->bindParam(2, $isBonneReponse, PDO::PARAM_BOOL);
        $req->bindParam(3, $idQuestion, PDO::PARAM_INT);
        $req->execute();
        return true; // Indique que l'insertion a réussi
}catch(Exception $e){
        return $e->getMessage();
    }
}

//!Fonction qui supprime une association qr code animal via rattacher
function deleteQRCodeAnimal($bdd, $idQRCode, $idAnimal){
    try{
        $req = $bdd->prepare("DELETE FROM rattacher WHERE id_qr_code = ? AND id_animal");
        $req->bindParam(1, $idQRCode, PDO::PARAM_INT);
        $req->bindParam(2, $idAnimal, PDO::PARAM_INT);
        $req->execute();

        return true;
    }catch(Exception $e){
        return $e->getMessage();
    }
}

//!Fonction qui lie un animal à un qr code
function rattacherQRCodeAnimal($bdd, $idQRCode, $idAnimal){
    try{
        $req = $bdd->prepare("INSERT INTO rattacher (id_qr_code, id_animal) VALUES (?, ?)");
        $req->bindParam(1, $idQRCode, PDO::PARAM_INT);
        $req->bindParam(2, $idAnimal, PDO::PARAM_INT);
        $req->execute();
        return true; // Indique que l'association a réussi
    }catch(Exception $e){
        return $e->getMessage();
    }
}


//!Fonction qui supprime les réponses liées à une question
function deleteReponse($bdd, $idQuestion){
    try{
        $req = $bdd->prepare("DELETE FROM reponse WHERE id_question = ?");
        $req->bindParam(1, $idQuestion, PDO::PARAM_INT);
        $req->execute();
        return true; // Indique que la suppression a réussi
    }catch(Exception $e){
        return $e->getMessage();
    }
}
//!Fonction qui supprime l'association avec le QR code
function deleteAssocier($bdd, $idQuestion){
    try{
        $req = $bdd->prepare("DELETE FROM associer WHERE id_question = ?");
        $req->bindParam(1, $idQuestion, PDO::PARAM_INT);
        $req->execute();
        return true; // Indique que la suppression a réussi
    }catch(Exception $e){
        return $e->getMessage();
    }
}
//!Fonction qui supprime une question de la bdd
function deleteQuestion($bdd, $idQuestion){
    try{
        $req = $bdd->prepare("DELETE FROM question WHERE id_question = ?");
        $req->bindParam(1, $idQuestion, PDO::PARAM_INT);
        $req->execute();
        return true; // Indique que la suppression a réussi
    }catch(Exception $e){
        return $e->getMessage();
    }
}
//!Fonction qui supprime un QR code de la bdd
function deleteQRCode($bdd, $idQRCode){
    try{
        $req = $bdd->prepare("DELETE FROM qr_code WHERE id_qr_code = ?");
        $req->bindParam(1, $idQRCode, PDO::PARAM_INT);
        $req->execute();
        return true; // Indique que la suppression a réussi
    }catch(Exception $e){
        return $e->getMessage();
    }
}

function deleteQRCodeComplet($bdd, $idQuestion){
    try{
        $req = $bdd->prepare("SELECT id_qr_code FROM associer WHERE id_question = ?");
        $req->execute([$idQuestion]);
        $qrData= $req->fetch(PDO::FETCH_ASSOC);
        $idQRCode = $qrData ? $qrData['id_qr_code'] : null;

        deleteReponse($bdd, $idQuestion);
        deleteAssocier($bdd, $idQuestion);
        deleteQuestion($bdd, $idQuestion);

        if($idQRCode) {
            deleteQRCode($bdd, $idQRCode);
            $qrImagePath = 'public/qr_codes/' . $idQRCode . '.png';
            if (file_exists($qrImagePath)) {
                unlink($qrImagePath); // Supprime le fichier QR code
            }
        }
        return true; // Indique que la suppression a réussi
    }catch (Exception $e){
        return $e->getMessage();
    }
}

//!Fonction qui génère un QR code et l'enregistre dans le dossier   OK
function genererQRCode($contenu, $nomFichier){
    $chemin = 'public/qr_codes/' . $nomFichier . '.png';

    if(!file_exists('public/qr_codes/')){
        mkdir('public/qr_codes/', 0777, true); // Crée le dossier s'il n'existe pas
    }
    QRcode::png($contenu, $chemin, QR_ECLEVEL_L, 5); // Génère le QR code
    return $chemin; // Retourne le chemin du QR code généré
}

//!Fonction qui ajoute le QR code à la bdd          OK
function addQRCode($bdd, $code, $position){
    try{
        $req = $bdd->prepare("INSERT INTO qr_code (code_qr_code, position_qr_code) VALUES (?, ?)");
        $req->bindParam(1, $code, PDO::PARAM_STR);
        $req->bindParam(2, $position, PDO::PARAM_INT);
        $req->execute();
        return $bdd->lastInsertId(); // Retourne l'ID du dernier QR code inséré
    }catch(Exception $e){
        return $e->getMessage();
    }
}

//!Fonction qui lie le QR code à la question (table associer)       OK
function associerQRCodeQuestion($bdd, $idQRCode, $idQuestion){
    try {
        $req= $bdd->prepare("INSERT INTO associer (id_qr_code, id_question) VALUES (?, ?)");
        $req->bindParam(1, $idQRCode, PDO::PARAM_INT);
        $req->bindParam(2, $idQuestion, PDO::PARAM_INT);
        $req->execute();
        return true; // Indique que l'association a réussi
    }catch(Exception $e){
        return $e->getMessage();
    }
}

//!Fonction qui lie le QR au quizz (table contenir)         OK
function associerQRCodeJeuDePiste($bdd, $idQRCode, $idJeuDePiste){
    try{
        $req = $bdd->prepare("INSERT INTO contenir (id_jeu_de_piste, id_qr_code) VALUES (?,?)");
        $req->bindParam(1, $idJeuDePiste, PDO::PARAM_INT);
        $req->bindParam(2, $idQRCode, PDO::PARAM_INT);
        $req->execute();
        return true; // Indique que l'association a réussi
    }catch(Exception $e){
        return $e->getMessage();
    }
}


//! Fonction qui récupère tous les quizz de la bdd
function getAllQuizz($bdd){
    $req = $bdd->prepare("SELECT id_jeu_de_piste, nmae_jeu_de_piste FROM jeu_de_piste ORDER BY date_jeu_de_piste DESC");
    return $req->fetchAll(PDO::FETCH_ASSOC);
}
?>
