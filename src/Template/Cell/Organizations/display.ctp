<?=
    $this->Form->select('organization_id', $organizations, [
        'value' => $value,
        'data-id' => 'organization',
        'class' => 'organization',
        'empty' => $empty,
    ]);
?>
