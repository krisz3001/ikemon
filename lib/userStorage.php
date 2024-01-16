<?php if (!defined("GUARD")) die("Directly not accessible!"); ?>

<?php 

    include_once("lib/storage.php");

    class UserStorage extends Storage {

        public function __construct() {
            parent::__construct(new JsonIO("storage/users.json"));
        }

        public function canAfford($userid, $cost) {
            return $this->contents[$userid]["money"] >= $cost;
        }

        public function spendMoney($userid, $money) {
            $this->contents[$userid]["money"] -= $money;
            $this->contents[$userid]["money"] = round($this->contents[$userid]["money"], 2);
        }

        public function addMoney($userid, $money) {
            $this->contents[$userid]["money"] += $money;
            $this->contents[$userid]["money"] = round($this->contents[$userid]["money"], 2);
        }

        public function updateMoney($session) {
            return $this->contents[$session["user"]["id"]]["money"];
        }

        public function findByName($name) {
            $user = $this->findMany(function ($e) use ($name) {
                if ($e["username"] === $name) {
                    return true;
                }
                return false;
            });
            return array_shift($user);
        }

        // From the perspective of the initiator
        public function addRequest($from_id, $to_id, $give, $get, $pay, $receive){
            $request_id = uniqid();
            $request = [
                "from" => $from_id,
                "to" => $to_id,
                "give" => $give,
                "get" => $get,
                "pay" => $pay,
                "receive" => $receive
            ];
            $recipient = $this->contents[$to_id];
            $recipient["requests"][$request_id] = $request;
            $this->update($to_id, $recipient);
        }

        public function removeRequest($user_id, $request_id) {
            unset($this->contents[$user_id]["requests"][$request_id]);
            $this->update($user_id, $this->contents[$user_id]);
        }

        public function getRequests($user_id) {
            return $this->contents[$user_id]["requests"];
        }
    }

?>