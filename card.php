<?php 

session_start();

define("GUARD", 1);

if (!isset($_SERVER["HTTP_FETCH"])) die("Directly not accessible!");

include_once("lib/cardStorage.php");
include_once("lib/userStorage.php");
include_once("lib/auth.php");

$cardStorage = new CardStorage();
$cards = $cardStorage->findAll();

$get = $_GET;

$card = NULL;

if (!isset($get["id"]) || !is_string(trim($get["id"])) || trim($get["id"]) === "") return;
else $card = $cardStorage->findById($get["id"]);

if (!$card) return;
?>
<div class="pokemon-card">
    <a href="details.php?id=<?= $get["id"] ?>">
        <div class="image clr-<?= $card["type"] ?>"><img src="<?= $card["image"] ?>"></div>
    </a>
    <div class="details">
        <h2><a href="details.php?id=<?= $get["id"] ?>"><?= $card["name"] ?></a></h2>
        <span class="card-type"><span class="icon">🏷</span> <?= $card["type"] ?></span>
        <span class="attributes">
            <span class="card-hp"><span class="icon">❤</span> <?= $card["hp"] ?></span>
            <span class="card-attack"><span class="icon">⚔</span> <?= $card["attack"] ?></span>
            <span class="card-defense"><span class="icon">🛡</span> <?= $card["defense"] ?></span>
        </span>
        <span>Owner: <?= $card["owner"] ?></span>
    </div>
</div>