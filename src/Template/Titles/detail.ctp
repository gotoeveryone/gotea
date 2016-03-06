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
    'action' => 'detail',
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

    <section id="detail">
        <table class="detail">
            <tr class="headerRow1">
                <td colspan="4">
                    <?='タイトル情報（ID：'.h($title->ID).'）'?>
                </td>
            </tr>
            <tr>
                <td class="right detailColumn1">タイトル：</td>
                <td class="detailColumn2">
                    <?=$this->Form->hidden('selectTitleId', ['value' => $title->ID])?>
                    <?=h($title->TITLE_NAME)?>
                </td>
                <td class="right detailColumn1">分類：</td>
                <td class="detailColumn2">
                    <?=($title->country->COUNTRY_NAME).'棋戦'?>
                </td>
            </tr>
            <tr class="headerRow2">
                <td colspan="4">
                    新規登録
                </td>
            </tr>
            <tr>
                <td class="right detalColumn1">
                    対象年：
                </td>
                <td class="detailColumn2">
                    <?=
                        $this->Form->text('registYear', [
                            'id' => 'registYear',
                            'value' => $this->request->data('registYear'),
                            'maxlength' => 4,
                            'class' => 'imeDisabled targetYear'
                        ]);
                    ?>
                </td>
                <td class="right detailColumn1">
                    期：
                </td>
                <td class="detailColumn2">
                    <?=
                        $this->Form->text('registHolding', [
                            'id' => 'registHolding',
                            'value' => $this->request->data('registHolding'),
                            'maxlength' => 3,
                            'class' => 'imeDisabled targetHolding'
                        ]);
                    ?>
                </td>
            </tr>
            <tr>
                <td class="right detailColumn1">
                    <?=($title->GROUP_FLAG) ? '優勝団体名' : '棋士名：'?>
                </td>
                <td class="detailColumn2">
                    <?php
                        if ($title->GROUP_FLAG) {
                            echo $this->Form->text('registGroupName', [
                                'value' => $this->request->data('registGroupName'),
                                'maxlength' => 30
                            ]);
                        } else {
                            echo $this->Form->hidden('registPlayerId', ['id' => 'registPlayerId', 'value' => $this->request->data('registPlayerId')]);
                            echo $this->Form->text('registPlayerName', [
                                'id' => 'registPlayerName',
                                'value' => $this->request->data('registPlayerName'),
                                'tabindex' => -1,
                                'readonly' => true,
                                'style' => 'border-style: None; background-color: transparent; -moz-user-input: none; user-focus: none'
                            ]);
                            echo $this->Form->hidden('registPlayerName', ['id' => 'registPlayerName', 'value' => $this->request->data('registPlayerName')]);
                            echo $this->Form->text('registRankText', [
                                'id' => 'registRankText',
                                'value' => $this->request->data('registRankText'),
                                'tabindex' => -1,
                                'readonly' => true,
                                'style' => 'border-style: None; background-color: transparent; -moz-user-input: none; user-focus: none'
                            ]);
                            echo $this->Form->hidden('registRank', ['id' => 'registRank', 'value' => $this->request->data('registRank')]);
                        }
                    ?>
                </td>
                <td colspan="2" class="right">
                    <?php
                        if (!$title->GROUP_FLAG) {
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
                    ?>
                </td>
            </tr>
            <tr class="headerRow1">
                <td colspan="4">
                    保持情報
                </td>
            </tr>
            <?php if (!empty($title->title_retains)) { ?>
            <?php $beforeYear = ''; ?>
            <?php $header = false; ?>
            <?php foreach ($title->title_retains as $key=>$titleRetain) : ?>
                <?php if ($title->HOLDING === $titleRetain->HOLDING) { ?>
                <tr class="headerRow2">
                    <td colspan="4">
                        現在の保持情報
                    </td>
                </tr>
                <?php } else if (!$header) { ?>
                <tr class="headerRow2">
                    <td colspan="4">
                        保持情報（履歴）
                    </td>
                </tr>
                <?php $header = true; ?>
                <?php } ?>
                <?php if ($beforeYear !== $titleRetain->TARGET_YEAR) { ?>
                <tr class="headerRow3">
                    <td colspan="4">
                        <?=h($titleRetain->TARGET_YEAR).'年度'?>
                    </td>
                </tr>
                <?php $beforeTitle = $titleRetain->TARGET_YEAR; ?>
                <?php } ?>
                <tr>
                    <td class="right detailColumn1">
                        期：
                    </td>
                    <td class="detailColumn2">
                        <?=h($titleRetain->HOLDING)?>
                    </td>
                    <td class="right detailColumn1">
                        優勝者：
                    </td>
                    <td class="detailColumn2">
                        <?php
                            if ($title->GROUP_FLAG) {
                                echo h($titleRetain->WIN_GROUP_NAME);
                            } else {
                                echo h($titleRetain->player->PLAYER_NAME).' '.h($titleRetain->rank->RANK_NAME);
                            }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="right detailColumn1">更新日時：</td>
                    <td colspan="4">
                        <?=$this->Date->formatToDateTime($titleRetain->MODIFIED)?>
                    </td>
                </tr>
            <?php endforeach ?>
            <?php }?>
        </table>
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
            <?php
                if ($title->GROUP_FLAG) {
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
        });
        // 新規登録ボタンの制御
        function controlDisabled() {
            $('#regist').attr('disabled', !($('#registYear').val() !== '' && $('#registHolding').val() !== ''));
        }
    });
</script>