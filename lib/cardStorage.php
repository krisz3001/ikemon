<?php if (!defined("GUARD")) die("Directly not accessible!"); ?>

<?php 

    include_once("lib/storage.php");

    class CardStorage extends Storage {

        const LIMIT = 5;

        const TYPES = ["normal", "fire", "water", "electric", "grass", "ice", "fighting", "poison", "ground", "psychic", "bug", "rock", "ghost", "dark", "steel"];

        public function __construct() {
            parent::__construct(new JsonIO("storage/cards.json"));
        }

        public function transfer($id, $new_owner) {
            $card = $this->contents[$id];
            if ($card) {
                $card["owner"] = $new_owner;
                $this->update($id, $card);
            }
        }

        public function findByOwner($name) {
            return $this->findMany(function ($e) use ($name) {
                if ($e["owner"] === $name) {
                    return true;
                }
                return false;
            });
        }

        public function get_filtered_by_type($type) {
            if ($type === "all") return $this->contents;
            return $this->findMany(function ($e) use ($type) {
                if ($e["type"] === $type) {
                    return true;
                }
                return false;
            });
        }

        public function get_nth_nine($n, $type) {
            if ($type === "all") return array_slice($this->contents, $n*9, 9);
            return array_slice($this->get_filtered_by_type($type), $n*9, 9);
        }

        public function get_page_count($type) {
            if ($type === "all") return ceil(count($this->contents)/9);
            return ceil(count($this->get_filtered_by_type($type))/9);
        }

        public function exists($name) {
            $card = $this->findMany(function ($e) use ($name) {
                if ($e["name"] === $name) {
                    return true;
                }
                return false;
            });
            return count($card) != 0;
        }

        public function hasSpace($username) {
            return count($this->findByOwner($username)) < self::LIMIT;
        }

        public function addCard($name, $type, $hp, $attack, $defense, $price, $description, $image) {
            $card = [
                "name" => $name,
                "type" => $type,
                "hp" => $hp,
                "attack" => $attack,
                "defense" => $defense,
                "price" => $price,
                "description" => $description,
                "image" => $image,
                "owner" => "admin"
            ];
            $this->add($card);
        }

        public function lock($cardid) {
            $card = $this->contents[$cardid];
            $card["locked"] = true;
            $this->update($cardid, $card);
        }

        public function unlock($card_id) {
            $card = $this->contents[$card_id];
            $card["locked"] = false;
            $this->update($card_id, $card);
        }

        public function isLocked($card) {
            return isset($card["locked"]) && $card["locked"];
        }

        public function updateCard($id, $name, $type, $hp, $attack, $defense, $price, $description, $image, $owner) {
            $card = [
                "name" => $name,
                "type" => $type,
                "hp" => $hp,
                "attack" => $attack,
                "defense" => $defense,
                "price" => $price,
                "description" => $description,
                "image" => $image,
                "owner" => $owner,
                "id" => $id
            ];
            $this->update($id, $card);
        }

        public function checkData($post, &$errors, $auth) {
            if (!isset($post["name"]) || trim($post["name"]) === "") {
              $errors[] = "Name is not set";
            }
            else if ($this->exists($post["name"]) && !isset($post["edit"])) {
                $errors[] = "Card exists";
            }
            if (!isset($post["type"]) || trim($post["type"]) === "") {
                $errors[] = "Type is not set";
            }
            if (!isset($post["hp"]) || trim($post["hp"]) === "") {
                $errors[] = "HP is not set";
            }
            else if (!is_numeric($post["hp"])) {
                $errors[] = "HP must be a number";
            }
            else if ($post["hp"] < 0) {
                $errors[] = "HP can not be negative";
            }
            if (!isset($post["attack"]) || trim($post["attack"]) === "") {
                $errors[] = "Attack is not set";
            }
            else if (!is_numeric($post["attack"])) {
                $errors[] = "Attack must be a number";
            }
            else if ($post["attack"] < 0) {
                $errors[] = "Attack can not be negative";
            }
            if (!isset($post["defense"]) || trim($post["defense"]) === "") {
                $errors[] = "Defense is not set";
            }
            else if (!is_numeric($post["defense"])) {
                $errors[] = "Defense must be a number";
            }
            else if ($post["defense"] < 0) {
                $errors[] = "Defense can not be negative";
            }
            if (!isset($post["price"]) || trim($post["price"]) === "") {
                $errors[] = "Price is not set";
            }
            else if (!is_numeric($post["price"])) {
                $errors[] = "Price must be a number";
            }
            else if ($post["price"] < 0) {
                $errors[] = "Price can not be negative";
            }
            if (!isset($post["description"]) || trim($post["description"]) === "") {
                $errors[] = "Description is not set";
            }
            if (!isset($post["image"]) || trim($post["image"]) === "") {
                $errors[] = "Image is not set";
            }
            if (isset($post["edit"]) && (!isset($post["owner"]) || trim($post["owner"]) === "")) {
                $errors[] = "Owner is not set";
            }
            else if (isset($post["edit"]) && !$auth->user_exists($post["owner"])) {
                $errors[] = "Invalid owner";
            }
          }
    }

?>