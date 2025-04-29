<?php
session_start();
include "./model/model_jeu.php";
include "./utils/function.php";

$bdd = DBconnect();
$bdd->exec("SET NAMES 'utf8mb4'");
$message="";


include "./view/jeu/header_compteConnecte.php";
include "./view/jeu/view_afficher_question.php";
include "./view/footer.php";