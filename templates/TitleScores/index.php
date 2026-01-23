<?php
/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\TitleScore[]|\Cake\Collection\CollectionInterface $titleScores
 */
?>
<section class="title-scores">
    <div>
        <?= $this->Form->create($form, ['class' => 'main-form', 'type' => 'get', 'url' => ['_name' => 'find_scores']]) ?>
        <ul class="search-header">
            <li class="search-row">
                <div class="search-box">
                    <?= $this->Form->control('name1', [
                        'label' => ['text' => '棋士名1'],
                        'class' => 'name',
                        'value' => $this->getRequest()->getQuery('name1'),
                    ]) ?>
                </div>
                <div class="search-box">
                    <?= $this->Form->control('name2', [
                        'label' => ['text' => '棋士名2'],
                        'class' => 'name',
                        'value' => $this->getRequest()->getQuery('name2'),
                    ]) ?>
                </div>
                <div class="search-box">
                    <?= $this->Form->control('title_name', [
                        'label' => ['text' => 'タイトル名'],
                        'class' => 'title_name',
                        'value' => $this->getRequest()->getQuery('title_name'),
                    ]) ?>
                </div>
            </li>
            <li class="search-row">
                <div class="search-box">
                    <?= $this->cell('Countries', [
                        'hasTitleOnly' => false,
                        'attributes' => [
                            'label' => ['text' => '棋戦分類'],
                            'value' => $this->getRequest()->getQuery('country_id'),
                        ],
                    ]) ?>
                </div>
                <div class="search-box">
                    <?= $this->Form->years('target_year', [
                        'label' => ['text' => '対局年'],
                        'class' => 'year',
                        'empty' => true,
                        'value' => $this->getRequest()->getQuery('target_year'),
                    ]) ?>
                </div>
                <div class="search-box">
                    <?php
                    echo $this->Form->label('started', '対局日', ['class' => 'search-box_label']);
                    echo $this->Form->text('started', [
                        'class' => 'started datepicker',
                        'autocomplete' => 'off',
                        'value' => $this->getRequest()->getQuery('started'),
                    ]);
                    echo $this->Form->label('ended', '～');
                    echo $this->Form->text('ended', [
                        'class' => 'ended datepicker',
                        'autocomplete' => 'off',
                        'value' => $this->getRequest()->getQuery('ended'),
                    ]);
                    ?>
                </div>
                <div class="search-box search-box-right">
                    <?php if ($this->isAdmin()) : ?>
                    <a class="layout-button button-secondary" @click="openModal('<?= $this->Url->build(['_name' => 'upload_scores']) ?>')">
                        <?= __('Upload') ?>
                    </a>
                    <?php endif ?>
                    <?= $this->Form->button('検索', ['type' => 'submit', 'class' => 'button button-primary']) ?>
                </div>
            </li>
        </ul>
        <?= $this->Form->end() ?>
    </div>

    <?php if (!empty($titleScores)) : ?>
        <?= $this->element('Paginator/default', ['url' => ['_name' => 'find_scores']]) ?>
    <?php endif ?>

    <div class="search-results">
        <?= $this->element('TitleScores/list') ?>
    </div>
</section>
