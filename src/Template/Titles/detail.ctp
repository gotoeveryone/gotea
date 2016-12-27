<article class="title-detail">
    <section class="detail-dialog">
        <!-- タブ -->
        <section id="tabs">
            <section class="tab" name="title">タイトル情報</section>
            <section class="tab" name="histories">保持履歴</section>
        </section>

        <!-- 詳細 -->
        <section class="detail">
            <!-- マスタ -->
            <section id="title">
                <?=$this->Form->create($title, [
                    'id' => 'mainForm',
                    'type' => 'post',
                    'url' => ['action' => 'save', $title->id],
                    'templates' => [
                        'inputContainer' => '{{content}}',
                        'textFormGroup' => '{{input}}',
                        'selectFormGroup' => '{{input}}'
                    ]
                ])?>
                    <?=$this->Form->hidden('id', ['value' => $title->id])?>
                    <?=$this->Form->hidden('optimistic_key', ['value' => $this->Date->format($title->modified, 'yyyyMMddHHmmss')])?>
                    <section class="category-row"><span><?='タイトル情報（ID：'.h($title->id).'）'?></span></section>
                    <section class="row">
                        <section class="box3">
                            <section class="label-row"><span>タイトル名</span></section>
                            <section class="input-row">
                                <span>
                                    <?=$this->Form->text('name', ['maxlength' => 20])?>
                                </span>
                            </section>
                        </section>
                        <section class="box3">
                            <section class="label-row"><span>タイトル名（英語）</span></section>
                            <section class="input-row">
                                <span>
                                    <?=$this->Form->text('name_english', ['maxlength' => 20])?>
                                </span>
                            </section>
                        </section>
                        <section class="box3">
                            <section class="label-row"><span>分類</span></section>
                            <section class="input-row">
                                <span><?=($title->country->name).'棋戦'?></span>
                            </section>
                        </section>
                    </section>
                    <section class="row">
                        <section class="box4">
                            <section class="label-row"><span>期</span></section>
                            <section class="input-row">
                                <span>
                                    <?=$this->Form->text('holding', ['maxlength' => 3, 'class' => 'holding'])?>
                                </span>
                            </section>
                        </section>
                        <section class="box2">
                            <section class="label-row"><span>現在の保持者</span></section>
                            <section class="input-row">
                                <span>
                                <?php
                                    if (!empty($title->retention_histories)) :
                                        foreach ($title->retention_histories as $retention) :
                                            if ($retention->holding === $title->holding) :
                                                echo h($retention->getWinnerName($title->is_team));
                                                break;
                                            endif;
                                        endforeach;
                                    endif;
                                ?>
                                </span>
                            </section>
                        </section>
                        <section class="box4">
                            <section class="label-row"><span>団体戦</span></section>
                            <section class="input-row">
                                <span>
                                    <?=$this->Form->checkbox('is_team', ['id' => 'team'])?>
                                    <?=$this->Form->label('team', '団体戦')?>
                                </span>
                            </section>
                        </section>
                    </section>
                    <section class="row">
                        <section class="box3">
                            <section class="label-row"><span>HTMLファイル名</span></section>
                            <section class="input-row">
                                <span>
                                    <?=$this->Form->text('html_file_name', ['maxlength' => 10])?>
                                </span>
                            </section>
                        </section>
                        <section class="box3">
                            <section class="label-row"><span>修正日</span></section>
                            <section class="input-row">
                                <span>
                                    <?=$this->Form->text('html_file_modified')?>
                                </span>
                            </section>
                        </section>
                        <section class="box3">
                            <section class="label-row"><span>更新日時</span></section>
                            <section class="input-row"><span><?=$this->Date->formatToDateTime($title->modified)?></span></section>
                        </section>
                    </section>
                    <section class="row">
                        <section class="label-row"><span>その他備考</span></section>
                        <section class="input-row">
                            <?=$this->Form->textarea('remarks', ['class' => 'remarks'])?>
                        </section>
                    </section>
                    <section class="button-row">
                        <?=$this->Form->button('更新', ['data-button-type' => 'title', 'type' => 'button'])?>
                    </section>
                <?=$this->Form->end()?>
                <?php $this->MyHtml->scriptStart(['inline' => false, 'block' => 'script']); ?>
                <script>
                    $(function() {
                        // 登録・更新ボタン押下時
                        $("[data-button-type=title]").click(function() {
                            openConfirm('タイトル情報を更新します。よろしいですか？');
                        });
                    });
                </script>
                <?php $this->MyHtml->scriptEnd(); ?>
            </section>

            <!-- タイトル保持履歴 -->
            <section id="histories">
                <?=$this->Form->create(null, [
                    'id' => 'addHistoryForm',
                    'type' => 'post',
                    'url' => ['action' => 'addHistory'],
                    'templates' => [
                        'inputContainer' => '{{content}}',
                        'textFormGroup' => '{{input}}',
                        'selectFormGroup' => '{{input}}'
                    ]
                ])?>
                    <?=$this->Form->hidden('is_latest', ['id' => 'isLatest', 'value' => false])?>
                    <?=$this->Form->hidden('title_id', ['value' => $title->id])?>
                    <?=$this->Form->hidden('name', ['value' => $title->name])?>
                    <?=$this->Form->hidden('is_team', ['value' => $title->is_team])?>
                    <section class="category-row"><span>保持情報</span></section>
                    <section class="row">
                        <section class="label-row"><span>新規登録</span></section>
                        <section class="add-condition input-row">
                            <section class="box2">
                                <span>
                                    対象年：
                                    <?=$this->Form->text('target_year', ['maxlength' => 4, 'class' => 'year'])?>
                                </span>
                                <span>
                                    期：
                                    <?=$this->Form->text('holding', ['maxlength' => 3, 'class' => 'holding'])?>
                                </span>
                            </section>
                            <section class="box2 button-area">
                                <?=
                                    $this->Form->button('新規登録', [
                                        'type' => 'button',
                                        'disabled' => true,
                                        'data-button-type' => 'add'
                                    ]);
                                ?>
                                <?=
                                    $this->Form->button('最新として登録', [
                                        'type' => 'button',
                                        'disabled' => true,
                                        'data-button-type' => 'addLatest'
                                    ]);
                                ?>
                            </section>
                        </section>
                        <section class="add-condition input-row">
                            <section class="box">
                                <span>
                                <?php if ($title->is_team) : ?>
                                    優勝団体名：
                                    <?=$this->Form->text('win_group_name', ['id' => 'winner', 'maxlength' => 30])?>
                                <?php else : ?>
                                    設定棋士名：
                                    <strong id="winnerName"><span>（検索エリアから棋士を検索してください。）</span></strong>
                                    <?=$this->Form->hidden('player_id', ['id' => 'winner'])?>
                                    <?=$this->Form->hidden('rank_id', ['id' => 'winnerRank'])?>
                                <?php endif ?>
                                </span>
                            </section>
                        </section>
                    </section>
                    <?php if (!$title->is_team) : ?>
                    <section class="row">
                        <section class="label-row"><span>棋士検索</span></section>
                        <section class="input-row">
                            <section class="box2">
                                <span>
                                    棋士名：
                                    <?=$this->Form->text('player_name', ['id' => 'playerName', 'value' => '', 'class' => 'playerName']);?>
                                </span>
                            </section>
                            <section class="box2 button-area">
                                <?=$this->Form->button('検索', ['type' => 'button', 'id' => 'searchPlayer']);?>
                            </section>
                        </section>
                        <section class="retentions"><table></table></section>
                    </section>
                    <?php endif ?>
                    <?php if (!empty($title->retention_histories)) : ?>
                        <?php foreach ($title->retention_histories as $retention) : ?>
                            <?php if ($title->holding === $retention->holding) : ?>
                            <section class="row">
                                <section class="label-row">
                                    <section class="box"><span>現在の保持情報</span></section>
                                </section>
                                <section class="input-row">
                                    <span>
                                        <?=h(__("{$retention->target_year}年 {$retention->holding}期 {$retention->name} "))?>
                                        <?=h($retention->getWinnerName($title->is_team))?>
                                    </span>
                                </section>
                            </section>
                            <?php break ?>
                            <?php endif ?>
                        <?php endforeach ?>
                        <section class="row">
                        <?php $header = false; ?>
                        <?php foreach ($title->retention_histories as $retention) : ?>
                            <?php if (!$header) : ?>
                            <section class="label-row">
                                <span>保持情報（履歴）</span>
                            </section>
                            <?php $header = true; ?>
                            <?php endif ?>
                            <?php if ($title->holding !== $retention->holding) : ?>
                            <section class="input-row">
                                <span>
                                    <?=h(__("{$retention->target_year}年 {$retention->holding}期 {$retention->name} "))?>
                                    <?=h($retention->getWinnerName($title->is_team))?>
                                </span>
                            </section>
                            <?php endif ?>
                        <?php endforeach ?>
                        </section>
                    <?php endif ?>
                </section>
                <?=$this->Form->end()?>
                <?php $this->MyHtml->scriptStart(['inline' => false, 'block' => 'script']); ?>
                <script>
                    $(function() {
                        // 新規登録関連ボタンの制御
                        var controlAddCondition = function() {
                            var disabled = false;
                            $('.add-condition input[type!=hidden]').each(function() {
                                if (!$(this).val()) {
                                    $('.add-condition button').attr('disabled', true);
                                    disabled = true;
                                    return false;
                                }
                            });
                            if (!disabled) {
                                $('.add-condition button').removeAttr('disabled');
                            }
                        };
                        controlAddCondition();

                        // 登録エリアのテキスト変更時
                        $('.add-condition input[type!=hidden]').on('change', function() {
                            controlAddCondition();
                        });

                        // 新規登録、最新として登録ボタン押下時
                        $('.add-condition button').on('click', function() {
                            if (!$('#winner').val()) {
                                var dialog = $("#dialog");
                                dialog.html('<?=($title->is_team ? '優勝団体名を入力してください。' : '棋士を選択してください。')?>');
                                dialog.click();
                                return;
                            }

                            // 最新として登録するかどうか
                            $('#isLatest').val(($(this).attr('data-button-type') === 'addLatest'));

                            openConfirm('保持履歴を登録します。よろしいですか？', $('#addHistoryForm'));
                        });

                        <?php if (!$title->is_team) : ?>
                        // 棋士名を抜き出す
                        if ($('#winner').val()) {
                            $.ajax({
                                type: 'GET',
                                url: "<?=$this->Url->build(['controller' => 'api', 'action' => 'player'])?>/" + $('#winner').val(),
                                contentType: "application/json",
                                dataType: 'json'
                            }).done(function (data) {
                                data = data.response;
                                $('#winnerName').text(data.name + ' ' + data.rank.name);
                            });
                        }

                        // 棋士検索ボタン押下時
                        $('#searchPlayer').click(function(event) {
                            event.preventDefault();

                            var searchValue = $("#playerName").val();
                            if (!searchValue) {
                                var dialog = $("#dialog");
                                dialog.html('<span class="red">棋士名を入力してください。</span>');
                                dialog.click();
                                return false;
                            }
                            $.ajax({
                                type: 'POST',
                                url: "<?=$this->Url->build(['controller' => 'api', 'action' => 'players'])?>",
                                contentType: "application/json",
                                dataType: 'json',
                                data: JSON.stringify({name: searchValue})
                            }).done(function (data) {
                                data = data.response;
                                // 該当者1件の場合はそのまま設定
                                if (data.size === 1) {
                                    var obj = data.results[0];
                                    $('#winner').val(obj.id);
                                    $("#winnerRank").val(obj.rankId);
                                    $('#winnerName').css("color", "#000000").text(obj.name + " " + obj.rankName);
                                    $("#playerName").val('');
                                    return false;
                                }
                                var resultArea = $(".retentions table");
                                resultArea.find("*").remove();
                                var tbody = $("<tbody>");
                                $.each(data.results, function(idx, obj) {
                                    var tr = $("<tr>")
                                            .append($("<input>", {type: "hidden", "data-name": "id", value: obj.id}))
                                            .append($("<input>", {type: "hidden", "data-name": "rankId", value: obj.rankId}))
                                            .append($("<td>", {"data-name": "playerName"}).text(obj.name))
                                            .append($("<td>").text(obj.nameEnglish))
                                            .append($("<td>", {"class": "center"}).text(obj.countryName))
                                            .append($("<td>", {"data-name": "rankName", "class": "center"}).text(obj.rankName))
                                            .append($("<td>", {"class": "center"}).text(obj.sex))
                                            .append($("<td>", {"class": "center"}).append($("<button>", {type: "button", name: "select", style: "font-size: 12px"}).text("選択")));
                                    tbody.append(tr);
                                });
                                resultArea.append(tbody);
                                $(".retentions").show();
                            }).fail(function (data) {
                                var dialog = $("#dialog");
                                dialog.html('<span class="red">棋士検索に失敗しました。</span>');
                                dialog.click();
                            });
                        });
                        // 選択ボタン押下時
                        $('#histories').on('click', '[name=select]', function() {
                            var parent = $(this).parents("tr");
                            $('#winner').val(parent.find("[data-name=id]").val());
                            $("#winnerRank").val(parent.find("[data-name=rankId]").val());
                            var playerName = parent.find("[data-name=playerName]").text();
                            var rankName = parent.find("[data-name=rankName]").text();
                            $("#winnerName").css("color", "#000000").text(playerName + " " + rankName);
                            $("#playerName").val('');
                            // 一覧を消す
                            $(".retentions table *").remove();
                            $(".retentions").hide();
                        });
                        <?php endif ?>
                    });
                </script>
                <?php $this->MyHtml->scriptEnd(); ?>
            </section>
        </section>
</article>
