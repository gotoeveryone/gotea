<?php
    $options = [
        'data-id' => 'country',
        'class' => 'country',
        'empty' => true,
    ];

    foreach ($customOptions as $key => $value) {
        $options[$key] = $value;
    }

    echo $this->Form->select('country_id', $countries, $options);
?>
