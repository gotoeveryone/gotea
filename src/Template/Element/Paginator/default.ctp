<?php
/**
 * ページネーション
 */
?>
<div class="pagination">
    <ul class="pagination-item">
        <li class="result-count"><?= __('{0}件のレコードが該当しました。', $this->Paginator->param('count')) ?></li>
    </ul>
    <ul class="pagination-item pagination-pager">
        <?= $this->Paginator->prev('<', compact('url')) ?>
        <?= $this->Paginator->numbers(compact('url')) ?>
        <?= $this->Paginator->next('>', compact('url')) ?>
    </ul>
</div>
