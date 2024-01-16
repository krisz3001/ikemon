<?php if (!defined("GUARD")) die("Directly not accessible!"); ?>

<?php 

const RANDOM_CARD_PRICE = 50;

const IMG_ERROR = "img/error.jpg";
const IMG_SUCCESS = "img/success.jpg";
const IMG_REGISTERED = "img/registered.jpg";
const IMG_TRADE = "img/trade.png";
const IMG_NOPE = "img/nope.gif";

$toasts = [];

// Adds a new toast to the session
function toast($type, $msg, $img) {
    $_SESSION["toasts"][] = ["type" => $type, "msg" => $msg, "img" => $img];
}

// Adds a new error toast to the session 
function errorToast($msg) {
    toast("error", $msg, IMG_ERROR);
}

function invalidate($reason, &$valid) {
    errorToast($reason);
    $valid = false;
}

function post_not_all_string() : bool {
    foreach ($_POST as $value) {
        if (!is_string($value)) return true;
    }
    return false;
}

if (post_not_all_string()) redirect("/");
else if (isset($_POST["register"])) {
    $_SESSION["register"] = $_POST;
    if (!isset($_POST["username"]) || trim($_POST["username"]) === "") {
        $errors[] = "Username required";
    }
    else if ($auth->user_exists($_POST["username"])) {
        $errors[] = "Username is taken";
    }
    if (!isset($_POST["email"]) || trim($_POST["email"]) === "") {
        $errors[] = "Email required";
    }
    else if (!preg_match('/[a-z0-9]+@[a-z0-9]+\.[a-z]+/', $_POST["email"])) {
        $errors[] = "Invalid email format";
    }
    if (!isset($_POST["password"]) || trim($_POST["password"]) === "") {
        $errors[] = "Password required";
    }
    else if (!isset($_POST["passwordagain"]) || trim($_POST["passwordagain"]) === "") {
        $errors[] = "Confirm password";
    }
    else if ($_POST["password"] != $_POST["passwordagain"]) {
        $errors[] = "Passwords must match";
    }
    $_SESSION["register"]["errors"] = $errors;
    if (count($errors) === 0) {
        $auth->register([
            "username" => $_POST["username"],
            "email" => $_POST["email"],
            "password" => $_POST["password"],
        ]);
        toast("success", "Registered successfully!", IMG_SUCCESS);
        toast("success", "Welcome!", IMG_REGISTERED);
        unset($_SESSION["register"]);
    }
    redirect("/");
    
}
else if (isset($_POST["login"])) {
    $_SESSION["login"] = $_POST;
    if (!isset($_POST["username"]) || trim($_POST["username"]) === "") {
        $errors[] = "Username required";
    }
    if (!isset($_POST["password"]) || trim($_POST["password"]) === "") {
        $errors[] = "Password required";
    }
    if (count($errors) === 0) {
        $user = $auth->authenticate($_POST["username"], $_POST["password"]);
        if (!$user) {
            $errors[] = "Invalid credentials";
        } else  {
            $auth->login($user);
            unset($_SESSION["login"]);
        }
    }
    if (count($errors) > 0) $_SESSION["login"]["errors"] = $errors;
    redirect('/');
}
else if (isset($_POST["buy"])) {
    $valid = true;

    if (!$auth->is_authenticated()) invalidate("Unauthorized", $valid);
    if (!isset($_POST["id"]) || !is_string($_POST["id"]) || trim($_POST["id"]) === "") invalidate("ID is not set!", $valid);

    $card = $cardStorage->findById($_POST["id"]);

    if (!$card) invalidate("Invalid card id", $valid);
    else if (!$userStorage->findByName($card["owner"])["isadmin"]) invalidate("You can buy from admin only!", $valid);

    if ($valid) {
        $buyerid = $_SESSION["user"]["id"];
        $buyer = $_SESSION["user"]["username"];

        if (!$userStorage->canAfford($buyerid, $card["price"])) invalidate("You can not afford that!", $valid);
        if (!$cardStorage->hasSpace($buyer)) invalidate("No space for more cards!", $valid);

        if ($valid) {
            $cardStorage->transfer($_POST["id"], $buyer);
            $userStorage->spendMoney($buyerid, $card["price"]);
            toast($card["type"], $card["name"] . " has been added to your collection!", $card["image"]);
        }
    }
    redirect("/");
}
else if (isset($_POST["newcard"])) {
    if (!$auth->is_authenticated() || !$auth->is_admin()) $errors[] = "Unauthorized";

    $_SESSION["newcard"] = $_POST;

    $cardStorage->checkData($_POST, $errors, $auth);

    $_SESSION["newcard"]["errors"] = $errors;

    if (count($errors) === 0) {
        $cardStorage->addCard($_POST["name"], $_POST["type"], $_POST["hp"], $_POST["attack"], $_POST["defense"], $_POST["price"], $_POST["description"], $_POST["image"]);
        toast($_POST["type"], $_POST["name"] . " has been added!", $_POST["image"]);
        unset($_SESSION["newcard"]);
    }
    redirect("/");
}
else if (isset($_POST["random"])) {
    $valid = true;

    if (!$auth->is_authenticated() || $auth->is_admin()) invalidate("Unauthorized!", $valid);

    $admin_cards = $cardStorage->findByOwner("admin");
    if (count($admin_cards) === 0) invalidate("No more cards to buy!", $valid);

    if ($valid) {
        $card_id = array_rand($admin_cards);
        $card = $admin_cards[$card_id];
        $buyerid = $_SESSION["user"]["id"];
        $buyer = $_SESSION["user"]["username"];

        if (!$userStorage->canAfford($buyerid, RANDOM_CARD_PRICE)) invalidate("You can not afford that!", $valid);
        if (!$cardStorage->hasSpace($buyer)) invalidate("No space for more cards!", $valid);

        if ($valid) {
            $cardStorage->transfer($card_id, $buyer);
            $userStorage->spendMoney($buyerid, RANDOM_CARD_PRICE);
            toast($card["type"], $card["name"] . " has been added to your collection!", $card["image"]);
        }
    }
    redirect($_SERVER["REQUEST_URI"]);
}
else if (isset($_POST["offer"])) {
    $valid = true;
    
    if (!$auth->is_authenticated()) invalidate("Unauthorized!", $valid);

    if ($auth->is_admin()) invalidate("Admin can not send trade offers!", $valid);

    $_SESSION["offer"] = $_POST;
    
    if (!isset($_POST["getting"]) || trim($_POST["getting"]) === "") invalidate("The card you get is not set!", $valid);
    else {
        $gettingCard = $cardStorage->findById($_POST["getting"]);

        if (!$gettingCard) invalidate("The card you get does not exist!", $valid);
        else $recipientName = $gettingCard["owner"];
    }

    if (!isset($_POST["giving"]) || trim($_POST["giving"]) === "") invalidate("The card you give is not set!", $valid);
    else {
        $givingCard = $cardStorage->findById($_POST["giving"]);

        if (!$givingCard) invalidate("The card you give does not exist!", $valid);
        else if ($givingCard["owner"] != $_SESSION["user"]["username"]) invalidate("The card you give is not yours!", $valid);
        else if (isset($givingCard["locked"]) && $givingCard["locked"]) invalidate("The card you give is locked!", $valid);
    }

    if (!isset($_POST["pay"]) || trim($_POST["pay"]) === "") invalidate("The price you pay is not set!", $valid);
    else if (!is_numeric($_POST["pay"])) invalidate("The price you pay must be a number!", $valid);
    else if ($_POST["pay"] < 0) invalidate("The price you pay can not be negative!", $valid);

    if (!isset($_POST["receive"]) || trim($_POST["receive"]) === "") invalidate("The price they pay is not set!", $valid);
    else if (!is_numeric($_POST["receive"])) invalidate("The price they pay must be a number!", $valid);
    else if ($_POST["receive"] < 0) invalidate("The price they pay can not be negative!", $valid);

    $senderId = $_SESSION["user"]["id"];
    $recipientId = isset($recipientName) ? $userStorage->findByName($recipientName)["id"] : NULL;
    
    if (!$recipientId) invalidate("Recipient does not exist!", $valid);
    
    if (!$userStorage->canAfford($senderId, $_POST["pay"])) invalidate("You can not afford that!", $valid);

    if ($valid) {
        $pay = ltrim($_POST["pay"], "0");
        $receive = ltrim($_POST["receive"], "0");
        $userStorage->spendMoney($senderId, $pay === "" ? 0 : $pay);
        $cardStorage->lock($_POST["giving"]);
        $userStorage->addRequest($senderId, $recipientId, $_POST["giving"], $_POST["getting"], $pay === "" ? 0 : $pay, $receive === "" ? 0 : $receive);
        toast("success", "Trade offer sent to " . $gettingCard["owner"], IMG_SUCCESS);
        unset($_SESSION["offer"]);
    }
    redirect("/");
}
else if (isset($_POST["request"])) {
    $valid = true;
    
    if (!$auth->is_authenticated()) invalidate("Unauthorized!", $valid);

    if ($auth->is_admin()) invalidate("Admin can not answer trade offers!", $valid);

    if (!isset($_POST["id"]) || trim($_POST["id"] === "")) invalidate("ID is not set!", $valid);
    
    if (!isset($_POST["answer"]) || trim($_POST["answer"] === "")) invalidate("Answer is not set!", $valid);
    else if ($_POST["answer"] !== "accept" && $_POST["answer"] !== "deny") invalidate("Invalid answer!", $valid);

    $user_id = $auth->authenticated_user()["id"];
    if (!isset($auth->authenticated_user()["requests"][$_POST["id"]])) invalidate("Request ID is invalid!", $valid);

    if ($valid) {
        $request = $auth->authenticated_user()["requests"][$_POST["id"]];

        if (!$userStorage->canAfford($user_id, $request["receive"]) && $_POST["answer"] === "accept") invalidate("You can not afford that!", $valid);

        $card_remove = $cardStorage->findById($request["get"]);
        $card_add = $cardStorage->findById($request["give"]);

        if ($card_remove["owner"] !== $auth->authenticated_user()["username"] && $_POST["answer"] === "accept") invalidate("The card you give is not yours!", $valid);

        if ($valid) {
            if ($_POST["answer"] === "accept") {
                $userStorage->spendMoney($user_id, $request["receive"]);
                $userStorage->addMoney($request["from"], $request["receive"]);
                $userStorage->addMoney($request["to"], $request["pay"]);
                $cardStorage->transfer($request["give"], $auth->authenticated_user()["username"]);
                $cardStorage->transfer($request["get"], $userStorage->findById($request["from"])["username"]);
                $cardStorage->unlock($request["give"]);
                $userStorage->removeRequest($user_id, $_POST["id"]);
                toast("success", "Trade offer accepted!", IMG_TRADE);
                toast($card_remove["type"], $card_remove["name"] . " has been removed from your collection!", $card_remove["image"]);
                toast($card_add["type"], $card_add["name"] . " has been added to your collection!", $card_add["image"]);
            }
            else if ($_POST["answer"] === "deny") {
                $userStorage->addMoney($request["from"], $request["pay"]);
                $cardStorage->unlock($request["give"]);
                $userStorage->removeRequest($user_id, $_POST["id"]);
                toast("normal", "Trade offer denied!", IMG_NOPE);
            }
        }
    }
    redirect("/");
}
else if (isset($_POST["refund"])) {
    $valid = true;

    $card = NULL;

    if (!$auth->is_authenticated()) redirect("/");

    if (!isset($_POST["id"]) || !is_string($_POST["id"]) || trim($_POST["id"]) === "") invalidate("ID not set!", $valid);

    if ($valid) $card = $cardStorage->findById($_POST["id"]);

    if (!$card) invalidate("Card does not exist!", $valid);
    else if ($cardStorage->isLocked($card)) invalidate("Card is locked!", $valid);

    if ($valid) {
        $seller_id = $_SESSION["user"]["id"];
        
        $cardStorage->transfer($_POST["id"], "admin");
        $userStorage->addMoney($seller_id, $card["price"] * REFUND_MODIFIER);
        toast($card["type"], $card["name"] . " has been refunded!", $card["image"]);
    }
    redirect("/account.php");
}
else if (isset($_POST["edit"])) {

    if (!$auth->is_authenticated()) redirect("/");

    $_SESSION["edit"] = $_POST;
    $cardStorage->checkData($_POST, $errors, $auth);
    $_SESSION["edit"]["errors"] = $errors;

    if (count($errors) === 0) {
        $original_name = $cardStorage->findById($_POST["id"])["name"];
        $cardStorage->updateCard($_POST["id"], $_POST["name"], $_POST["type"], $_POST["hp"], $_POST["attack"], $_POST["defense"], $_POST["price"], $_POST["description"], $_POST["image"], $_POST["owner"]);
        toast($_POST["type"], $original_name . " has been edited!", $_POST["image"]);
        unset($_SESSION["edit"]);
    }
    redirect("/account.php");
}

if (isset($_POST["nesi"])) {
    toast("fire", "Nobody expects the Spanish inquisition!", "img/nesi.png");
    redirect("/");
}

if (isset($_SESSION["toasts"])) {
    $toasts = $_SESSION["toasts"];
}

?>