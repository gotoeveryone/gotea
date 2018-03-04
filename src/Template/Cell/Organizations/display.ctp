<?=
    $this->Form->control('organization_id', [
        'type' => 'select',
        'options' => $organizations,
        'data-id' => 'organization',
        'class' => 'organization',
    ] + $attributes);
?>
