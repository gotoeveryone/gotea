<?php
/**
 * ページネーション
 *
 * @var \Gotea\View\AppView $this ビューオブジェクト
 */
?>
<div class="pagination">
    <ul class="pagination-item">
        <li class="result-count"><?= __('{0}件のレコードが該当しました。', $this->Paginator->param('count')) ?></li>
    </ul>
    <ul class="pagination-item pager">
        <?php if ($this->Paginator->hasPage(2)) : ?>
            <?= $this->Paginator->prev('<', compact('url')) ?>
        <?php endif ?>
        <?= $this->Paginator->numbers(compact('url')) ?>
        <?php if ($this->Paginator->hasPage(2)) : ?>
            <?= $this->Paginator->next('>', compact('url')) ?>
        <?php endif ?>
    </ul>
</div>
