<?php
/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\RetentionHistory $retention
 */
?>
<li class="retention-row">
    <div class="detail_box_item box-2">
        <?php
            $label = '第' . $retention->holding . '期(' . $retention->target_year . '年)';
        ?>
        <span class="inner-column"><?= h($label) ?></span>
        <?php if ($retention->isRecent()) : ?>
            <span><span class="mark-new">NEW!</span></span>
        <?php endif ?>
    </div>
    <div class="detail_box_item box-1">
        <?= !$retention->is_official ? '非公式戦' : '公式戦' ?>
    </div>
    <div class="detail_box_item box-2">
        <span class="inner-column">
            <?= $this->Date->format($retention->acquired, 'yyyy年M月d日') . '優勝' ?>
            <?php if ($retention->broadcasted) : ?>
                <br/><?= '(' . $this->Date->format($retention->broadcasted, 'yyyy年M月d日') . '放映)' ?>
            <?php endif ?>
        </span>
    </div>
    <div class="detail_box_item box-5">
        <?php
            $winner = $title->country->isWorlds() && !$retention->is_team
                ? $retention->country->name
                : $retention->winner_name . '(' . $retention->country->name . ')';
        ?>
        <span class="inner-column"><?= h($winner) ?></span>
    </div>
    <?php if ($isAdmin) : ?>
    <div class="detail_box_item detail_box_item-buttons box-2">
        <button
            type="button"
            class="button button-secondary"
            value="edit"
            @click="select('<?= $retention->id ?>')"
        >編集</button>
    </div>
    <?php endif ?>
</li>
