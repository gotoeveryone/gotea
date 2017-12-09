<?php

namespace Gotea\View\Widget;

use Cake\View\Widget\DateTimeWidget as BaseDateTimeWidget;

/**
 * `datatime`のカスタムウィジェット
 */
class DateTimeWidget extends BaseDateTimeWidget
{
    /**
     * {@inheritDoc}
     */
    protected function _yearSelect($options, $context)
    {
        $options += [
            'name' => '',
            'val' => null,
            'start' => date('Y', strtotime('-5 years')),
            'end' => date('Y', strtotime('+5 years')),
            'order' => 'desc',
            'templateVars' => [],
            'options' => []
        ];

        if (!empty($options['val'])) {
            $options['start'] = min($options['val'], $options['start']);
            $options['end'] = max($options['val'], $options['end']);
        }
        if (empty($options['options'])) {
            $options['options'] = $this->_generateNumbers($options['start'], $options['end'], $options);
        }
        if ($options['order'] === 'desc') {
            $options['options'] = array_reverse($options['options'], true);
        }
        unset($options['start'], $options['end'], $options['order']);

        return $this->_select->render($options, $context);
    }

    /**
     * {@inheritDoc}
     */
    protected function _generateNumbers($start, $end, $options = [])
    {
        $numbers = parent::_generateNumbers($start, $end, $options);

        // サフィックスがあればテキストに付与
        if (($suffix = $options['suffix'] ?? '')) {
            foreach ($numbers as $key => $value) {
                $numbers[$key] = $value . $suffix;
            }
        }

        return $numbers;
    }
}
