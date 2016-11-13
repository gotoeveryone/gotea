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
<section class="searchHeader categories">
    <section class="searchRow">
        <section class="label">対象国：</section>
        <section>
            <?=
                $this->Form->input("selectCountry", [
                    "id" => "selectCountry", "options" => $countries, "class" => "country"
                ]);
            ?>
        </section>
        <section class="right">
            <!-- 検索ボタン -->
            <?=
                $this->Form->button("検索", [
                    "type" => "button", "id" => "search", "data-categories" => "ranks"
                ]);
            ?>
        </section>
    </section>
</section>

<table class="searchView ranks">
    <thead>
        <tr>
            <th class="rank">段位</th>
            <th class="count">人数</th>
        </tr>
    </thead>
</table>
<?=$this->Form->end()?>
<script type="text/javascript">
$(function() {
    // 検索ボタン押下時
    $('#search').click(function() {
        var target = $(this).attr("data-categories");
        $.ajax({
            type: "GET",
            url: "<?=$this->Url->build(['controller' => 'api', 'action' => 'categorize'])?>/" + $('#selectCountry option:selected').text() + "/",
            contentType: "application/json",
            accepts: "application/json; charset=utf-8"
        }).done(function (data) {
            data = data.response;
            // TBODY要素を削除
            $("." + target + " tbody").remove();

            var tbody = $('<tbody>');
            $.each(data.categories, function(idx, obj) {
                // TR要素を作成
                var tr = $('<tr>')
                    .append($('<td>', {class: 'rank'}).html(obj.rank.name))
                    .append($('<td>', {class: 'count'}).html(obj.count + "人"));
                // TBODY要素にTR要素を追加
                tbody.append(tr);
            });

            // TBODY要素を追加
            $("." + target + " thead").after(tbody);
        }).fail(function (data) {
            var dialog = $("#dialog");
            dialog.html('<span class="red">データの取得に失敗しました。</span>');
            dialog.click();
        });
    });
});
</script>
