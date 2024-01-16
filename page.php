<?php 

session_start();

define("GUARD", 1);

if (!isset($_SERVER["HTTP_FETCH"])) die("Directly not accessible!");

include_once("lib/cardStorage.php");
include_once("lib/userStorage.php");
include_once("lib/auth.php");

$userStorage = new UserStorage();
$auth = new Auth($userStorage);
$cardStorage = new CardStorage();

$get = $_GET;

if (!isset($get["page"]) || !is_numeric($get["page"])) $page = 0;
else $page = $get["page"];

if (!isset($get["type"]) || !is_string($get["type"]) || trim($get["type"]) === "") $type = $_SESSION["pager"]["type"] ?? "all";
else $type = $get["type"];

$_SESSION["pager"]["page"] = $page;
$_SESSION["pager"]["type"] = $type;

$cards = $cardStorage->get_nth_nine($page, $type);

?>

<?php $index = $page * 9 ?>
<?php foreach ($cards as $key => $value) : ?>
    <?php $is_owner_admin = $userStorage->findByName($value["owner"])["isadmin"] ?? false; ?>
    <div class="pokemon-card">
        <?php if ($auth->is_authenticated() && $value["owner"] != $_SESSION["user"]["username"] && !$is_owner_admin && !$auth->is_admin() && !$cardStorage->isLocked($value)) : ?>
            <button class="btn-trade" title="Trade card" data-index="<?= $index ?>" data-id="<?= $key ?>"><span class="material-symbols-outlined btn-trade-icon <?= in_array($value["type"], ["dark", "rock", "poison", "water", "psychic"]) ? "trade-light" : "" ?>">currency_exchange</span></button>
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
    <?php $index++ ?>
<?php endforeach ?>