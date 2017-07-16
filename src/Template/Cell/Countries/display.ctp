<?=
    $this->Form->select('country_id', $countries, [
        'data-id' => 'country',
        'class' => 'country',
        'empty' => true,
    ]);
?>
