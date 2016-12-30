<article class="ranking">
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
        <section class="search-header">
            <section class="row">
                <section class="label">対象年度：</section>
                <section>
                    <?=
                        $this->Form->input('selectYear', [
                            'id' => 'selectYear',
                            'options' => $years,
                            'class' => 'ranking'
                        ]);
                    ?>
                    <?=
                        $this->Form->input('selectCountry', [
                            'id' => 'selectCountry',
                            'options' => $countries,
                            'class' => 'ranking'
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
                            ],
                            'class' => 'ranking'
                        ]);
                    ?>
                </section>
            </section>
            <section class="row">
                <section class="label">最終更新日：</section>
                <section>
                    <span class="lastUpdate"></span>
                </section>
                <section class="button-column">
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
                </section>
            </section>
        </section>

        <section class="search-results">
            <section class="table-header">
                <ul class="table-row">
                    <li class="no">No.</li>
                    <li class="player">棋士名</li>
                    <li class="point">勝</li>
                    <li class="point">敗</li>
                    <li class="point">分</li>
                    <li class="percent">勝率</li>
                    <li></li>
                </ul>
            </section>
        </section>
    <?=$this->Form->end()?>
</article>

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
                // TBODY要素を削除
                $('.search-results .table-body').remove();

                // JSON出力ボタンを活性状態に変更
                $('#outputJson').removeAttr('disabled');

                // JSONから最終更新日を取得
                var lastUpdate = new Date(data.lastUpdate);
                $('.lastUpdate').html(lastUpdate.getFullYear() + '年'
                        + (lastUpdate.getMonth() + 1) + '月' + lastUpdate.getDate() + '日');

                var body = $('<section>', {class: "table-body"});
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

                    // TR要素を作成
                    var tr = $('<ul>', {class: 'table-row'})
                        .append($('<li>', {class: 'right no'}).html('<span class="rank">' + rank + '</span>'))
                        .append($('<li>', {class: 'left player'}).html($('<a>', {"class": "colorbox", "href": "/IgoApp/players/detail/" + record.playerId}).html(record.playerNameJp)))
                        .append($('<li>', {class: 'point'}).html(record.winPoint))
                        .append($('<li>', {class: 'point'}).html(record.losePoint))
                        .append($('<li>', {class: 'point'}).html(record.drawPoint))
                        .append($('<li>', {class: 'percent'}).html(Math.round(record.winPercentage * 100) + '%'));

                    // 一覧に行を追加
                    body.append(tr);
                }
                // TBODY要素を追加
                $('.search-results .table-header').after(body);
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
