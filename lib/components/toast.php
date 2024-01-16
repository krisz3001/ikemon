<?php if (!defined("GUARD")) die("Directly not accessible!"); ?>

<?php 
    const TOAST_HEIGHT = 80;
    const MARGIN = 20;
?>

<div id="toasts">
    <?php foreach ($toasts as $index => $toast) : ?>
        <div class="toast" data-id="<?= $index ?>" <?php if ($index > 0) : ?> style="bottom: <?= (TOAST_HEIGHT + MARGIN) * $index + MARGIN ?>px;" <?php endif ?> >
            <img src="<?= $toast["img"] ?>"  class="toast-img clr-<?= $toast["type"] ?>">
            <span class="toast-msg"><?= $toast["msg"] ?></span>
        </div>
    <?php endforeach ?>
    <?php unset($_SESSION["toasts"]); ?>
</div>