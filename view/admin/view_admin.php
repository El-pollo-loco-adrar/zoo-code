<head>
    <link rel="stylesheet" href="src/style/compteUtilisateur.css">
    <script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
</head>

<div id="containerJeu">


        <ul class="infosAdmin">
            <?= $message ?>
        </ul>
        <ul class="infosAnimaux">
            <?php echo $messageListe ?>
        </ul>

        <div class="selectionQuizzAdmin">
            <form action="POST" action="controllerAdmin.php">
                <label for="quizzSelect"></label>
            </form>

        </div>
        <?php if(!empty($messageSupp)):?>
        <p style="color: red;"><?= $messageSupp;?> </p>
        <?php endif;?>
        

</div>