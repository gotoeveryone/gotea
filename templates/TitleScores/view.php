<?php
/**
 * @var \Gotea\View\AppView $this
 * @var int $id
 */
?>
<?= $this->Html->css('view', ['block' => true]) ?>
<title-score-detail-page
    :id="<?= $id ?>"
    csrf-token="<?= $this->request->getAttribute('csrfToken') ?>"
></title-score-detail-page>
