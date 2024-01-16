<?php if (!defined("GUARD")) die("Directly not accessible!"); ?>

<?php if ($auth->is_authenticated() && $auth->is_admin()) : ?>
    <div class="modal <?php if (isset($_SESSION["edit"])) echo "active" ?>" id="modal-edit">
        <form class="modal-form" method="POST" action="" novalidate>
            <?php if (isset($_SESSION["edit"]) && isset($_SESSION["edit"]["errors"])) : ?>
                <ul class="errors" id="edit-errors">
                    <?php foreach ($_SESSION["edit"]["errors"] as $value) : ?>
                        <li><?= $value ?></li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
            <label for="edit-name">Name</label>
            <input type="text" name="name" id="edit-name" value="<?= isset($_SESSION["edit"]) ? $_SESSION["edit"]["name"] ?? "" : ""  ?>">
            
            <label for="edit-type">Type</label>
            <select name="type" id="edit-type">
                <?php foreach ($cardStorage::TYPES as $type) : ?>
                    <option value="<?= $type ?>" <?php if (isset($_SESSION["edit"]) && isset($_SESSION["edit"]["type"]) && $_SESSION["edit"]["type"] === $type) echo "selected" ?>><?= $type ?></option>
                <?php endforeach ?>
            </select>
                
            <label for="edit-hp">Attributes</label>
            <span id="attributes">
                <label class="icon" for="edit-hp">❤</label><input type="number" name="hp" id="edit-hp" class="input-attribute" min="0" value="<?= isset($_SESSION["edit"]) ? $_SESSION["edit"]["hp"] ?? "" : "" ?>">
                <label class="icon" for="edit-attack">⚔</label><input type="number" name="attack" id="edit-attack" class="input-attribute" min="0" value="<?= isset($_SESSION["edit"]) ? $_SESSION["edit"]["attack"] ?? "" : "" ?>">
                <label class="icon" for="edit-defense">🛡</label><input type="number" name="defense" id="edit-defense" class="input-attribute" min="0" value="<?= isset($_SESSION["edit"]) ? $_SESSION["edit"]["defense"] ?? "" : "" ?>">
            </span>
            
            <label for="edit-price">Price</label>
            <input type="number" name="price" id="edit-price" value="<?= isset($_SESSION["edit"]) ? $_SESSION["edit"]["price"] ?? "" : "" ?>" min="0">

            <label for="edit-description">Description</label>
            <input type="text" name="description" id="edit-description" value="<?= isset($_SESSION["edit"]) ? $_SESSION["edit"]["description"] ?? "" : "" ?>">
            
            <label for="edit-image">Image link</label>
            <input type="text" name="image" id="edit-image" value="<?= isset($_SESSION["edit"]) ? $_SESSION["edit"]["image"] ?? "" : "" ?>">

            <label for="edit-owner">Owner</label>
            <input type="text" name="owner" id="edit-owner" value="<?= isset($_SESSION["edit"]) ? $_SESSION["edit"]["owner"] ?? "" : "" ?>">
            <input type="hidden" name="id" id="edit-id" value="<?= isset($_SESSION["edit"]) ? $_SESSION["edit"]["id"] ?? "" : "" ?>">
            <input type="hidden" name="edit">
            <div class="buttons">
                <button class="btn" id="btn-edit-card">Edit</button>
                <button class="btn" id="btn-edit-close">Close</button>
                <?php unset($_SESSION["edit"]) ?>
            </div>
        </form>
    </div>
<?php endif ?>