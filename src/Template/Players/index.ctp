<article class="players">
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
        <section class="search-header">
            <section class="row">
                <section class="label">所属国：</section>
                <section>
                    <?=
                        $this->Form->input('country_id', [
                            'data-id' => 'country',
                            'options' => $countries,
                            'class' => 'country',
                            'empty' => true
                        ]);
                    ?>
                </section>
                <section class="label">所属組織：</section>
                <section>
                    <?=
                        $this->Form->input('organization_id', [
                            'data-id' => 'organization',
                            'options' => $organizations,
                            'class' => 'organization',
                            'empty' => true
                        ]);
                    ?>
                </section>
                <section class="label">段位：</section>
                <section>
                    <?=
                        $this->Form->input('rank_id', [
                            'data-id' => 'rank',
                            'options' => $ranks,
                            'class' => 'rank',
                            'empty' => true
                        ]);
                    ?>
                </section>
                <section class="label">性別：</section>
                <section>
                    <?=
                        $this->Form->input('sex', [
                            'options' => [
                                '男性' => '男性',
                                '女性' => '女性'
                            ],
                            'class' => 'sex',
                            'empty' => true
                        ]);
                    ?>
                </section>
                <section class="label">引退者：</section>
                <section>
                    <?=
                        $this->Form->input('is_retired', [
                            'options' => [
                                '0' => '検索しない',
                                '1' => '検索する'
                            ],
                            'class' => 'retired'
                        ]);
                    ?>
                </section>
                <section class="button-column">
                    <?php if (!empty($players) && count($players) > 0) { ?>
                        <span class="left red">
                            <?=count($players).'件のレコードが該当しました。'?>
                        </span>
                    <?php } ?>
                </section>
            </section>
            <section class="row">
                <section class="label">棋士名：</section>
                <section>
                    <?=$this->Form->text('name', ['class' => 'name'])?>
                </section>
                <section class="label">（英語）：</section>
                <section>
                    <?=$this->Form->text('name_english', ['class' => 'name']);?>
                </section>
                <section class="label">入段年：</section>
                <section>
                    <?=
                        $this->Form->text('joined_from', [
                            'class' => 'enrollment imeDisabled',
                            'maxlength' => 4,
                            'min' => 1,
                            'max' => 9999
                        ]);
                    ?>
                    ～
                    <?=
                        $this->Form->text('joined_to', [
                            'class' => 'enrollment imeDisabled',
                            'maxlength' => 4,
                            'min' => 1,
                            'max' => 9999
                        ]);
                    ?>
                </section>
                <section class="button-column">
                    <?=
                        $this->Form->button('新規作成', [
                            'id' => 'addNew',
                            'type' => 'button',
                            'disabled' => 'disabled'
                        ]);
                    ?>
                    <?=$this->Form->button('検索', ['type' => 'submit'])?>
                </section>
            </section>
        </section>

        <section class="search-results">
            <table class="players">
                <thead>
                    <tr>
                        <th rowspan="2" class="playerId">ID</th>
                        <th rowspan="2" class="playerName">棋士名</th>
                        <th rowspan="2" class="playerNameEn">棋士名（英語）</th>
                        <th rowspan="2" class="enrollment">入段日</th>
                        <th rowspan="2" class="country">所属国</th>
                        <th rowspan="2" class="organization">所属組織</th>
                        <th rowspan="2" class="rank">段位</th>
                        <th rowspan="2" class="sex">性別</th>
                        <th colspan="3" class="score"><?php echo date('Y')?>年国内成績</th>
                        <th colspan="3" class="score"><?php echo date('Y')?>年国際成績</th>
                    </tr>
                    <tr>
                        <th class="scorePoint">勝</th>
                        <th class="scorePoint">敗</th>
                        <th class="scorePoint">分</th>
                        <th class="scorePoint">勝</th>
                        <th class="scorePoint">敗</th>
                        <th class="scorePoint">分</th>
                    </tr>
                </thead>
                <?php if (!empty($players) && count($players) > 0) : ?>
                <tbody>
                    <?php foreach ($players as $player) : ?>
                    <?php
                        $class = '';
                        if ($player->is_retired) {
                            $class .= 'retired';
                        }
                        if ($player->sex === '女性') {
                            if ($class !== '') {
                                $class .= ' ';
                            }
                            $class .= 'female';
                        }
                        if ($class !== '') {
                            $class = ' class="'.$class.'"';
                        }
                    ?>
                    <tr<?php echo $class ?>>
                        <td class="center playerId">
                            <?=h($player->id)?>
                        </td>
                        <td class="left playerName">
                            <?php
                                $setClass = ($player->sex === '女性' ? 'female' : 'blue');
                                echo $this->Html->link($player->name, [
                                    'action' => 'detail/'.$player->id
                                ], [
                                    'class' => $setClass.' colorbox'
                                ]);
                            ?>
                        </td>
                        <td class="left playerNameEn">
                            <?=h($player->name_english); ?>
                        </td>
                        <td class="center enrollment">
                            <?=$this->Date->formatJoinDelimiterValue($player->joined, '/'); ?>
                        </td>
                        <td class="center country">
                            <?=h($player->country->name); ?>
                        </td>
                        <td class="center organization">
                            <?=h($player->organization->name); ?>
                        </td>
                        <td class="center rank">
                            <?=h($player->rank->name); ?>
                        </td>
                        <td class="center sex">
                            <?=h($player->sex); ?>
                        </td>
                        <td class="center scorePoint">
                            <?=h((empty($player->player_scores) ? '-' : $player->player_scores[0]->win_point)); ?>
                        </td>
                        <td class="center scorePoint">
                            <?=h((empty($player->player_scores) ? '-' : $player->player_scores[0]->lose_point)); ?>
                        </td>
                        <td class="center scorePoint">
                            <?=h((empty($player->player_scores) ? '-' : $player->player_scores[0]->draw_point)); ?>
                        </td>
                        <td class="center scorePoint">
                            <?=h((empty($player->player_scores) ? '-' : $player->player_scores[0]->win_point_world)); ?>
                        </td>
                        <td class="center scorePoint">
                            <?=h((empty($player->player_scores) ? '-' : $player->player_scores[0]->lose_point_world)); ?>
                        </td>
                        <td class="center scorePoint">
                            <?=h((empty($player->player_scores) ? '-' : $player->player_scores[0]->draw_point_world)); ?>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
                <?php endif ?>
            </table>
        </section>
    <?=$this->Form->end()?>
</article>

<?php $this->MyHtml->scriptStart(['inline' => false, 'block' => 'script']); ?>
<script>
    $(function () {
        $('#addNew').attr('disabled', !$('#searchCountry').val());
        // 国プルダウン変更時
        $('select[name=searchCountry]').change(function () {
            $('#addNew').attr('disabled', !$(this).val());
        });
        // 新規作成画面へ遷移
        $('#addNew').click(function () {
            setColorbox("<?=$this->Url->build(['action' => 'detail'])?>?countryId=" + $('#searchCountry').val());
        });
    });
</script>
<?php $this->MyHtml->scriptEnd(); ?>
