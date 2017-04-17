<section class="categories" categorize></section>

<?=$this->Html->script('categorize.min', ['inline' => false])?>

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
