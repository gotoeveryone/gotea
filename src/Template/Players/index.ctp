<section class="players">
    <?=$this->Form->create($form, [
        'id' => 'mainForm',
        'type' => 'post',
        'url' => ['action' => 'search'],
        'templates' => [
            'inputContainer' => '{{content}}',
            'textFormGroup' => '{{input}}',
            'selectFormGroup' => '{{input}}'
        ]
    ])?>
        <ul class="search-header">
            <li class="search-row">
                <label>所属国：</label>
                <?=
                    $this->Form->select('country_id', $countries, [
                        'data-id' => 'country',
                        'class' => 'country',
                        'empty' => true
                    ]);
                ?>
                <label>所属組織：</label>
                <?=
                    $this->Form->select('organization_id', $organizations, [
                        'data-id' => 'organization',
                        'class' => 'organization',
                        'empty' => true
                    ]);
                ?>
                <label>段位：</label>
                <?=
                    $this->Form->select('rank_id', $ranks, [
                        'data-id' => 'rank',
                        'class' => 'rank',
                        'empty' => true
                    ]);
                ?>
                <label>性別：</label>
                <?=
                    $this->Form->select('sex', [
                        '男性' => '男性',
                        '女性' => '女性'
                    ], [
                        'class' => 'sex',
                        'empty' => true
                    ]);
                ?>
                <label>引退者：</label>
                <?=
                    $this->Form->select('is_retired', [
                        '0' => '検索しない',
                        '1' => '検索する'
                    ], [
                        'class' => 'excluded'
                    ]);
                ?>
            </li>
            <li class="search-row">
                <label>棋士名：</label>
                <?=$this->Form->text('name', ['class' => 'name', 'maxlength' => 20])?>
                <label>（英語）：</label>
                <?=$this->Form->text('name_english', ['class' => 'name', 'maxlength' => 40]);?>
                <label>（その他）：</label>
                <?=$this->Form->text('name_other', ['class' => 'name', 'maxlength' => 20]);?>
                <label>入段年：</label>
                <?=$this->Form->text('joined_from', ['class' => 'joined', 'maxlength' => 4])?>
                ～
                <?=$this->Form->text('joined_to', ['class' => 'joined', 'maxlength' => 4])?>
            </li>
            <li class="search-row">
                <?php if (!empty($players) && count($players) > 0) { ?>
                <div class="result-count">
                    <?=count($players).'件のレコードが該当しました。'?>
                </div>
                <?php } ?>
                <div class="button-wrap">
                    <?=
                        $this->Form->button('新規作成', [
                            'class' => 'add-new',
                            'type' => 'button',
                            'disabled' => 'disabled'
                        ]);
                    ?>
                    <?=$this->Form->button('検索', ['type' => 'submit'])?>
                </div>
            </li>
        </ul>

        <div class="search-results">
            <ul class="players table-header">
                <li class="table-row">
                    <span class="id">ID</span>
                    <span class="name">棋士名</span>
                    <span class="name">棋士名（英語）</span>
                    <span class="enrollment">入段日</span>
                    <span class="country">所属国</span>
                    <span class="organization">所属組織</span>
                    <span class="rank">段位</span>
                    <span class="sex">性別</span>
                    <span class="score">
                        <?=date('Y')?>年国内<br/>
                        <span class="point">勝</span>
                        <span class="point">敗</span>
                        <span class="point">分</span>
                    </span>
                    <span class="score">
                        <?=date('Y')?>年国際<br/>
                        <span class="point">勝</span>
                        <span class="point">敗</span>
                        <span class="point">分</span>
                    </span>
                </li>
            </ul>
            <?php if (!empty($players) && count($players) > 0) : ?>
            <ul class="players table-body">
                <?php foreach ($players as $player) : ?>
                <?php
                    $class = '';
                    if ($player->is_retired) {
                        $class .= 'excluded-row';
                    }
                    if ($player->sex === '女性') {
                        if ($class !== '') {
                            $class .= ' ';
                        }
                        $class .= 'female';
                    }
                ?>
                <li class="table-row<?= ($class ? ' '.$class : ''); ?>">
                    <span class="id">
                        <?=h($player->id)?>
                    </span>
                    <span class="name">
                        <?php
                            $setClass = ($player->sex === '女性' ? 'female' : 'blue');
                            echo $this->Html->link($player->name, [
                                'action' => 'detail/'.$player->id
                            ], [
                                'class' => $setClass.' colorbox'
                            ]);
                        ?>
                    </span>
                    <span class="name">
                        <?=h($player->name_english); ?>
                    </span>
                    <span class="enrollment">
                        <?=$this->Date->formatJoinDelimiterValue($player->joined, '/'); ?>
                    </span>
                    <span class="country">
                        <?=h($player->country->name); ?>
                    </span>
                    <span class="organization">
                        <?=h($player->organization->name); ?>
                    </span>
                    <span class="rank">
                        <?=h($player->rank->name); ?>
                    </span>
                    <span class="sex">
                        <?=h($player->sex); ?>
                    </span>
                    <span class="point">
                        <?=h((empty($player->player_scores) ? '-' : $player->player_scores[0]->win_point)); ?>
                    </span>
                    <span class="point">
                        <?=h((empty($player->player_scores) ? '-' : $player->player_scores[0]->lose_point)); ?>
                    </span>
                    <span class="point">
                        <?=h((empty($player->player_scores) ? '-' : $player->player_scores[0]->draw_point)); ?>
                    </span>
                    <span class="point">
                        <?=h((empty($player->player_scores) ? '-' : $player->player_scores[0]->win_point_world)); ?>
                    </span>
                    <span class="point">
                        <?=h((empty($player->player_scores) ? '-' : $player->player_scores[0]->lose_point_world)); ?>
                    </span>
                    <span class="point">
                        <?=h((empty($player->player_scores) ? '-' : $player->player_scores[0]->draw_point_world)); ?>
                    </span>
                </li>
                <?php endforeach ?>
            <?php endif ?>
            </ul>
        </div>
    <?=$this->Form->end()?>
</section>

<?php $this->MyHtml->scriptStart(['inline' => false, 'block' => 'script']); ?>
<script>
    var country = document.querySelector('[data-id=country]');
    var addNew = document.querySelector('.add-new');

    // 初期状態
    addNew.disabled = !country.value;

    // 新規作成画面へ遷移
    addNew.addEventListener('click', function() {
        setColorbox("<?=$this->Url->build(['action' => 'detail'])?>?countryId=" + country.value);
    }, false);

    // 所属国変更時
    country.addEventListener('change', function() {
        addNew.disabled = !this.value;
    }, false);
</script>
<?php $this->MyHtml->scriptEnd(); ?>
