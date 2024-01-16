<?php if (!defined("GUARD")) die("Directly not accessible!"); ?>

<?php if ($auth->is_authenticated() && $auth->is_admin()) : ?>
    <div class="modal <?php if (isset($_SESSION["newcard"])) echo "active" ?>" id="modal-new-card">
        <form class="modal-form" method="POST" action="" novalidate>
            <?php if (isset($_SESSION["newcard"]) && isset($_SESSION["newcard"]["errors"])) : ?>
                <ul class="errors">
                    <?php foreach ($_SESSION["newcard"]["errors"] as $value) : ?>
                        <li><?= $value ?></li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
            <label for="new-card-name">Name</label>
            <input type="text" name="name" id="new-card-name" value="<?= isset($_SESSION["newcard"]) ? $_SESSION["newcard"]["name"] ?? "" : ""  ?>">
            
            <label for="new-card-type">Type</label>
            <select name="type" id="new-card-type">
                <?php foreach ($cardStorage::TYPES as $type) : ?>
                    <option value="<?= $type ?>" <?php if (isset($_SESSION["newcard"]) && isset($_SESSION["newcard"]["type"]) && $_SESSION["newcard"]["type"] === $type) echo "selected" ?>><?= $type ?></option>
                <?php endforeach ?>
            </select>
                
            <label for="new-card-hp">Attributes</label>
            <span id="attributes">
                <label class="icon" for="new-card-hp">❤</label><input type="number" name="hp" id="new-card-hp" class="input-attribute" min="0" value="<?= isset($_SESSION["newcard"]) ? $_SESSION["newcard"]["hp"] ?? "" : "" ?>">
                <label class="icon" for="new-card-attack">⚔</label><input type="number" name="attack" id="new-card-attack" class="input-attribute" min="0" value="<?= isset($_SESSION["newcard"]) ? $_SESSION["newcard"]["attack"] ?? "" : "" ?>">
                <label class="icon" for="new-card-defense">🛡</label><input type="number" name="defense" id="new-card-defense" class="input-attribute" min="0" value="<?= isset($_SESSION["newcard"]) ? $_SESSION["newcard"]["defense"] ?? "" : "" ?>">
            </span>
            
            <label for="new-card-price">Price</label>
            <input type="number" name="price" id="new-card-price" value="<?= isset($_SESSION["newcard"]) ? $_SESSION["newcard"]["price"] ?? "" : "" ?>" min="0">

            <label for="new-card-description">Description</label>
            <input type="text" name="description" id="new-card-description" value="<?= isset($_SESSION["newcard"]) ? $_SESSION["newcard"]["description"] ?? "" : "" ?>">
            
            <label for="new-card-image">Image link</label>
            <input type="text" name="image" id="new-card-image" value="<?= isset($_SESSION["newcard"]) ? $_SESSION["newcard"]["image"] ?? "" : "" ?>">
            <input type="hidden" name="newcard">
            <div class="buttons">
                <button class="btn" id="btn-add-card">Add</button>
                <button class="btn" id="btn-add-close">Close</button>
                <?php unset($_SESSION["newcard"]) ?>
            </div>
        </form>
    </div>
<?php endif ?>