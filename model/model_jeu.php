<?php

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
        WHERE qr.code = :code
    ");
    $req->execute([':code' => $code]);
    $quizz= $req->fetchAll(PDO::FETCH_ASSOC);

    if(empty($quizz)){
        
    }
}