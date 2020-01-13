<?php
use Cake\Core\Configure;
use Cake\Error\Debugger;

$this->assign('title', '内部サーバーエラー発生');
if (Configure::read('debug')):
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error500.ctp');

    $this->start('file');
?>
<?php if (!empty($error->queryString)) : ?>
    <p class="notice">
        <strong>SQL Query: </strong>
        <?= h($error->queryString) ?>
    </p>
<?php endif; ?>
<?php if (!empty($error->params)) : ?>
        <strong>SQL Query Params: </strong>
        <?= Debugger::dump($error->params) ?>
<?php endif; ?>
<?php
    echo $this->element('auto_table_warning');

    if (extension_loaded('xdebug')):
        xdebug_print_function_stack();
    endif;

    $this->end();
else:
    $this->layout = 'default';
endif;
?>
<p><strong><?= __d('cake', 'ステータスコード：'.$code) ?></strong></p>
<p><strong><?= __d('cake', '対象URL：'.$url) ?></strong></p>
<p><strong><?= __d('cake', '詳細：'.$message) ?></strong></p>
