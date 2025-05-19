<head>
    <link rel="stylesheet" href="src/style/afficherQuestion.css">
    <script src="src\script\scriptAfficherQuestion.js"></script>
</head>
<div id="containerQuestion">
    <?php if($nomQuizz): ?>
        <h1><?= htmlspecialchars($nomQuizz) ?></h1>
    <?php endif ?>
    <?php if($question): ?>
        <h3><?= htmlspecialchars($question['titre_question']) ?></h3>
        <form method="POST" action="verifier_reponse.php" class="form-reponses">
            <?php foreach ($question['reponse'] as $index => $reponse): ?>
                <button type="button"
                        class="reponse-btn"
                        data-reponse="<?= $reponse['id_reponse'] ?>">                   
                    <?= htmlspecialchars($reponse['texte_reponse']) ?>
                </button>            
            <?php endforeach ?>
            <input type="hidden" name="id_question" value="<?= $question['id_question'] ?>">
            <input type="hidden" name="reponse" id="hidden-reponse">
        </form>
        <?php else: ?>
            <p>Aucune question trouvée pour ce QR code.</p>
    <?php endif ?>
</div>
    