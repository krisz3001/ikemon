<?php if (!defined("GUARD")) die("Directly not accessible!"); ?>

<div id="bell">
    <span id="notification-count" class="<?= count($requests) > 0 ? "notification-count-active" : "" ?>"><span><?= count($requests) ?></span></span>
    <span class="material-symbols-outlined <?= count($requests) > 0 ? "notification-icon-filled" : "" ?>" id="notification-icon" title="Notifications">notifications</span>
    <div id="notifications">
        <?php foreach ($requests as $id => $request) : ?>
            <?php $give = $cardStorage->findById($request["give"]); ?>
            <?php $get = $cardStorage->findById($request["get"]); ?>
            <div class="notification" data-id="<?= $id ?>" data-from="<?= $request["from"] ?>" data-to="<?= $request["to"] ?>" data-give="<?= $request["give"] ?>" data-get="<?= $request["get"] ?>" data-pay="<?= $request["pay"] ?>" data-receive="<?= $request["receive"] ?>">
                <div class="notification-body">
                    <span class="material-symbols-outlined trade-icon">currency_exchange</span>
                    <span class="notification-text">Trade offer from <span class="trade-from"><?= $userStorage->findById($request["from"])["username"] ?></span></span>
                </div>
            </div>
        <?php endforeach ?>
        <?php if (count($requests) === 0) : ?>
            <span style="text-align: center;">You have no notifications</span>
        <?php endif ?>
    </div>
</div>