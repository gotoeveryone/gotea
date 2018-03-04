<?=
    $this->Form->control('country_id', [
        'type' => 'select',
        'options' => $countries,
        'data-id' => 'country',
        'class' => 'country',
    ] + $attributes);
?>
