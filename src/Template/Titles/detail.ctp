<!-- 棋士検索用フォーム -->
<form id="searchPlayerForm" method="post" action="<?=$this->Url->build(['controller' => 'players', 'action' => 'index'])?>">
    <input type="hidden" name="searchCountry" value="<?=$this->request->data('searchCountry')?>">
    <input type="hidden" name="searchFlag" value="false">
    <input type="hidden" name="searchRetire" value="<?=$this->request->data('searchRetire')?>">
    <input type="hidden" name="dialogFlag" value="true">
</form>

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
    <!-- 検索画面の検索状態 -->
    <?=$this->Form->hidden('searchFlag', ['value' => $this->request->data('searchFlag')])?>
    <?=$this->Form->hidden('country', ['value' => $this->request->data('searchCountry')])?>
    <?=$this->Form->hidden('searchRetire', ['value' => $this->request->data('searchRetire')])?>
    <?=$this->Form->hidden('registWithMapping', ['id' => 'registWithMapping', 'value' => false])?>

    <section id="detail">
        <section id="tabs">
            <section class="tabs" name="title">タイトル情報</section>
            <section class="tabs" name="titleRetains">保持情報</section>
        </section>
        <section id="scroll">
            <section id="title" class="details">
                <section class="categoryRow">
                    <?='タイトル情報（ID：'.h($title->id).'）'?>
                </section>
                <section class="row">
                    <section class="box2">
                        <section class="headerRow">タイトル名</section>
                        <section class="valueRow">
                            <?=$this->Form->hidden('selectTitleId', ['value' => $title->id])?>
                            <?=h($title->name)?>
                        </section>
                    </section>
                    <section class="box2">
                        <section class="headerRow">タイトル名（英語）</section>
                        <section class="valueRow">
                            <?=h($title->name_english)?>
                        </section>
                    </section>
                    <section class="box2">
                        <section class="headerRow">分類</section>
                        <section class="valueRow">
                            <?=($title->country->name).'棋戦'?>
                        </section>
                    </section>
                    <section class="box2">
                        <section class="headerRow">更新日時</section>
                        <section class="valueRow">
                            <?=$this->Date->formatToDateTime($title->modified)?>
                            <?=
                                $this->Form->hidden('lastUpdateTitle', [
                                    'value' => $this->Date->format($title->modified, 'yyyyMMddHHmmss')
                                ])
                            ?>
                        </section>
                    </section>
                    <section class="box">
                        <section class="headerRow">現在の保持者</section>
                        <section class="valueRow">
                            <?php
                                if (!empty($title->arquisition_histories)) :
                                    foreach ($title->arquisition_histories as $arquisition) :
                                        if ($arquisition->holding === $title->holding) :
                                            echo h(__("{$title->holding}期 "));
                                            echo h($arquisition->getWinnerName($title->is_team));
                                            break;
                                        endif;
                                    endforeach;
                                endif;
                            ?>
                        </section>
                    </section>
                    <section class="row">
                        <section class="box">
                            <section class="headerRow">その他備考</section>
                            <section class="valueRow">
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
                </section>
            </section>
            <section id="titleRetains" class="details">
                <section class="categoryRow">保持情報</section>
                <section class="row">
                    <section class="box">
                        <section class="headerRow">新規登録</section>
                    </section>
                </section>
                <section class="row">
                    <section class="box2">
                        <section class="valueRow">対象年：
                            <?=
                                $this->Form->text('registYear', [
                                    'id' => 'registYear',
                                    'value' => $this->request->data('registYear'),
                                    'maxlength' => 4,
                                    'class' => 'imeDisabled targetYear'
                                ]);
                            ?>
                            期：
                            <?=
                                $this->Form->text('registHolding', [
                                    'id' => 'registHolding',
                                    'value' => $this->request->data('registHolding'),
                                    'maxlength' => 3,
                                    'class' => 'imeDisabled targetHolding'
                                ]);
                            ?>
                        </section>
                    </section>
                    <section class="box2">
                        <section class="valueRow">
                            <?=h($title->is_team ? '優勝団体名' : '棋士名：')?>
                            <?php
                                if ($title->is_team) {
                                    echo $this->Form->text('registGroupName', [
                                        'value' => $this->request->data('registGroupName'),
                                        'maxlength' => 30
                                    ]);
                                } else {
                                    echo $this->Form->hidden('registPlayerId', [
                                        'id' => 'registPlayerId',
                                        'value' => $this->request->data('registPlayerId')
                                    ]);
                                    echo $this->Form->text('registPlayerName', [
                                        'id' => 'registPlayerName',
                                        'value' => '',
                                        'tabindex' => -1,
                                        'readonly' => true,
                                        'class' => 'readonly playerName'
                                    ]);
                                    echo $this->Form->hidden('registPlayerName', [
                                        'id' => 'registPlayerName',
                                        'value' => $this->request->data('registPlayerName')
                                    ]);
                                    echo $this->Form->text('registRankText', [
                                        'id' => 'registRankText',
                                        'value' => '',
                                        'tabindex' => -1,
                                        'readonly' => true,
                                        'class' => 'readonly rank'
                                    ]);
                                    echo $this->Form->hidden('registRank', [
                                        'id' => 'registRank',
                                        'value' => $this->request->data('registRank')
                                    ]);
                                }
                            ?>
                        </section>
                    </section>
                </section>
                <section class="row">
                    <section class="box button">
                        <?php
                            if (!$title->is_team) {
                                echo $this->Form->button('棋士検索', [
                                    'type' => 'button',
                                    'id' => 'searchPlayer'
                                ]);
                            }
                            echo $this->Form->button('新規登録', [
                                'type' => 'button',
                                'id' => 'regist',
                                'disabled' => true
                            ]);
                            echo $this->Form->button('最新を登録', [
                                'type' => 'button',
                                'id' => 'registNew',
                                'disabled' => true
                            ]);
                        ?>
                    </section>
                </section>
                <?php if (!empty($title->arquisition_histories)) : ?>
                    <?php foreach ($title->arquisition_histories as $arquisition) : ?>
                        <?php if ($title->holding === $arquisition->holding) : ?>
                        <section class="row">
                            <section class="box">
                                <section class="headerRow">現在の保持情報</section>
                            </section>
                        </section>
                        <section class="row">
                            <section class="box">
                                <section class="valueRow">
                                    <?=h(__("{$arquisition->target_year}年 {$arquisition->holding}期 "))?>
                                    <?=h($arquisition->getWinnerName($title->is_team))?>
                                </section>
                            </section>
                        </section>
                        <?php break ?>
                        <?php endif ?>
                    <?php endforeach ?>
                    <?php $header = false; ?>
                    <?php foreach ($title->arquisition_histories as $arquisition) : ?>
                        <?php if (!$header) : ?>
                        <section class="row">
                            <section class="box">
                                <section class="headerRow">保持情報（履歴）</section>
                            </section>
                        </section>
                        <?php $header = true; ?>
                        <?php endif ?>
                        <?php if ($title->holding !== $arquisition->holding) : ?>
                        <section class="row">
                            <section class="box">
                                <section class="valueRow">
                                    <?=h(__("{$arquisition->target_year}年 {$arquisition->holding}期 "))?>
                                    <?=h($arquisition->getWinnerName($title->is_team))?>
                                </section>
                            </section>
                        </section>
                        <?php endif ?>
                    <?php endforeach ?>
                <?php endif ?>
            </section>
        </section>
    </section>
