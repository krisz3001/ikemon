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

if (!isset($get["type"]) || !is_string($get["type"]) || trim($get["type"]) === "") $type = $_SESSION["pager"]["type"] ?? "all";
else $type = $get["type"];

$_SESSION["pager"]["type"] = $type;

$pages = $cardStorage->get_page_count($type);

?>
<?php for ($i = 0; $i < $pages; $i++) : ?>
    <button class="btn <?= $i == ($_SESSION["pager"]["page"] ?? 0) ? "pager-active" : "" ?>" data-index="<?= $i ?>"><?= $i + 1 ?></button>
<?php endfor ?>