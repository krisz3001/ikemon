<?php if (!defined("GUARD")) die("Directly not accessible!"); ?>

<h3 id="filter">
    <label for="filter-type">Filter by type</label>
    <select id="filter-type">
        <option value="all" selected>All</option>
        <?php foreach ($cardStorage::TYPES as $type) : ?>
            <option value="<?= $type ?>" <?= $type === ($_SESSION["pager"]["type"] ?? "all") ? "selected" : "" ?>><?= $type ?></option>
        <?php endforeach ?>
    </select>
</h3>