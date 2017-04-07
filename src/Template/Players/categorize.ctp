<section class="categories">
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
                <label>対象国：</label>
                <?=
                    $this->Form->select("selectCountry", $countries, [
                        "id" => "selectCountry", "class" => "country"
                    ]);
                ?>
                <section class="button-column">
                    <?=
                        $this->Form->button("検索", [
                            "type" => "button", "id" => "search", "data-categories" => "ranks"
                        ]);
                    ?>
                </section>
            </li>
        </ul>

        <div class="search-results">
            <ul class="table-header">
                <li class="table-row">
                    <span class="rank">段位</span>
                    <span class="count">人数</span>
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
            $.ajax({
                type: "GET",
                url: "<?=$this->Url->build(['controller' => 'api', 'action' => 'categorize'])?>/" + $('#selectCountry option:selected').text() + "/",
                contentType: "application/json",
                accepts: "application/json; charset=utf-8"
            }).done(function (data) {
                data = data.response;
                // ボディを削除
                $('.table-body').remove();

                var body = $('<ul>', {class: 'table-body'});
                $.each(data.categories, function(idx, obj) {
                    // 行を生成
                    var tr = $('<li>', {class: 'table-row'})
                        .append($('<span>', {class: 'rank'}).html(obj.rank.name))
                        .append($('<span>', {class: 'count'}).html(obj.count + "人"));
                    // 一覧に行を追加
                    body.append(tr);
                });

                // ボディを追加
                $(".table-header").after(body);
            }).fail(function (data) {
                var dialog = $("#dialog");
                dialog.html('<span class="red">データの取得に失敗しました。</span>');
                dialog.click();
            });
        });
    });
</script>
<?php $this->MyHtml->scriptEnd(); ?>
