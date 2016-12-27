<?php

namespace App\Log\Engine;

use Cake\Log\Engine\FileLog;

/**
 * 独自のログ出力クラス。
 * 
 * @author      Kazuki Kamizuru
 * @since       2016/12/28
 */
class MyFileLog extends FileLog
{
    /**
     * Converts to string the provided data so it can be logged. The context
     * can optionally be used by log engines to interpolate variables
     * or add additional info to the logged message.
     *
     * @param mixed $data The data to be converted to string and logged.
     * @param array $context Additional logging information for the message.
     * @return string
     */
    protected function _format($data, array $context = [])
    {
        $prefix = '';
        if (isset($context['scope'][1]['url'])) {
            $prefix = $context['scope'][1]['url'];
        }
        return $prefix.' '.parent::_format($data, $context);
    }
}
