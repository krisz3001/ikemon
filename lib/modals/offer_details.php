<?php if (!defined("GUARD")) die("Directly not accessible!"); ?>

<?php if ($auth->is_authenticated() && count($requests) > 0) : ?>
    <div class="modal" id="modal-request">
        <form class="modal-trade" method="POST" action="">
            <span class="offer-title">Trade offer</span>
            <div class="offer-cards">
                <div id="request-left">
                    <span class="offer-info">You get</span>
                    <div id="request-card-first"></div>
                    <div class="payment">
                        <label>You pay</label>
                        <span id="request-pay"></span>
                    </div>
                </div>
                <div class="offer-divider"><span class="material-symbols-outlined btn-trade-icon">currency_exchange</span></div>
                <div id="request-right">
                    <span class="offer-info">You give</span>
                    <div id="request-card-second"></div>
                    <div class="payment">
                        <label>They pay</label>
                        <span id="request-receive"></span>
                    </div>
                    <input type="hidden" name="id" id="request-id">
                    <input type="hidden" name="answer" id="request-answer">
                    <input type="hidden" name="request">
                </div>
            </div>
            <div id="request-replies">
                <img src="img/success.jpg" id="request-accept" data-reply="accept" class="request-reply">
                <img src="img/error.jpg" id="request-deny" data-reply="deny" class="request-reply">
            </div>
            <div class="request-btn">
                <button class="btn disabled" id="btn-request-confirm"></button>
                <button class="btn" id="btn-request-close">Close</button>
            </div>
        </form>
    </div>
<?php endif ?>