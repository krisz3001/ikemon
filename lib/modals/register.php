<?php if (!defined("GUARD")) die("Directly not accessible!"); ?>

<div class="modal <?php if (isset($_SESSION["register"])) echo "active" ?>" id="modal-register">
    <form action="" method="POST" id="register" class="modal-form" novalidate>
        <div>
            <button type="button" class="btn-close" id="btn-register-close">✖</button>
            <?php if (isset($_SESSION["register"]) && isset($_SESSION["register"]["errors"])) : ?>
                <ul class="errors">
                    <?php foreach ($_SESSION["register"]["errors"] as $value) : ?>
                        <li><?= $value ?></li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
            <label for="register-username">Username</label>
            <input type="text" name="username" id="register-username" value="<?= isset($_SESSION["register"]) ? $_SESSION["register"]["username"] ?? "" : ""  ?>" autocomplete="username">
            <label for="register-email">Email</label>
            <input type="email" name="email" id="register-email" value="<?= isset($_SESSION["register"]) ? $_SESSION["register"]["email"] ?? "" : ""  ?>">
            <label for="register-password">Password</label>
            <input type="password" name="password" id="register-password" autocomplete="new-password">
            <label for="register-password-again">Password again</label>
            <input type="password" name="passwordagain" id="register-password-again" autocomplete="new-password">
            <input type="hidden" name="register">
        </div>
        <div class="buttons">
            <input type="submit" value="Register" id="btn-register" class="btn">
            <?php unset($_SESSION["register"]) ?>
        </div>
    </form>
</div>