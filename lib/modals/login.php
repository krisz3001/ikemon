<?php if (!defined("GUARD")) die("Directly not accessible!"); ?>

<div class="modal <?php if (isset($_SESSION["login"])) echo "active" ?>" id="modal-login">
    <form action="" method="POST" id="login" class="modal-form">
        <div>
            <button type="button" class="btn-close" id="btn-login-close">✖</button>
            <?php if (isset($_SESSION["login"]) && isset($_SESSION["login"]["errors"])) : ?>
                <ul class="errors">
                    <?php foreach ($_SESSION["login"]["errors"] as $value) : ?>
                        <li><?= $value ?></li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
            <label for="login-username">Username</label>
            <input type="text" name="username" id="login-username" value="<?= isset($_SESSION["login"]) ? $_SESSION["login"]["username"] ?? "" : ""  ?>" autocomplete="username">
            <label for="login-password">Password</label>
            <input type="password" name="password" id="login-password" autocomplete="current-password">
            <input type="hidden" name="login">
        </div>
        <div class="buttons">
            <input type="submit" value="Login" id="btn-login" class="btn">
            <?php unset($_SESSION["login"]) ?>
        </div>
    </form>
</div>