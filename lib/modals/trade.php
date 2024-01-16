<?php if (!defined("GUARD")) die("Directly not accessible!"); ?>

<div class="modal" id="modal-trade">
    <form class="modal-trade" method="POST" action="">
        <input type="hidden" name="buy">
        <input type="hidden" name="id" id="trade-id">
        <span>
            Buying
            <span id="trade-card"></span>
            from
            <span id="trade-owner"></span>
            for
            💰<span id="trade-price"></span>
        </span>
        <p>Are you sure?</p>
        <div>
            <button class="btn" id="btn-confirm">Buy</button>
            <button class="btn" id="btn-close">Close</button>
        </div>
    </form>
</div>