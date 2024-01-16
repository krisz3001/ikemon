<?php

    session_start();

    define("GUARD", 1);
    
    include_once("lib/cardStorage.php");
    include_once("lib/userStorage.php");
    include_once("lib/auth.php");
    
    $userStorage = new UserStorage();
    $auth = new Auth($userStorage);
    $cardStorage = new CardStorage();

    $type = $_SESSION["pager"]["type"] ?? "all";
    $page = $_SESSION["pager"]["page"] ?? 0;

    $pages = $cardStorage->get_page_count($type);

    if ($page > ($pages - 1)) $page = 0;
    
    $cards = $cardStorage->get_nth_nine($page, $type);

    $requests = $auth->is_authenticated() ? $userStorage->getRequests($_SESSION["user"]["id"]) : [];

    $errors = [];

    include_once("lib/post_processor.php");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>IKémon | Home</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
        <link rel="stylesheet" href="styles/main.css">
        <link rel="stylesheet" href="styles/notifications.css">
        <link rel="stylesheet" href="styles/cards.css">
        <link rel="stylesheet" href="styles/offers.css">
        <link rel="stylesheet" href="styles/toasts.css">
    </head>
    <body>
        <?php 
            include_once("lib/modals/login.php");
            include_once("lib/modals/register.php");
            include_once("lib/modals/trade.php");
            include_once("lib/modals/random.php");
            include_once("lib/modals/offer.php");
            include_once("lib/modals/offer_details.php");
            include_once("lib/modals/newcard.php");
            include_once("lib/components/toast.php");
        ?>
        <header>
            <div id="menu">
                <h1><a href="/">IKémon</a> | Home</h1>
                <div id="menubar">
                    <div id="menu-buttons">
                        <?php if (!$auth->is_authenticated()) : ?>
                            <button id="btn-login-show" class="btn">Login</button>
                            <button id="btn-register-show" class="btn">Register</button>
                        <?php endif ?>
                        <?php if ($auth->is_authenticated()) : ?>
                            <a href="/account.php"><button class="btn">Account</button></a>
                            <?php if ($auth->is_admin()) : ?>
                                <button id="btn-new-card" class="btn">New card</button>
                            <?php endif ?>
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
            <?php include_once("lib/components/filter.php"); ?>
            <div id="card-list">
                <?php foreach ($cards as $key => $value) : ?>
                    <?php $is_owner_admin = $userStorage->findByName($value["owner"])["isadmin"] ?? false; ?>
                    <div class="pokemon-card">
                        <?php if ($auth->is_authenticated() && $value["owner"] != $_SESSION["user"]["username"] && !$is_owner_admin && !$auth->is_admin() && !$cardStorage->isLocked($value)) : ?>
                            <button class="btn-trade" title="Trade card" data-id="<?= $key ?>"><span class="material-symbols-outlined btn-trade-icon <?= in_array($value["type"], ["dark", "rock", "poison", "water", "psychic"]) ? "trade-light" : "" ?>">currency_exchange</span></button>
                        <?php endif ?>
                        <a href="details.php?id=<?= $key ?>">
                            <div class="image clr-<?= $value["type"] ?>"><img src="<?= $value["image"] ?>"></div>
                        </a>
                        <div class="details">
                            <h2><a href="details.php?id=<?= $key ?>"><?= $value["name"] ?></a></h2>
                            <span class="card-type"><span class="icon">🏷</span> <?= $value["type"] ?></span>
                            <span class="attributes">
                                <span class="card-hp"><span class="icon">❤</span> <?= $value["hp"] ?></span>
                                <span class="card-attack"><span class="icon">⚔</span> <?= $value["attack"] ?></span>
                                <span class="card-defense"><span class="icon">🛡</span> <?= $value["defense"] ?></span>
                            </span>
                            <span>Owner: <?= $value["owner"] ?></span>
                        </div>
                        <?php if ($auth->is_authenticated() && $value["owner"] != $_SESSION["user"]["username"] && !$auth->is_admin() && $is_owner_admin && !$cardStorage->isLocked($value)) : ?>
                            <div class="trade" data-id="<?= $key ?>" data-price="<?= $value["price"] ?>" data-owner="<?= $value["owner"] ?>" data-name="<?= $value["name"] ?>">
                                <span class="card-price"><span class="icon">💰</span> <?= $value["price"] ?></span>
                            </div>
                        <?php elseif ($cardStorage->isLocked($value)): ?>
                            <span class="material-symbols-outlined locked-icon" title="In pending trade offer">lock</span>
                        <?php endif ?>
                    </div>
                <?php endforeach ?>
            </div>
            <div class="pager">
                <?php for ($i = 0; $i < $pages; $i++) : ?>
                    <button class="btn <?= $i == $page ? "pager-active" : "" ?>" data-index="<?= $i ?>"><?= $i + 1 ?></button>
                <?php endfor ?>
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
        <script src="js/pager.js"></script>
        <script src="js/filter.js"></script>
    </body>
</html>