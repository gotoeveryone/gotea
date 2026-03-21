<?php
/**
 * @var \App\View\AppView $this
 * @var string $code
 * @var string $message
 * @var string $url
 */
use Cake\Core\Configure;

if (Configure::read('debug')) :
    $this->setLayout('dev_error');

    $this->assign('title', $message);
    $this->assign('templateName', 'error400.php');

    $this->start('file');
    echo $this->element('auto_table_warning');
    $this->end();
endif;
?>
<p><strong><?= __d('cake', 'ステータスコード：' . $code) ?></strong></p>
<p><strong><?= __d('cake', '対象URL：' . $url) ?></strong></p>
<p><strong><?= __d('cake', '詳細：' . $message) ?></strong></p>
