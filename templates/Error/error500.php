<?php
use Cake\Core\Configure;
use Cake\Error\Debugger;

$this->assign('title', '内部サーバーエラー発生');
if (Configure::read('debug')):
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error500.php');

    $this->start('file');
?>
<?php if ($error instanceof Error) : ?>
    <?php $file = $error->getFile() ?>
    <?php $line = $error->getLine() ?>
    <strong>Error in: </strong>
    <?= $this->Html->link(sprintf('%s, line %s', Debugger::trimPath($file), $line), Debugger::editorUrl($file, $line)); ?>
<?php endif; ?>
<?php
    echo $this->element('auto_table_warning');

    $this->end();
endif;
?>
<p><strong><?= __d('cake', 'ステータスコード：'.$code) ?></strong></p>
<p><strong><?= __d('cake', '対象URL：'.$url) ?></strong></p>
<p><strong><?= __d('cake', '詳細：'.$message) ?></strong></p>
