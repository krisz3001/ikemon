<?php if (!defined("GUARD")) die("Directly not accessible!"); ?>

<?php $gettingValue = isset($_SESSION["offer"]) && isset($_SESSION["offer"]["getting"]) ? array_search($_SESSION["offer"]["getting"], array_keys($cards)) ?? "" : ""; ?>
<?php if ($auth->is_authenticated() && !$auth->is_admin()) : ?>
    <div class="modal <?php if (isset($_SESSION["offer"]) && $gettingValue != "") echo "active" ?>" id="modal-offer">
        <form class="modal-trade" method="POST" action="">
            <span class="offer-title">Trade offer</span>
            <div class="offer-cards">
                <div id="offer-left">
                    <span class="offer-info">You get</span>
                    <div id="offer-card-first"></div>
                    <div class="payment">
                        <label for="pay-this">You pay</label>
                        <input type="number" name="pay" id="pay-this" value="<?= isset($_SESSION["offer"]) ? $_SESSION["offer"]["pay"] ?? "0" : "0"  ?>">
                    </div>
                </div>
                <div class="offer-divider"><span class="material-symbols-outlined btn-trade-icon">currency_exchange</span></div>
                <div id="offer-right">
                    <span class="offer-info">You give</span>
                    <div id="offer-card-second"></div>
                    <select id="offer-select" name="giving">
                        <option value="" style="text-align: center;" disabled selected>--- Select card ---</option>
                        <?php $index = 0 ?>
                        <?php $allCards = $cardStorage->findAll() ?>
                        <?php foreach ($allCards as $key => $value) : ?>
                            <?php if ($value["owner"] === $_SESSION["user"]["username"] && !$cardStorage->isLocked($value)) : ?>
                                <option value="<?= $key ?>" data-id="<?= $index ?>" <?php if (isset($_SESSION["offer"]) && isset($_SESSION["offer"]["giving"]) && $_SESSION["offer"]["giving"] === $key) echo "selected" ?>><?= $value["name"] ?></option>
                            <?php endif ?>
                            <?php $index++ ?>
                        <?php endforeach ?>
                    </select>
                    <div class="payment">
                        <label for="pay-that">They pay</label>
                        <input type="number" name="receive" id="pay-that" value="<?= isset($_SESSION["offer"]) ? $_SESSION["offer"]["receive"] ?? "0" : "0"  ?>">
                    </div>
                    <input type="hidden" name="offer">
                    <input type="hidden" name="getting" id="getting" data-index="<?= $gettingValue  ?>" value="<?= isset($_SESSION["offer"]) ? $_SESSION["offer"]["getting"] ?? "" : ""  ?>">
                </div>
            </div>
            <p>Are you sure?</p>
            <div>
                <button class="btn" id="btn-offer-confirm">Send offer</button>
                <button class="btn" id="btn-offer-close">Close</button>
                <?php unset($_SESSION["offer"]) ?>
            </div>
        </form>
    </div>
<?php endif ?>