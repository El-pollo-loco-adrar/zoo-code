<head>
    <link rel="stylesheet" href="src/style/compteUtilisateur.css">
    <script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
</head>
<div id="containerJeu">


        <h2>AJOUT D'UN ANIMAL</h2>
        <P id="para">*Vous devez OBLIGATOIREMENT remplir tous les champs pour valider la nouvelle entrée</P>
        <form action="" method="POST" id="ajoutAnimal" enctype="multipart/form-data">

        <label for="photoAnimal" class="custom-file">Photo de l'animal</label>
            <input type="file" id="photoAnimal" accept=".png, .jpg" name="image" required>
            <div id="previewContainer">
                <img src="" alt="Votre photo" id="previewPhoto">
            </div>
            <label for="nom">Nom de l'animal</label>
            <input type="text" id="nom" name="nom" required>

            <label for="espece">Espèce de l'animal</label>
            <input type="text" id="espece" name="espece" required>

            <label for="description">Description de l'animal</label>
            <textarea name="description" id="description" required></textarea>

            <label for="age">Age de l'animal</label>
            <input type="number" id="inputAge" name="age" required>

            <p><?php echo $message; ?></p>
            <button type="submit" name="animalSubmit" value="envoyer">Valider</button>
        </form>

</div>