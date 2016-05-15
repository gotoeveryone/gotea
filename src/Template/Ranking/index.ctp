<?=$this->Form->create(null, [
    'id' => 'mainForm',
    'method' => 'post',
    'action' => 'index',
    'templates' => [
        'inputContainer' => '{{content}}',
        'textFormGroup' => '{{input}}',
        'selectFormGroup' => '{{input}}'
    ]
])?>
<table class="rankingHeader">
		<tr class="left">
			<td class="rankingColumn1">対象年度：</td>
			<td colspan="2">
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
			</td>
		</tr>
		<tr>
			<td class="rankingColumn1">最終更新日：</td>
			<td>
				<span class="lastUpdate"></span>
			</td>
			<td class="right">
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
			</td>
		</tr>
	</table>
	<div class="spacer"></div>

	<table class="searchView ranking">
		<thead>
			<tr>
				<th class="no">No.</th>
				<th class="playerName">棋士名</th>
				<th class="winPoint">勝</th>
				<th class="losePoint">敗</th>
				<th class="winPercent">勝率</th>
				<th class="space">&nbsp;</th>
			</tr>
		</thead>
	</table>
<?=$this->Form->end()?>
<script type="text/javascript">
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
            crossDomain: true,
            contentType: 'application/' + type,
            dataType: type,
            accepts: "application/json; charset=utf-8",
            success: function (data) {
                data = data.response;
                // TBODY要素を削除
                $('.ranking tbody').remove();

                // JSON出力ボタンを活性状態に変更
                $('#outputJson').removeAttr('disabled');

                // JSONから最終更新日を取得
                var lastUpdate = new Date(data.lastUpdate);
                $('.lastUpdate').html(lastUpdate.getFullYear() + '年'
                        + (lastUpdate.getMonth() + 1) + '月' + lastUpdate.getDate() + '日');

                var tbody = $('<tbody>');
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
                        record.playerNameJp = '<span class="red">' + record.playerNameJp + '</span>';
                    }

                    // TR要素を作成
                    var tr = $('<tr>')
                        .append($('<td>', {class: 'right no'}).html('<span class="rank">' + rank + '</span>'))
                        .append($('<td>', {class: 'left playerName'}).html(record.playerNameJp))
                        .append($('<td>', {class: 'center winPoint'}).html(record.winPoint))
                        .append($('<td>', {class: 'center losePoint'}).html(record.losePoint))
                        .append($('<td>', {class: 'center winPercent'}).html(record.winPercent));

                    // TBODY要素にTR要素を追加
                    tbody.append(tr);
                }
                // TBODY要素を追加
                $('.ranking thead').after(tbody);
            },
            error: function (data) {
                // JSON出力ボタンを非活性状態に変更し、エラーメッセージを出力
                $('#outputJson').attr('disabled', true);
                var dialog = $("#dialog");
                dialog.html('<span class="red">ランキングの生成に失敗しました。</span>');
                dialog.click();
            }
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
            success: function (data) {
                var dialog = $("#dialog");
                dialog.html('JSON出力に成功しました。');
                dialog.click();
            },
            error: function (data) {
                var dialog = $("#dialog");
                dialog.html('<span class="red">JSON出力に失敗しました。</span>');
                dialog.click();
            }
        });
    });
});
</script>
