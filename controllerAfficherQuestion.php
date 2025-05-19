<?php
session_start();
include "./model/model_jeu.php";
include "./utils/function.php";

$bdd = DBconnect();
$bdd->exec("SET NAMES 'utf8mb4'");
$code = $_GET['code'] ?? null;
$question = null;
$nomQuizz = getNameQuizz($bdd, $code);


if($code){
    $question = getQuestionByQrCode($bdd, $code);
}

include "./view/jeu/header_compteConnecte.php";
include "./view/jeu/view_afficher_question.php";
// include "./view/footer.php";
include "./view/jeu/footer_jeu.php";