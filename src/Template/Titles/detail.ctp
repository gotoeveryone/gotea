<article class="title-detail">
    <?=$this->Form->create(null, [
        'id' => 'mainForm',
        'method' => 'post',
        'url' => ['action' => 'detail'],
        'templates' => [
            'inputContainer' => '{{content}}',
            'textFormGroup' => '{{input}}',
            'selectFormGroup' => '{{input}}'
        ]
    ])?>
        <?=$this->Form->hidden('selectTitleId', ['value' => $title->id])?>
        <?=$this->Form->hidden('selectTitleName', ['value' => $title->name])?>
        <?=$this->Form->hidden('lastUpdateTitle', ['value' => $this->Date->format($title->modified, 'yyyyMMddHHmmss')])?>
        <section class="detail-dialog">
            <!-- タブ -->
            <section id="tabs">
                <section class="tab" name="title">タイトル情報</section>
                <section class="tab" name="titleRetains">保持履歴</section>
            </section>

            <!-- 詳細 -->
            <section class="detail">
                <!-- マスタ -->
                <section id="title">
                    <section class="category-row"><span><?='タイトル情報（ID：'.h($title->id).'）'?></span></section>
                    <section class="row">
                        <section class="box2">
                            <section class="label-row"><span>タイトル名</span></section>
                            <section class="input-row"><span><?=h($title->name)?></span></section>
                        </section>
                        <section class="box2">
                            <section class="label-row"><span>タイトル名（英語）</span></section>
                            <section class="input-row"><span><?=h($title->name_english)?></span></section>
                        </section>
                    </section>
                    <section class="row">
                        <section class="box2">
                            <section class="label-row"><span>分類</span></section>
                            <section class="input-row"><span><?=($title->country->name).'棋戦'?></span></section>
                        </section>
                        <section class="box2">
                            <section class="label-row"><span>更新日時</span></section>
                            <section class="input-row"><span><?=$this->Date->formatToDateTime($title->modified)?></span></section>
                        </section>
                    </section>
                    <section class="row">
                        <section class="label-row"><span>現在の保持者</span></section>
                        <section class="input-row">
                            <?php
                                if (!empty($title->retention_histories)) :
                                    foreach ($title->retention_histories as $retention) :
                                        if ($retention->holding === $title->holding) :
                                            echo '<span>';
                                            echo h(__("{$title->holding}期 "));
                                            echo h($retention->getWinnerName($title->is_team));
                                            echo '</span>';
                                            break;
                                        endif;
                                    endforeach;
                                endif;
                            ?>
                        </section>
                    </section>
                    <!-- 更新機能がないためコメント
                    <section class="row">
                        <section class="box">
                            <section class="key">その他備考</section>
                            <section class="value">
                                <?=
                                    $this->Form->textarea('remarks', [
                                        'cols' => 30,
                                        'rows' => 3,
                                        'class' => 'remarks',
                                        'value' => h($title->remarks)
                                    ])
                                ?>
                            </section>
                        </section>
                    </section>
                    -->
                </section>

                <!-- タイトル取得履歴 -->
                <section id="titleRetains">
                    <?=$this->Form->hidden('registWithMapping', ['id' => 'registWithMapping', 'value' => false])?>
                    <section class="category-row"><span>保持情報</span></section>
                    <section class="row">
                        <section class="label-row"><span>新規登録</span></section>
                        <section class="input-row">
                            <section class="box2">
                                <span>
                                    対象年：
                                    <?=
                                        $this->Form->text('registYear', [
                                            'id' => 'registYear',
                                            'value' => $this->request->data('registYear'),
                                            'maxlength' => 4,
                                            'class' => 'year imeDisabled'
                                        ]);
                                    ?>
                                </span>
                                <span>
                                    期：
                                    <?=
                                        $this->Form->text('registHolding', [
                                            'id' => 'registHolding',
                                            'value' => $this->request->data('registHolding'),
                                            'maxlength' => 3,
                                            'class' => 'holding imeDisabled'
                                        ]);
                                    ?>
                                </span>
                            </section>
                            <section class="box2 button-area">
                                <?=
                                    $this->Form->button('新規登録', [
                                        'type' => 'button',
                                        'id' => 'regist',
                                        'disabled' => true,
                                        'with-mapping' => false
                                    ]);
                                ?>
                                <?=
                                    $this->Form->button('最新として登録', [
                                        'type' => 'button',
                                        'id' => 'registNew',
                                        'disabled' => true,
                                        'with-mapping' => true
                                    ]);
                                ?>
                            </section>
                        </section>
                        <section class="input-row">
                            <?php
                                echo '<span>';
                                if ($title->is_team) {
                                    echo '優勝団体名：';
                                    echo $this->Form->text('registGroupName', [
                                        'id' => 'registGroupName',
                                        'value' => $this->request->data('registGroupName'),
                                        'maxlength' => 30
                                    ]);
                                } else {
                                    echo '設定棋士名：';
                                    echo '<strong id="registPlayerName">（検索エリアから棋士を検索してください。）</strong>';
                                    echo $this->Form->hidden('registPlayerId', [
                                        'id' => 'registPlayerId',
                                        'value' => $this->request->data('registPlayerId')
                                    ]);
                                    echo $this->Form->hidden('registRank', [
                                        'id' => 'registRank',
                                        'value' => $this->request->data('registRank')
                                    ]);
                                }
                                echo '</span>';
                            ?>
                        </section>
                    </section>
                    <?php if (!$title->is_team) : ?>
                    <section class="row">
                        <section class="label-row"><span>棋士検索</span></section>
                        <section class="input-row">
                            <section class="box2">
                                <span>
                                    棋士名：
                                    <?=$this->Form->text('searchPlayerName', ['id' => 'searchPlayerName', 'value' => '', 'class' => 'playerName']);?>
                                </span>
                            </section>
                            <section class="box2 button-area">
                                <?=$this->Form->button('検索', ['type' => 'button', 'id' => 'searchPlayer']);?>
                            </section>
                        </section>
                        <section id="searchResult"><table></table></section>
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
            </section>
        </section>
    <?=$this->Form->end()?>
