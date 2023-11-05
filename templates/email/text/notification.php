段位差分がありました。<?= PHP_EOL ?>
<?= PHP_EOL ?>
<?php foreach ($messages as $key => $values) : ?>
<?php if (count($values) > 0) : ?>
<?= "【{$key}】" ?><?= PHP_EOL ?>
<?php foreach ($values as $value) : ?>
    <?= $value ?><?= PHP_EOL ?>
<?php endforeach ?>
<?php endif ?>
<?= PHP_EOL ?>
<?php endforeach ?>
