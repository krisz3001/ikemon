<?php 

    session_start();

    define("GUARD", 1);
    
    include_once("lib/cardStorage.php");
    include_once("lib/userStorage.php");
    include_once("lib/auth.php");
    
    $userStorage = new UserStorage();
    $auth = new Auth($userStorage);
    $cardStorage = new CardStorage("cards.json");

    $id = $_GET["id"];

    if (!isset($id) || trim($id) === "") redirect("/");

    $card = $cardStorage->findById($id);

    if (!$card) redirect("/");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>IKémon | <?= $card["name"] ?></title>
        <link rel="stylesheet" href="styles/main.css">
        <link rel="stylesheet" href="styles/details.css">
    </head>
    <body>
    <header>
        <div id="menu">
            <h1><a href="/">IKémon</a> | <?= $card["name"] ?></h1>
            <div id="menubar">
                <div id="menu-buttons">
                    <?php if ($auth->is_authenticated()) : ?>
                        <a href="/"><button class="btn">Home</button></a>
                        <a href="/account.php"><button class="btn">Account</button></a>
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
        </div>
        <?php endif ?>
    </header>
        <div id="content" style="display: flex;">
            <div id="details">
                <div class="image clr-<?= $card["type"] ?>">
                    <img src="<?= $card["image"] ?>">
                </div>
                <div class="info">
                    <h2 class="name"><?= $card["name"] ?></h2>
                    <div class="description"><?= $card["description"] ?></div>
                    <span class="card-type"><span class="icon">🏷</span> Type: <?= $card["type"] ?></span>
                    <div class="attributes">
                        <div class="card-hp"><span class="icon">❤</span> Health: <?= $card["hp"] ?></div>
                        <div class="card-attack"><span class="icon">⚔</span> Attack: <?= $card["attack"] ?></div>
                        <div class="card-defense"><span class="icon">🛡</span> Defense: <?= $card["defense"] ?></div>
                    </div>
                </div>
            </div>
        </div>
        <footer>
            <p>IKémon | ELTE IK Webprogramozás</p>
        </footer>
    </body>
</html>