<?=$this->Form->end()?>
<script type="text/javascript">
    $(document).ready(function() {
        controlDisabled();
        // 棋士検索ボタン押下時
        $('#searchPlayer').click(function() {
            w = window.open('<?=$this->Url->build(['controller' => 'player'])?>', 'Search', 'width=900, height=700, menubar=no, toolbar=no, status=no,location=no, scrollbars=no, resizable=no');
            var playerForm = $('#searchPlayerForm');
            playerForm.attr('target', 'Search');
            playerForm.submit();
            w.focus();
        });
        // 登録エリアの対象年、期フォーカスアウト時
        $('#registYear, #registHolding').blur(function() {
            controlDisabled();
        });
        // 新規登録ボタン押下時
        $('#regist').click(function() {
            $('#registWithMapping').val(false);
            regist();
        });
        // 最新を登録ボタン押下時
        $('#registNew').click(function() {
            $('#registWithMapping').val(true);
            regist();
        });
        function regist() {
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
            var confirm = $("#confirm");
            confirm.html('タイトル保持情報を登録します。よろしいですか？');
            $('#mainForm').attr('action', '<?=$this->Url->build(['action' => 'regist'])?>');
            confirm.click();
        }
        // 新規登録ボタンの制御
        function controlDisabled() {
            $('#regist, #registNew').attr('disabled', !($('#registYear').val() !== '' && $('#registHolding').val() !== ''));
        }
    });
</script>