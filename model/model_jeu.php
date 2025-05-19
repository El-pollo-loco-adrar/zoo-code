<?php

//!Fonction qui récupère les questions + réponses d'un quizz par le QR code
function getQuestionByQrCode($bdd,$code){
    $req= $bdd->prepare("SELECT
            q.id_question,
            q.titre_question,
            r.id_reponse,
            r.texte_reponse,
            r.valid_reponse
        FROM qr_code as qr
        JOIN associer as a ON qr.id_qr_code = a.id_qr_code
        JOIN question as q ON a.id_question = q.id_question
        JOIN reponse as r ON q.id_question = r.id_question
        WHERE qr.code_qr_code = :code
    ");
    $req->execute([':code' => $code]);
    $quizz= $req->fetchAll(PDO::FETCH_ASSOC);

    if(empty($quizz)){
        return "<p style='color: red;'>Une erreur s'est produite. Veuillez contacter l'accueil.</p>";
    }else{
        $question = [
            "id_question" => $quizz[0]["id_question"],
            "titre_question" => $quizz[0]["titre_question"],
            "reponse" => []
        ];

        foreach($quizz as $row){
            $question["reponse"][] = [
                "id_reponse" => $row["id_reponse"],
                "texte_reponse" => $row["texte_reponse"],
                "valid_reponse" => $row["valid_reponse"]
            ];
        }
        return $question;
    }

}

//!Fonction qui récupère et affiche le nom du quizz
function getNameQuizz($bdd, $code){
    $req= $bdd->prepare("SELECT
            jp.name_jeu_de_piste
        FROM qr_code AS qr
        JOIN contenir AS c ON qr.id_qr_code = c.id_qr_code
        JOIN jeu_de_piste AS jp ON c.id_jeu_de_piste = jp.id_jeu_de_piste
        WHERE qr.code_qr_code = :code
    ");
    
    $req->execute([':code' => $code]);
    $data= $req->fetch(PDO::FETCH_ASSOC);

    return $data ? $data['name_jeu_de_piste'] : null;
}