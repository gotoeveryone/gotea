段位差分がありました。<?= PHP_EOL ?>
<?= PHP_EOL ?>
<?php foreach ($messages as $key => $values) : ?>
<?= "【${key}】" ?><?= PHP_EOL ?>
<?php if (is_array($values)) : ?>
<?php foreach ($values as $value) : ?>
   <?= $value ?><?= PHP_EOL ?>
<?php endforeach ?>
<?php else : ?>
<?= $values ?><?= PHP_EOL ?>
<?php endif ?>
<?= PHP_EOL ?>
<?php endforeach ?>
