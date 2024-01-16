<?php if (!defined("GUARD")) die("Directly not accessible!"); ?>

<?php if ($auth->is_authenticated() && !$auth->is_admin()) : ?>
    <div class="modal" id="modal-random">
        <form class="modal-trade" method="POST" action="">
            <input type="hidden" name="random">
            <span>Buying a random card for 💰<?= RANDOM_CARD_PRICE ?></span>
            <p>Are you sure?</p>
            <div>
                <button class="btn" id="btn-random-confirm">Buy</button>
                <button class="btn" id="btn-random-close">Close</button>
            </div>
        </form>
    </div>
<?php endif ?>