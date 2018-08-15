<?=
    $this->Form->control('country_id', [
        'type' => 'select',
        'options' => $countries,
        'data-id' => 'country',
    ] + $attributes);
?>
