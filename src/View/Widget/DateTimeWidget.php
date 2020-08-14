<?php
declare(strict_types=1);

namespace Gotea\View\Widget;

use Cake\Utility\Hash;
use Cake\View\Form\ContextInterface;
use Shim\View\Widget\DateTimeWidget as BaseDateTimeWidget;

/**
 * `datatime`のカスタムウィジェット
 */
class DateTimeWidget extends BaseDateTimeWidget
{
    /**
     * @inheritDoc
     */
    protected function _yearSelect(array $options, ContextInterface $context): string
    {
        $options += [
            'name' => '',
            'val' => null,
            'start' => date('Y', strtotime('-5 years')),
            'end' => date('Y', strtotime('+5 years')),
            'order' => 'desc',
            'templateVars' => [],
            'options' => [],
        ];

        if (!empty($options['val'])) {
            $options['start'] = min($options['val'], $options['start']);
            $options['end'] = max($options['val'], $options['end']);
        }
        if (empty($options['options'])) {
            $options['options'] = $this->_generateNumbers((int)$options['start'], (int)$options['end'], $options);
        }
        if ($options['order'] === 'desc') {
            $options['options'] = array_reverse($options['options'], true);
        }
        unset($options['start'], $options['end'], $options['order']);

        return $this->_select->render($options, $context);
    }

    /**
     * @inheritDoc
     */
    protected function _secondSelect(array $options, ContextInterface $context): string
    {
        $options += [
            'name' => '',
            'val' => null,
            'leadingZeroKey' => true,
            'leadingZeroValue' => true,
            'options' => $this->_generateNumbers(0, 59, $options),
            'templateVars' => [],
        ];

        unset($options['leadingZeroKey'], $options['leadingZeroValue']);

        return $this->_select->render($options, $context);
    }

    /**
     * @inheritDoc
     */
    protected function _generateNumbers(int $start, int $end, array $options = []): array
    {
        $numbers = parent::_generateNumbers($start, $end, $options);

        // サフィックスがあればテキストに付与
        $suffix = Hash::get($options, 'suffix');
        if ($suffix) {
            foreach ($numbers as $key => $value) {
                $numbers[$key] = $value . $suffix;
            }
        }

        return $numbers;
    }
}
