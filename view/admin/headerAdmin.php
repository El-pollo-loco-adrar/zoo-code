<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Découvrez le jeu de piste du Zoo de la Lèze : êtes-vous prêt à découvrir le parc dans son intimité?">
    <link rel="stylesheet" href="src\style\admin.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title><?= isset($title) ? $title : "Zoo de la Lèze"; ?></title>
</head>
<body>
    <div id="containerTotalConnexion">
        <header>
            <!--IMAGE DE FOND HEADER-->
            <img class="fondHeader" src="images/feuilles-1.png" alt="header">



                    <!--LOGO-->
            <div class="logo">
            <a href="././controllerAdmin.php"><img src="images/logo.png" alt="logo" style="width: 100px; height: 100px; object-fit: cover;" ></a>
            </div>
        </header>
        <div id="containerJeu">
        <h1>Bonjour <?= $_SESSION["pseudo_user"];?></h1>
        
        <ul class="menuAdmin">
            <li><a class="dropdown-item" href="././controllerAdmin.php">Accueil Admin</a></li>
            <li><a class="dropdown-item" href="controllerAdmin.php?action=quizz">Quizz</a></li>
            <li><a class="dropdown-item" href="controllerAdmin.php?action=utilisateurs">Utilisateurs</a></li>
            <li><a class="dropdown-item" href="./controllerAjoutAnimauxAdmin.php">Ajouter animal</a></li>
            <li><a class="dropdown-item" href="controllerAdmin.php?action=animal">liste animal</a></li>
            <li><a class="dropdown-item" href="./deco.php">Me déconnecter</a></li>
        </ul>
        <div class="separate"></div>
