<article id="player-detail">
    <section class="detail-dialog">
        <!-- タブ -->
        <section id="tabs">
            <section class="tab" name="player">棋士情報</section>
            <?php if ($player->id) : ?>
                <section class="tab" name="scores">成績情報</section>
                <?php if (!empty($player->retention_histories)) : ?>
                <section class="tab" name="titleRetains">タイトル取得情報</section>
                <?php endif ?>
            <?php endif ?>
        </section>

        <!-- 詳細 -->
        <section class="detail">
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
                    <section class="category-row"><span>棋士情報<?=($player->id ? '（ID：'.h($player->id).'）' : "")?></span></section>
                    <section class="row">
                        <section class="box3">
                            <section class="label-row"><span>所属国</span></section>
                            <section class="input-row"><span><?=h($player->country->name)?></span></section>
                        </section>
                        <section class="box3">
                            <section class="label-row"><span>所属組織</span></section>
                            <section class="input-row">
                                <span>
                                    <?=
                                        $this->Form->input('organization_id', [
                                            'options' => $organizations,
                                            'class' => 'organization',
                                            'value' => ($player->organization_id ? $player->organization_id : '1')
                                        ]);
                                    ?>
                                </span>
                            </section>
                        </section>
                        <section class="box3">
                            <section class="label-row"><span>引退フラグ</span></section>
                            <section class="input-row">
                                <?=$this->Form->checkbox('is_retired', ['id' => 'retired'])?>
                                <label for="retired">引退しました</label>
                            </section>
                        </section>
                    </section>
                    <section class="row">
                        <section class="label-row"><span>棋士名</span></section>
                        <section class="input-row">
                            <span>
                            <?=
                                $this->Form->text('name', [
                                    'class' => 'playerName',
                                    'maxlength' => 20
                                ]);
                            ?>
                            </span>
                            <span>
                            英語
                            <?=
                                $this->Form->text('name_english', [
                                    'class' => 'playerName',
                                    'maxlength' => 40
                                ]);
                            ?>
                            </span>
                            <span>
                            その他
                            <?=
                                $this->Form->text('name_other', [
                                    'class' => 'playerName',
                                    'maxlength' => 20
                                ]);
                            ?>
                            </span>
                        </section>
                    </section>
                    <section class="row">
                        <section class="box2">
                            <section class="label-row"><span>生年月日</span></section>
                            <section class="input-row">
                                <?php
                                    if (is_numeric($age = $player->getAge())) {
                                        $age .= '歳';
                                    }
                                    echo $this->Form->text('birthday', [
                                        'class' => 'imeDisabled datepicker birthday'
                                    ]);
                                ?>
                                <span class="age">（<?=($age ? $age : '不明')?>）</span>
                            </section>
                        </section>
                        <section class="box2">
                            <section class="label-row"><span>入段日</span></section>
                            <section class="input-row">
                                <?php
                                    if (!$player->id) {
                                        echo $this->Form->text('joined', [
                                            'class' => 'imeDisabled datepicker'
                                        ]);
                                    } else {
                                        echo '<span>'.$this->Date->formatJpValue($player->joined).'</span>';
                                        echo $this->Form->hidden('joined');
                                    }
                                ?>
                            </section>
                        </section>
                    </section>
                    <section class="row">
                        <section class="box4">
                            <section class="label-row"><span>性別</span></section>
                            <section class="input-row">
                                <?php
                                    if (!$player->id) {
                                        echo $this->Form->input('sex', [
                                            'options' => array(
                                                '男性' => '男性',
                                                '女性' => '女性'
                                            ),
                                            'class' => 'sex'
                                        ]);
                                    } else {
                                        echo '<span>'.h($player->sex).'</span>';
                                        echo $this->Form->hidden('sex');
                                    }
                                ?>
                            </section>
                        </section>
                        <section class="box4">
                            <section class="label-row"><span>段位</span></section>
                            <section class="input-row">
                                <?=
                                    $this->Form->input('rank_id', [
                                        'options' => $ranks,
                                        'class' => 'rank',
                                        'value' => ($player->rank_id ? $player->rank_id : '1')
                                    ]);
                                ?>
                            </section>
                        </section>
                        <section class="box2">
                            <section class="label-row"><span>更新日時</span></section>
                            <section class="input-row">
                                <span><?=$this->Date->formatToDateTime($player->modified)?></span>
                                <?=
                                    $this->Form->hidden('modified', ['value' => $this->Date->format($player->modified, 'yyyyMMddHHmmss')])
                                ?>
                            </section>
                        </section>
                    </section>
                    <section class="row">
                        <section class="box">
                            <section class="label-row"><span>その他備考</span></section>
                            <section class="input-row">
                                <?=$this->Form->textarea('remarks', ['class' => 'remarks'])?>
                            </section>
                        </section>
                    </section>
                    <section class="button-row">
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
                    </section>
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
                <section class="category-row"><span>勝敗</span></section>

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
                    <section class="row-group">
                        <section class="genre-row">
                            <span><?=h($score->target_year).'年度'?></span>
                        </section>
                        <section class="row">
                            <section class="label-row"><span>段位</span></section>
                            <section class="input-row"><?=h($score->rank->name)?></section>
                        </section>
                        <section class="row">
                            <section class="box2">
                                <section class="label-row"><span>勝敗（国内）</span></section>
                                <section class="input-row" data-row="win-loss">
                                    <span>
                                        <?=$this->Form->text('win_point', ['value' => $score->win_point, 'class' => 'point imeDisabled']).'勝';?>
                                    </span>
                                    <span>
                                        <?=$this->Form->text('lose_point', ['value' => $score->lose_point, 'class' => 'point imeDisabled']).'敗';?>
                                    </span>
                                    <span>
                                        <?=$this->Form->text('draw_point', ['value' => $score->draw_point, 'class' => 'point imeDisabled']).'分';?>
                                    </span>
                                    <span class="percent">（勝率<strong></strong>%）<span>
                                </section>
                            </section>
                            <section class="box2">
                                <section class="label-row"><span>勝敗（国際）</span></section>
                                <section class="input-row" data-row="win-loss">
                                    </span>
                                        <?=$this->Form->text('win_point_world', ['value' => $score->win_point_world, 'class' => 'point imeDisabled']).'勝';?>
                                    </span>
                                    <span>
                                        <?=$this->Form->text('lose_point_world', ['value' => $score->lose_point_world, 'class' => 'point imeDisabled']).'敗';?>
                                    </span>
                                    <span>
                                        <?=$this->Form->text('draw_point_world', ['value' => $score->draw_point_world, 'class' => 'point imeDisabled']).'分';?>
                                    </span>
                                    <span class="percent">（勝率<strong></strong>%）</span>
                                </section>
                            </section>
                        </section>
                        <section class="row">
                            <section class="label-row"><span>更新日時</span></section>
                            <section class="input-row">
                                <section class="box2">
                                    <span><?=$this->Date->formatToDateTime($score->modified)?></span>
                                </section>
                                <section class="box2 button-area">
                                    <?=$this->Form->button('更新', ['data-button-type' => 'score', 'type' => 'button'])?>
                                </section>
                            </section>
                        </section>
                    </section>
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
                <section class="category-row"><span>タイトル取得履歴</span></section>

                <?php if (!empty($player->retention_histories)) : ?>
                <?php $beforeYear = 0; ?>
                <?php foreach ($player->retention_histories as $retention_history) : ?>
                    <?php if ($beforeYear !== $retention_history->target_year) : ?>
                    <section class="genre-row"><span><?=h($retention_history->target_year).'年度'?></span></section>
                    <?php endif ?>
                    <section class="input-row">
                        <span>
                            <?=h($retention_history->holding).'期'.h($retention_history->title->name)?>
                            <?="（{$retention_history->title->country->name}棋戦）"?>
                        </span>
                    </section>
                    <?php $beforeYear = $retention_history->target_year; ?>
                <?php endforeach ?>
                <?php endif ?>
            </section>
        </section>
    </section>
</article>