</article>
<script type="text/javascript">
    $(document).ready(function() {
        controlDisabled();
        // 登録エリアの対象年、期フォーカスアウト時
        $('#registYear, #registHolding').blur(function() {
            controlDisabled();
        });
        // 新規登録、最新として登録ボタン押下時
        $('#regist, #registNew').click(function() {
            regist($(this).attr('with-mapping'));
        });
        function regist(withMapping) {
            <?php
                if ($title->is_team) {
                    echo 'if ($("#registGroupName").val() === "") {';
                    echo '    var dialog = $("#dialog");';
                    echo '    dialog.html("優勝団体名を入力してください。");';
                    echo '    dialog.click();';
                    echo '    return;';
                    echo '}';
                } else {
                    echo 'if ($("#registPlayerId").val() === "") {';
                    echo '    var dialog = $("#dialog");';
                    echo '    dialog.html("棋士を選択してください。");';
                    echo '    dialog.click();';
                    echo '    return;';
                    echo '}';
                }
            ?>
            $('#registWithMapping').val(withMapping);
            var confirm = $("#confirm");
            confirm.html('タイトル保持情報を登録します。よろしいですか？');
            $('#mainForm').attr('action', '<?=$this->Url->build(['action' => 'regist'])?>');
            confirm.click();
        }
        // 新規登録ボタンの制御
        function controlDisabled() {
            $('#regist, #registNew').attr('disabled', !($('#registYear').val() !== '' && $('#registHolding').val() !== ''));
        }
        <?php if (!$title->is_team) : ?>
        // 棋士検索ボタン押下時
        $('#searchPlayer').click(function() {
            var searchValue = $("#searchPlayerName").val();
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
                    $('#registPlayerId').val(obj.id);
                    $('#registRank').val(obj.rankNumber);
                    $('#registPlayerName').css("color", "#000000").html(obj.name + " " + obj.rankName);
                    $("#searchPlayerName").val('');
                    return false;
                }
                var resultArea = $("#searchResult table");
                resultArea.find("*").remove();
                var tbody = $("<tbody>");
                $.each(data.results, function(idx, obj) {
                    var tr = $("<tr>")
                            .append($("<input>", {type: "hidden", name: "id", value: obj.id}))
                            .append($("<input>", {type: "hidden", name: "rank", value: obj.rankNumber}))
                            .append($("<td>", {name: "playerName"}).text(obj.name))
                            .append($("<td>").text(obj.nameEnglish))
                            .append($("<td>", {"class": "center"}).text(obj.countryName))
                            .append($("<td>", {name: "rankName", "class": "center"}).text(obj.rankName))
                            .append($("<td>", {"class": "center"}).text(obj.sex))
                            .append($("<td>", {"class": "center"}).append($("<button>", {type: "button", name: "select", style: "font-size: 12px"}).text("選択")));
                    tbody.append(tr);
                });
                resultArea.append(tbody);
                $("#searchResult").show();
            }).fail(function (data) {
                var dialog = $("#dialog");
                dialog.html('<span class="red">棋士検索に失敗しました。</span>');
                dialog.click();
            });
        });
        // 選択ボタン押下時
        $('#searchResult').on('click', '[name=select]', function() {
            var parent = $(this).parents("tr");
            var playerId = parent.find("[name=id]").val();
            var playerName = parent.find("[name=playerName]").text();
            var playerRank = parent.find("[name=rank]").val();
            var playerRankText = parent.find("[name=rankName]").text();
            $("#registPlayerId").val(playerId);
            $("#registRank").val(playerRank);
            $("#registPlayerName").css("color", "#000000").text(playerName + " " + playerRankText);
            $("#searchPlayerName").val('');
            // 一覧を消す
            $("#searchResult table *").remove();
            $("#searchResult").hide();
        });
        <?php endif ?>
    });
</script>