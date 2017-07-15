<div class="detail-dialog">
    <!-- タブ -->
    <ul id="tabs" class="tabs">
        <li class="tab" name="title">タイトル情報</li>
        <li class="tab" name="histories">保持履歴</li>
    </ul>

    <!-- 詳細 -->
    <div class="detail">
        <!-- マスタ -->
        <section id="title">
            <?=$this->Form->create($title, [
                'id' => 'mainForm',
                'type' => 'post',
                'url' => ['action' => 'save'],
                'templates' => [
                    'inputContainer' => '{{content}}',
                    'textFormGroup' => '{{input}}',
                    'selectFormGroup' => '{{input}}'
                ]
            ])?>
                <?=$this->Form->hidden('id', ['value' => $title->id])?>
                <?=$this->Form->hidden('optimistic_key', ['value' => $this->Date->format($title->modified, 'yyyyMMddHHmmss')])?>
                <div class="category-row"><?='タイトル情報（ID：'.h($title->id).'）'?></div>
                <ul class="boxes">
                    <li class="row">
                        <div class="box">
                            <div class="label-row">タイトル名</div>
                            <div class="input-row"><?=$this->Form->text('name', ['maxlength' => 20])?></div>
                        </div>
                        <div class="box">
                            <div class="label-row">タイトル名（英語）</div>
                            <div class="input-row"><?=$this->Form->text('name_english', ['maxlength' => 20])?></div>
                        </div>
                        <div class="box">
                            <div class="label-row">分類</div>
                            <div class="input-row"><?=($title->country->name).'棋戦'?></div>
                        </div>
                    </li>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">期</div>
                            <div class="input-row"><?=$this->Form->text('holding', ['maxlength' => 3, 'class' => 'holding'])?></div>
                        </div>
                        <div class="box">
                            <div class="label-row">現在の保持者</div>
                            <div class="input-row"><?= h($title->getWinnerName(true)) ?></div>
                        </div>
                        <div class="box">
                            <div class="label-row">団体戦</div>
                            <div class="input-row">
                                <?=$this->Form->checkbox('is_team', ['id' => 'team'])?>
                                <?=$this->Form->label('team', '団体戦')?>
                            </div>
                        </div>
                    </li>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">HTMLファイル名</div>
                            <div class="input-row">
                                <?=$this->Form->text('html_file_name', ['maxlength' => 10])?>
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">修正日</div>
                            <div class="input-row">
                                <?=$this->Form->text('html_file_modified', ['class' => 'datepicker'])?>
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">更新日時</div>
                            <div class="input-row"><?=$this->Date->formatToDateTime($title->modified)?></div>
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
                        <?=$this->Form->button('更新', ['data-button-type' => 'title', 'type' => 'button'])?>
                    </li>
                </ul>
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
                <?=$this->Form->hidden('is_latest', ['id' => 'isLatest', 'value' => ''])?>
                <?=$this->Form->hidden('title_id', ['value' => $title->id])?>
                <?=$this->Form->hidden('name', ['value' => $title->name])?>
                <?=$this->Form->hidden('is_team', ['value' => $title->is_team])?>
                <div class="category-row">保持情報</div>
                <ul class="boxes">
                    <li class="row">
                        <div class="box">
                        <div class="label-row">新規登録</div>
                        <div class="add-condition input-row">
                            <div class="box">
                                対象年：
                                <?=$this->Form->text('target_year', ['value' => '', 'maxlength' => 4, 'class' => 'year'])?>
                                期：
                                <?=$this->Form->text('holding', ['value' => '', 'maxlength' => 3, 'class' => 'holding'])?>
                            </div>
                            <div class="button-column">
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
                            </div>
                        </div>
                        <div class="add-condition input-row">
                            <div class="box">
                                <?php if ($title->is_team) : ?>
                                    優勝団体名：<?=$this->Form->text('win_group_name', ['value' => '', 'id' => 'winner', 'maxlength' => 30])?>
                                <?php else : ?>
                                    設定棋士名：
                                    <strong id="winnerName">（検索エリアから棋士を検索してください。）</strong>
                                    <?=$this->Form->hidden('player_id', ['id' => 'winner'])?>
                                    <?=$this->Form->hidden('rank_id', ['id' => 'winnerRank'])?>
                                <?php endif ?>
                            </div>
                        </div>
                        </div>
                    </li>
                    <?php if (!$title->is_team) : ?>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">棋士検索</div>
                            <div class="input-row">
                                <div class="box">
                                    棋士名：
                                    <?=$this->Form->text('player_name', ['id' => 'playerName', 'value' => '', 'class' => 'playerName']);?>
                                </div>
                                <div class="button-column">
                                    <?=$this->Form->button('検索', ['type' => 'button', 'id' => 'searchPlayer']);?>
                                </div>
                            </div>
                            <div class="retentions"><table></table></div>
                        </div>
                    </li>
                    <?php endif ?>
                    <?php if (!empty(($histories))) : ?>
                        <?php foreach ($histories as $history) : ?>
                            <?php if ($title->holding === $history->holding) : ?>
                            <li class="row">
                                <div class="box">
                                    <div class="label-row">現在の保持情報</div>
                                    <div class="input-row">
                                        <?=h(__("{$history->target_year}年 {$history->holding}期 {$history->name} "))?>
                                        <?=h($history->getWinnerName($title->is_team))?>
                                    </div>
                                </div>
                            </li>
                            <?php break ?>
                            <?php endif ?>
                        <?php endforeach ?>
                        <?php $header = false; ?>
                        <?php foreach ($histories as $history) : ?>
                            <?php if (!$header) : ?>
                            <li class="row">
                                <div class="box">
                                    <div class="label-row">保持情報（履歴）</div>
                                </div>
                            </li>
                            <?php $header = true; ?>
                            <?php endif ?>
                            <?php if ($title->holding !== $history->holding) : ?>
                            <li class="row">
                                <div class="box">
                                    <div class="input-row">
                                        <?=h(__("{$history->target_year}年 {$history->holding}期 {$history->name} "))?>
                                        <?=h($history->getWinnerName($title->is_team))?>
                                    </div>
                                </div>
                            </li>
                            <?php endif ?>
                        <?php endforeach ?>
                    <?php endif ?>
                </ul>
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
                    if ($(this).attr('data-button-type') === 'addLatest') {
                        $('#isLatest').val(true);
                    }

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
