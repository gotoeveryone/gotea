<section class="ranking">
    <?=$this->Form->create(null, [
        'id' => 'mainForm',
        'method' => 'post',
        'url' => ['action' => 'index'],
        'templates' => [
            'inputContainer' => '{{content}}',
            'textFormGroup' => '{{input}}',
            'selectFormGroup' => '{{input}}'
        ]
    ])?>
        <ul class="search-header">
            <li class="search-row">
                <label>抽出対象：</label>
                <?=
                    $this->Form->input('selectYear', [
                        'id' => 'selectYear',
                        'options' => $years
                    ]);
                ?>
                <?=
                    $this->Form->input('selectCountry', [
                        'id' => 'selectCountry',
                        'options' => $countries
                    ]);
                ?>
                <?=
                    $this->Form->input('selectRank', [
                        'id' => 'selectRank',
                        'options' => [
                            '20' => '～20位',
                            '30' => '～30位',
                            '40' => '～40位',
                            '50' => '～50位',
                        ]
                    ]);
                ?>
            </li>
            <li class="search-row">
                <label>最終更新日：</label>
                <span class="lastUpdate"></span>
                <div class="button-column">
                    <!-- 検索ボタン -->
                    <?=
                        $this->Form->button('検索', [
                            'type' => 'button',
                            'id' => 'search'
                        ]);
                    ?>
                    <!-- JSON連携ボタン -->
                    <?=
                        $this->Form->button('JSON出力', [
                            'type' => 'button',
                            'id' => 'outputJson',
                            'disabled' => true
                        ]);
                    ?>
                </div>
            </li>
        </ul>

        <div class="search-results">
            <ul class="table-header">
                <li class="table-row">
                    <span class="no">No.</span>
                    <span class="player">棋士名</span>
                    <span class="point">勝</span>
                    <span class="point">敗</span>
                    <span class="point">分</span>
                    <span class="percent">勝率</span>
                </li>
            </ul>
        </div>
    <?=$this->Form->end()?>
</section>

<?php $this->MyHtml->scriptStart(['inline' => false, 'block' => 'script']); ?>
<script>
    $(function() {
        // 検索ボタン押下時
        $('#search').click(function() {
            var country = $('#selectCountry').val();
            var year = $('#selectYear').val();
            var rank = $('#selectRank').val();
            var type = 'json';

            $.ajax({
                type: 'GET',
                url: '<?=$this->Url->build(['controller' => 'api', 'action' => 'rankings'])?>/' + country + '/' + year + '/' + rank + '/?jp=true',
                contentType: 'application/' + type,
                dataType: type,
                crossDomain: true,
                accepts: "application/json; charset=utf-8"
            }).done(function (data) {
                data = data.response;
                // ボディを削除
                $('.table-body').remove();

                // JSON出力ボタンを活性状態に変更
                $('#outputJson').removeAttr('disabled');

                // JSONから最終更新日を取得
                var lastUpdate = new Date(data.lastUpdate);
                $('.lastUpdate').html(lastUpdate.getFullYear() + '年'
                        + (lastUpdate.getMonth() + 1) + '月' + lastUpdate.getDate() + '日');

                var body = $('<ul>', {class: "table-body"});
                var beforeRank = 0;
                for (var i = 0; i < Object.keys(data.ranking).length; i++) {

                    var record = data.ranking[i];

                    // ランクが前順位と同じなら"&nbsp;"を設定（IE用）
                    var rank = record.rank;
                    if (beforeRank === rank) {
                        rank = '&nbsp;';
                    } else {
                        beforeRank = rank;
                    }

                    if (record.sex === '女性') {
                        record.playerNameJp = '<span class="female">' + record.playerNameJp + '</span>';
                    }

                    // 行を生成
                    var row = $('<li>', {class: 'table-row'})
                        .append($('<span>', {class: 'right no'}).html('<span class="rank">' + rank + '</span>'))
                        .append($('<span>', {class: 'left player'}).html($('<a>', {"class": "colorbox", "href": "<?=$this->Url->build(['controller' => 'players', 'action' => 'detail'])?>/" + record.playerId}).html(record.playerNameJp)))
                        .append($('<span>', {class: 'point'}).html(record.winPoint))
                        .append($('<span>', {class: 'point'}).html(record.losePoint))
                        .append($('<span>', {class: 'point'}).html(record.drawPoint))
                        .append($('<span>', {class: 'percent'}).html(Math.round(record.winPercentage * 100) + '%'));

                    // 一覧に行を追加
                    body.append(row);
                }
                // ボディを追加
                $('.table-header').after(body);
                setColorbox();
            }).fail(function (data) {
                // JSON出力ボタンを非活性状態に変更し、エラーメッセージを出力
                $('#outputJson').attr('disabled', true);
                var dialog = $("#dialog");
                dialog.html('<span class="red">ランキングの生成に失敗しました。</span>');
                dialog.click();
            });
        });

        // JSON出力ボタン押下時
        $('#outputJson').click(function() {
            var country = $('#selectCountry').val();
            var year = $('#selectYear').val();
            var rank = $('#selectRank').val();
            var type = 'json';

            $.ajax({
                type: 'GET',
                url: '<?=$this->Url->build(['controller' => 'api', 'action' => 'rankings'])?>/' + country + '/' + year + '/' + rank + '/?make=true',
                contentType: 'application/' + type,
                dataType: type,
                crossDomain: true,
                accepts: "application/json; charset=utf-8"
            }).done(function (data) {
                var dialog = $("#dialog");
                dialog.html('JSON出力に成功しました。');
                dialog.click();
            }).fail(function (data) {
                var dialog = $("#dialog");
                dialog.html('<span class="red">JSON出力に失敗しました。</span>');
                dialog.click();
            });
        });
    });
</script>
<?php $this->MyHtml->scriptEnd(); ?>
