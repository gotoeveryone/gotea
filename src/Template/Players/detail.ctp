<div class="detail-dialog">
    <!-- タブ -->
    <ul id="tabs">
        <li class="tab" name="player">棋士情報</li>
        <?php if ($player->id) : ?>
            <li class="tab" name="scores">成績情報</li>
            <?php if (!empty($player->retention_histories)) : ?>
            <li class="tab" name="titleRetains">タイトル取得情報</li>
            <?php endif ?>
        <?php endif ?>
    </ul>

    <!-- 詳細 -->
    <div class="detail">
        <!-- 棋士成績 -->
        <section id="player">
            <?=$this->Form->create($player, [
                'id' => 'mainForm',
                'type' => 'post',
                'url' => ['action' => 'save', $player->id],
                'templates' => [
                    'inputContainer' => '{{content}}',
                    'textFormGroup' => '{{input}}',
                    'selectFormGroup' => '{{input}}'
                ]
            ])?>
                <?=$this->Form->hidden('is_continue', ['id' => 'isContinue', 'value' => false])?>
                <?=$this->Form->hidden('id', ['value' => $player->id])?>
                <?=$this->Form->hidden('optimistic_key', ['value' => $this->Date->format($player->modified, 'YYYYMMddHHmmss')])?>
                <?=$this->Form->hidden('country_id')?>
                <div class="category-row">棋士情報<?=($player->id ? '（ID：'.h($player->id).'）' : "")?></div>
                <ul class="boxes">
                    <li class="row">
                        <div class="box">
                            <div class="label-row">所属国</div>
                            <div class="input-row"><?=h($player->country->name)?></div>
                        </div>
                        <div class="box">
                            <div class="label-row"><span>所属組織</span></div>
                            <div class="input-row">
                                <span>
                                    <?=
                                        $this->Form->select('organization_id', $organizations, [
                                            'class' => 'organization',
                                            'value' => ($player->organization_id ? $player->organization_id : '1')
                                        ]);
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">引退フラグ</div>
                            <div class="input-row">
                                <?=$this->Form->checkbox('is_retired', ['id' => 'retired'])?>
                                <label for="retired">引退しました</label>
                            </div>
                        </div>
                    </li>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">棋士名</div>
                            <div class="input-row">
                                
                                <?=
                                    $this->Form->text('name', [
                                        'class' => 'playerName',
                                        'maxlength' => 20
                                    ]);
                                ?>
                                
                                
                                英語
                                <?=
                                    $this->Form->text('name_english', [
                                        'class' => 'playerName',
                                        'maxlength' => 40
                                    ]);
                                ?>
                                
                                
                                その他
                                <?=
                                    $this->Form->text('name_other', [
                                        'class' => 'playerName',
                                        'maxlength' => 20
                                    ]);
                                ?>
                                
                            </div>
                        </div>
                    </li>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">生年月日</div>
                            <div class="input-row">
                                <?php
                                    if (is_numeric($age = $player->getAge())) {
                                        $age .= '歳';
                                    }
                                    echo $this->Form->text('birthday', [
                                        'class' => 'imeDisabled datepicker birthday'
                                    ]);
                                ?>
                                <span class="age">（<?=($age ? $age : '不明')?>）
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">入段日</div>
                            <div class="input-row">
                                <?php
                                    if (!$player->id) {
                                        echo $this->Form->text('joined', [
                                            'class' => 'imeDisabled datepicker'
                                        ]);
                                    } else {
                                        echo ''.$this->Date->formatJpValue($player->joined).'';
                                        echo $this->Form->hidden('joined');
                                    }
                                ?>
                            </div>
                        </div>
                    </li>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">性別</div>
                            <div class="input-row">
                                <?php
                                    if (!$player->id) {
                                        echo $this->Form->select('sex', [
                                            '男性' => '男性',
                                            '女性' => '女性'
                                        ], [
                                            'class' => 'sex'
                                        ]);
                                    } else {
                                        echo ''.h($player->sex).'';
                                        echo $this->Form->hidden('sex');
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">段位</div>
                            <div class="input-row">
                                <?=
                                    $this->Form->select('rank_id', $ranks, [
                                        'class' => 'rank',
                                        'value' => ($player->rank_id ? $player->rank_id : '1')
                                    ]);
                                ?>
                            </div>
                        </div>
                        <div class="box2">
                            <div class="label-row">更新日時</div>
                            <div class="input-row">
                                <?=$player->modified ? $this->Date->formatToDateTime($player->modified) : ''?>
                                <?=
                                    $this->Form->hidden('modified', ['value' => $this->Date->format($player->modified, 'yyyyMMddHHmmss')])
                                ?>
                            </div>
                        </div>
                    </li>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">その他備考</div>
                            <div class="input-row">
                                <?=$this->Form->textarea('remarks', ['class' => 'remarks'])?>
                            </div>
                        </div>
                    </li>
                    <li class="button-row">
                        <?php
                            echo $this->Form->button(($player->id ? '更新' : '登録'), [
                                'data-button-type' => 'player',
                                'type' => 'button',
                                'value' => 'save'
                            ]);
                            if (!$player->id) {
                                echo $this->Form->button('連続作成', [
                                    'data-button-type' => 'player',
                                    'type' => 'button',
                                    'value' => 'saveWithContinue'
                                ]);
                            }
                        ?>
                    </li>
                </ul>
            <?=$this->Form->end()?>
            <?php $this->MyHtml->scriptStart(['inline' => false, 'block' => 'script']); ?>
            <script>
                $(function() {
                    // 登録・更新ボタン押下時
                    $("[data-button-type=player]").click(function() {
                        if ($(this).val() === 'saveWithContinue') {
                            $('#isContinue').val(true);
                        }
                        openConfirm('棋士情報を' + $(this).text() + 'します。よろしいですか？');
                    });
                });
            </script>
            <?php $this->MyHtml->scriptEnd(); ?>

        </section>

        <!-- 棋士成績 -->
        <section id="scores">
            <div class="category-row">勝敗</div>

            <?php // 2017年以降 ?>
            <?php if ($player->title_scores) : ?>
            <?php foreach ($player->title_scores as $key=>$score) : ?>
                <ul class="boxes">
                    <li class="genre-row"><?=h($score->target_year).'年度'?></li>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">勝敗（国内）</div>
                            <div class="input-row" data-row="win-loss">
                                <?=$score->win_point?>勝
                                <?=$score->lose_point?>敗
                                <?=$score->draw_point?>分
                                <span class="percent">（勝率<strong></strong>%）
                                <?=$this->Form->hidden('win_point', ['value' => $score->win_point])?>
                                <?=$this->Form->hidden('lose_point', ['value' => $score->lose_point])?>
                                <?=$this->Form->hidden('draw_point', ['value' => $score->draw_point])?>
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">勝敗（国際）</div>
                            <div class="input-row" data-row="win-loss">
                                <?=$score->win_point_world?>勝
                                <?=$score->lose_point_world?>敗
                                <?=$score->draw_point_world?>分
                                <span class="percent">（勝率<strong></strong>%）
                                <?=$this->Form->hidden('win_point_world', ['value' => $score->win_point_world])?>
                                <?=$this->Form->hidden('lose_point_world', ['value' => $score->lose_point_world])?>
                                <?=$this->Form->hidden('draw_point_world', ['value' => $score->draw_point_world])?>
                            </div>
                        </div>
                    </li>
                </ul>
            <?php endforeach ?>
            <?php endif ?>

            <?php // 2016年以前 ?>
            <?php foreach ($player->player_scores as $key=>$score) : ?>

            <?=$this->Form->create($score, [
                'name' => 'scoreForm',
                'type' => 'post',
                'url' => ['action' => 'saveScore', $score->id],
                'templates' => [
                    'inputContainer' => '{{content}}',
                    'textFormGroup' => '{{input}}',
                    'selectFormGroup' => '{{input}}'
                ]
            ])?>
                <?=$this->Form->hidden('id', ['value' => $score->id])?>
                <?=$this->Form->hidden('target_year', ['value' => $score->target_year])?>
                <?=$this->Form->hidden('optimistic_key', ['value' => $this->Date->format($score->modified, 'YYYYMMddHHmmss')])?>
                <ul class="boxes">
                    <li class="genre-row"><?=h($score->target_year).'年度'?></li>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">段位</div>
                            <div class="input-row"><?=h($score->rank->name)?></div>
                        </div>
                    </li>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">勝敗（国内）</div>
                            <div class="input-row" data-row="win-loss">
                                <?=$this->Form->text('win_point', ['value' => $score->win_point, 'class' => 'point imeDisabled']).'勝';?>
                                <?=$this->Form->text('lose_point', ['value' => $score->lose_point, 'class' => 'point imeDisabled']).'敗';?>
                                <?=$this->Form->text('draw_point', ['value' => $score->draw_point, 'class' => 'point imeDisabled']).'分';?>
                                <span class="percent">（勝率<strong></strong>%）
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">勝敗（国際）</div>
                            <div class="input-row" data-row="win-loss">
                                <?=$this->Form->text('win_point_world', ['value' => $score->win_point_world, 'class' => 'point imeDisabled']).'勝';?>
                                <?=$this->Form->text('lose_point_world', ['value' => $score->lose_point_world, 'class' => 'point imeDisabled']).'敗';?>
                                <?=$this->Form->text('draw_point_world', ['value' => $score->draw_point_world, 'class' => 'point imeDisabled']).'分';?>
                                <span class="percent">（勝率<strong></strong>%）
                            </div>
                        </div>
                    </li>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">更新日時</div>
                            <div class="input-row">
                                <div class="box2">
                                    <?=$this->Date->formatToDateTime($score->modified)?>
                                </div>
                                <div class="button-column">
                                    <?=$this->Form->button('更新', ['data-button-type' => 'score', 'type' => 'button'])?>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            <?=$this->Form->end()?>
            <?php endforeach ?>

            <?php $this->MyHtml->scriptStart(['inline' => false, 'block' => 'script']); ?>
            <script>
                $(function() {
                    var calcPercent = function(parent) {
                        var win = parent.find("[name^=win_point]").val();
                        var lose = parent.find("[name^=lose_point]").val();
                        var total = Number(win) + Number(lose);
                        var percent = (total === 0 ? 0 : Math.round(win / total * 100));
                        parent.find("strong").text(percent);
                    };

                    // 勝率を設定
                    $("[data-row=win-loss]").each(function () {
                        calcPercent($(this));
                    });

                    // 勝数、敗数変更時に勝率を再設定
                    $("[name*=point").change(function() {
                        var parent = $(this).parents("[data-row=win-loss]");
                        calcPercent(parent);
                    });

                    // 棋士成績情報更新ボタン押下時
                    $("[data-button-type=score]").click(function() {
                        var targetYear = $(this).parents("form").find("[name=target_year]").val();
                        var message = targetYear + "年度の棋士成績情報を更新します。よろしいですか？";
                        openConfirm(message, $(this).parents("form"));
                    });
                });
            </script>
            <?php $this->MyHtml->scriptEnd(); ?>
        </section>

        <!-- タイトル取得履歴 -->
        <section id="titleRetains">
            <div class="category-row">タイトル取得履歴</div>

            <?php if (!empty($player->retention_histories)) : ?>
            <?php $beforeYear = 0; ?>
            <?php foreach ($player->retention_histories as $retention_history) : ?>
                <?php if ($beforeYear !== $retention_history->target_year) : ?>
                <div class="genre-row"><?=h($retention_history->target_year).'年度'?></div>
                <?php endif ?>
                <div class="input-row">
                    <?=h($retention_history->holding).'期'.h($retention_history->title->name)?>
                    <?="（{$retention_history->title->country->name}棋戦）"?>
                </div>
                <?php $beforeYear = $retention_history->target_year; ?>
            <?php endforeach ?>
            <?php endif ?>
        </section>
    </div>
</div>

<?php $this->MyHtml->scriptStart(['inline' => false, 'block' => 'script']); ?>
<script>
    $(function() {
        // タブ選択
        selectTab('<?=($tab ?? '')?>');
    });
</script>
<?php $this->MyHtml->scriptEnd(); ?>
