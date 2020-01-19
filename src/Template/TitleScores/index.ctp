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
                <fieldset class="search-box">
                    <?= $this->Form->control('name', [
                        'label' => ['text' => '棋士名'],
                        'class' => 'name',
                        'value' => $this->getRequest()->getQuery('name'),
                    ]) ?>
                </fieldset>
                <fieldset class="search-box">
                    <?= $this->Form->control('title_name', [
                        'label' => ['text' => 'タイトル名'],
                        'class' => 'title_name',
                        'value' => $this->getRequest()->getQuery('title_name'),
                    ]) ?>
                </fieldset>
            </li>
            <li class="search-row">
                <fieldset class="search-box">
                    <?= $this->cell('Countries', [
                        'hasTitleOnly' => false,
                        [
                            'label' => ['text' => '棋戦分類'],
                            'value' => $this->getRequest()->getQuery('country_id'),
                        ],
                    ]) ?>
                </fieldset>
                <fieldset class="search-box">
                    <?= $this->Form->years('target_year', [
                        'label' => ['text' => '対局年'],
                        'class' => 'year',
                        'empty' => true,
                        'value' => $this->getRequest()->getQuery('target_year'),
                    ]) ?>
                </fieldset>
                <fieldset class="search-box">
                    <?php
                    echo $this->Form->label('started', '対局日', ['class' => 'search-box_label']);
                    echo $this->Form->text('started', [
                        'class' => 'started datepicker',
                        'value' => $this->getRequest()->getQuery('started'),
                    ]);
                    echo $this->form->label('ended', '～');
                    echo $this->Form->text('ended', [
                        'class' => 'ended datepicker',
                        'value' => $this->getRequest()->getQuery('ended'),
                    ]);
                    ?>
                </fieldset>
                <fieldset class="search-box search-box-right">
                    <?= $this->Form->button('検索', ['type' => 'submit', 'class' => 'button button-primary']) ?>
                </fieldset>
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
