<?=$this->Form->create(null, [
    'id' => 'mainForm',
    'method' => 'post',
    'url' => ['action' => 'search'],
    'templates' => [
        'inputContainer' => '{{content}}',
        'textFormGroup' => '{{input}}',
        'selectFormGroup' => '{{input}}'
    ]
])?>
    <?=$this->Form->hidden('searchFlag', ['value' => (empty($searchFlag) ? '' : var_export($searchFlag, TRUE))])?>
    <?=$this->Form->hidden('dialogFlag', ['value' => (empty($dialogFlag) ? 'false' : var_export($dialogFlag, TRUE))])?>
    <table class="playersHeader">
        <tr>
            <td class="searchColumn1">所属国：</td>
            <td>
                <?=
                    $this->Form->input('searchCountry', [
                        'id' => 'searchCountry',
                        'options' => $countries,
                        'value' => h($this->request->data('searchCountry')),
                        'class' => 'country',
                        'empty' => true
                    ]);
                ?>
            </td>
            <td class="center">段位：</td>
            <td>
                <?=
                    $this->Form->input('searchRank', [
                        'id' => 'searchRank',
                        'options' => $ranks,
                        'value' => h($this->request->data('searchRank')),
                        'class' => 'rank',
                        'empty' => true
                    ]);
                ?>
            </td>
            <td class="center">性別：</td>
            <td>
                <?=
                    $this->Form->input('searchSex', [
                        'options' => [
                            '男性' => '男性',
                            '女性' => '女性'
                        ],
                        'value' => h($this->request->data('searchSex')),
                        'class' => 'sex',
                        'empty' => true
                    ]);
                ?>
            </td>
            <td class="searchColumn1">引退者：</td>
            <td>
                <?=
                    $this->Form->input('searchRetire', [
                        'options' => [
                            'false' => '検索しない',
                            'true' => '検索する'
                        ],
                        'value' => h($this->request->data('searchRetire')),
                        'class' => 'retired'
                    ]);
                ?>
            </td>
        </tr>
        <tr>
            <td class="searchColumn1">棋士名：</td>
            <td colspan="2">
                <?=
                    $this->Form->text('searchPlayerName', [
                        'value' => h($this->request->data('searchPlayerName')),
                        'class' => 'playerName'
                    ]);
                ?>
            </td>
            <td class="searchColumn1">（英語）：</td>
            <td colspan="2">
                <?=
                    $this->Form->text('searchPlayerNameEn', [
                        'value' => h($this->request->data('searchPlayerNameEn')),
                        'class' => 'playerName'
                    ]);
                ?>
            </td>
            <td class="searchColumn1">入段年：</td>
            <td>
                <?=
                    $this->Form->text('searchJoinedFrom', [
                        'value' => h($this->request->data('searchJoinedFrom')),
                        'class' => 'from imeDisabled',
                        'maxlength' => 4
                    ]);
                ?>
                ～
                <?=
                    $this->Form->text('searchJoinedTo', [
                        'value' => h($this->request->data('searchJoinedTo')),
                        'class' => 'to imeDisabled',
                        'maxlength' => 4
                    ]);
                ?>
            </td>
            <td colspan="4" class="right">
                <?=
                    $this->Form->button('新規作成', [
                        'id' => 'addNew',
                        'type' => 'button',
                        'disabled' => 'disabled'
                    ]);
                ?>
                <?=$this->Form->button('検索', ['type' => 'submit'])?>
            </td>
        </tr>
        <tr>
            <td colspan="8">
                <?php if (!empty($players) && count($players) > 0) { ?>
                    <span class="left red">
                        <?=count($players).'件のレコードが該当しました。'?>
                    </span>
                <?php } ?>
            </td>
        </tr>
    </table>

    <table class="searchView players">
        <thead>
            <tr>
                <th rowspan="2" class="playerId">ID</th>
                <th rowspan="2" class="playerName">棋士名</th>
                <th rowspan="2" class="playerNameEn">棋士名（英語）</th>
                <th rowspan="2" class="enrollment">入段日</th>
                <th rowspan="2" class="country">所属国</th>
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
                if ($player->DELETE_FLAG) {
                    $class .= 'retired';
                }
                if ($player->SEX === '女性') {
                    if ($class !== '') {
                        $class .= ' ';
                    }
                    $class .= 'red';
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
                        $setClass = ($player->sex === '女性' ? 'red' : 'blue');
                        if (isset($dialogFlag) && $dialogFlag) {
                            echo $this->Html->link($player->name, '#', [
                                'onClick' => 'selectParent("'.h($player->id).'",
                                    "'.h($player->name).'",
                                    "'.h($player->rank->id).'",
                                    "'.h($player->rank->name).'")',
                                'class' => $setClass
                            ]);
                        } else {
                            echo $this->Html->link($player->name, [
                                'action' => 'detail/'.h($player->id)
                            ], [
                                'class' => $setClass.' colorbox'
                            ]);
                        }
                    ?>
                </td>
                <td class="left playerNameEn">
                    <?=h($player->name_english); ?>
                </td>
                <td class="left enrollment">
                    <?=$this->Date->formatJoinDelimiterValue($player->joined, '/'); ?>
                </td>
                <td class="center country">
                    <?=h($player->country->name); ?>
                </td>
                <td class="center rank">
                    <?=h($player->rank->name); ?>
                </td>
                <td class="center sex">
                    <?=h($player->sex); ?>
                </td>
                <td class="center center scorePoint">
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
<?=$this->Form->end()?>
<script type="text/javascript">
    $(function () {
        $('.colorbox').colorbox({iframe: true, width: "50%", height: "95%"});
        $('#addNew').attr('disabled', !$('#searchCountry').val());
        // 国プルダウン変更時
        $('select[name=searchCountry]').change(function () {
            $('#addNew').attr('disabled', !$(this).val());
        });
        // 新規作成画面へ遷移
        $('#addNew').click(function () {
            $.colorbox({
                iframe: true,
                width: "90%",
                height: "90%",
                href: "<?=$this->Url->build(['action' => 'detail'])?>?countryId=" + $('#searchCountry').val()
            });
        });
    });

    /**
     * 選択された棋士IDをパラメータに設定して、親画面にパラメータを渡す
     * @param playerId
     * @param playerName
     * @param rank
     * @param rankText
     */
    function selectParent(playerId, playerName, rank, rankText) {
        // 親ウィンドウの項目に値を設定
        window.opener.$('#registPlayerId').val(playerId);
        window.opener.$('#registPlayerName').val(playerName);
        window.opener.$('#registRank').val(rank);
        window.opener.$('#registRankText').val(rankText);
        window.opener.$('.registPlayerName').html(playerName);
        window.opener.$('.registRankText').html(rankText);
        window.close();
        window.opener.focus();
    }
</script>
