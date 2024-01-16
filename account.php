<?php

    session_start();

    define("GUARD", 1);

    include_once("lib/cardStorage.php");
    include_once("lib/userStorage.php");
    include_once("lib/auth.php");

    const REFUND_MODIFIER = 0.9;

    $cardStorage = new CardStorage();
    $userStorage = new UserStorage();
    $auth = new Auth($userStorage);

    if (!$auth->is_authenticated()) {
        redirect("/");
    }
    
    $user = $_SESSION["user"];
    $cards = $cardStorage->findByOwner($user["username"]);

    $requests = [];

    if ($auth->is_authenticated()) $requests = $userStorage->getRequests($_SESSION["user"]["id"]);

    $errors = [];

    include_once("lib/post_processor.php");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>IKémon | Account</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
        <link rel="stylesheet" href="styles/main.css">
        <link rel="stylesheet" href="styles/notifications.css">
        <link rel="stylesheet" href="styles/cards.css">
        <link rel="stylesheet" href="styles/offers.css">
        <link rel="stylesheet" href="styles/toasts.css">
    </head>
    <body>
        <?php 
            include_once("lib/modals/refund.php");
            include_once("lib/modals/edit.php");
            include_once("lib/modals/offer_details.php");
            include_once("lib/modals/random.php");
            include_once("lib/components/toast.php");
        ?>
        <header>
            <div id="menu">
                <h1><a href="/">IKémon</a> | Account</h1>
                <div id="menubar">
                    <div id="menu-buttons">
                        <?php if ($auth->is_authenticated()) : ?>
                            <a href="/"><button class="btn">Home</button></a>
                            <?php if (!$auth->is_admin()) : ?>
                                <button id="btn-random-card" class="btn">Buy random card</button>
                            <?php endif ?>
                            <a href="/logout.php"><button class="btn">Logout</button></a>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <?php if ($auth->is_authenticated()) : ?>
            <div id="account-info">
                <span>Logged in as: <a href="/account.php" class="<?= $auth->is_admin() ? "admin" : "" ?> link"><?= $_SESSION["user"]["username"] ?></a></span>
                <span>💰<?= $_SESSION["user"]["money"] ?></span>
                <?php $count = count($cardStorage->findByOwner($_SESSION["user"]["username"])); ?>
                <span>You have <?= $count ?> card<?= $count === 1 ? "" : "s" ?></span>
                <span>Limit: <?= $cardStorage::LIMIT ?></span>
                <?php include_once("lib/components/notifications.php"); ?>
            </div>
            <?php endif ?>
        </header>
        <div id="content">
            <table>
                <caption><h2>Account details</h2></caption>
                <tr>
                    <td>Username</td>
                    <td <?= $auth->is_admin() ? 'class="admin"' : "" ?>><?= $user["username"] ?></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><?= $user["email"] ?></td>
                </tr>
                <tr>
                    <td>Money</td>
                    <td>💰<?= $user["money"] ?></td>
                </tr>
                <tr>
                    <td>Admin?</td>
                    <td><?= $auth->is_admin() == 1 ? "Yes" : "No" ?></td>
                </tr>
                <tr>
                    <td>Cards owned</td>
                    <td><?= count($cardStorage->findByOwner($user["username"])) ?></td>
                </tr>
            </table>
            <div id="card-list">
                <?php foreach ($cards as $key => $value) : ?>
                    <div class="pokemon-card">
                        <div class="image clr-<?= $value["type"] ?>">
                            <a href="details.php?id=<?= $key ?>"><img src="<?= $value["image"] ?>"></a>
                        </div>
                        <div class="details">
                        <?php if ($auth->is_admin()) : ?>
                            <button class="icon btn-edit" title="Edit card" 
                                    data-card='{"id": "<?= $key ?>", 
                                                "name": "<?= $value["name"] ?>", 
                                                "image" : "<?= $value["image"] ?>", 
                                                "description" : "<?= $value["description"] ?>", 
                                                "hp" : "<?= $value["hp"] ?>", 
                                                "attack" : "<?= $value["attack"] ?>", 
                                                "defense" : "<?= $value["defense"] ?>", 
                                                "price" : "<?= $value["price"] ?>", 
                                                "owner" : "<?= $value["owner"] ?>", 
                                                "type" : "<?= array_search($value["type"], $cardStorage::TYPES) ?>"}'>✏</button>
                        <?php endif ?>
                            <h2><a href="details.php?id=<?= $key ?>"><?= $value["name"] ?></a></h2>
                            <span class="card-type"><span class="icon">🏷</span> <?= $value["type"] ?></span>
                            <span class="attributes">
                                <span class="card-hp"><span class="icon">❤</span> <?= $value["hp"] ?></span>
                                <span class="card-attack"><span class="icon">⚔</span> <?= $value["attack"] ?></span>
                                <span class="card-defense"><span class="icon">🛡</span> <?= $value["defense"] ?></span>
                            </span>
                            <span>Owner: <?= $value["owner"] ?></span>
                        </div>
                        <?php if (!$auth->is_admin() && !$cardStorage->isLocked($value)) : ?>
                            <div class="trade" data-id="<?= $key ?>" data-price="<?= $value["price"] * REFUND_MODIFIER ?>" data-name="<?= $value["name"] ?>">
                                <span class="card-price"><span class="icon">💸</span> <?= $value["price"] * REFUND_MODIFIER ?></span>
                            </div>
                        <?php elseif ($cardStorage->isLocked($value)) : ?>
                            <span class="material-symbols-outlined locked-icon" title="In pending trade offer">lock</span>
                        <?php endif ?>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
        <footer>
            <p>IKémon | ELTE IK Webprogramozás</p>
            <form action="" method="POST">
                <input type="hidden" name="nesi">
                <input type="submit" value="What's this?" id="nesi">
            </form>
        </footer>
        <script src="js/modal.js"></script>
        <script src="js/toast.js"></script>
        <?php if ($auth->is_authenticated()) : ?>
            <script src="js/notifications.js"></script>
        <?php endif ?>
        <?php if ($auth->is_authenticated() && !$auth->is_admin()) : ?>
            <script src="js/offers.js"></script>
        <?php endif ?>
    </body>
</html>