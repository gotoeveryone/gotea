<?=
    $this->Form->select('rank_id', $ranks, [
        'value' => $value,
        'data-id' => 'rank',
        'class' => 'rank',
        'empty' => $empty,
    ]);
?>
