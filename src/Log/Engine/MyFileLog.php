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
     * {@inheritDoc}
     */
    protected function _format($data, array $context = [])
    {
        $prefix = '';
        if (isset($context['scope'][1]['url'])) {
            $prefix = $context['scope'][1]['url'];
        }
        return $prefix.' - '.parent::_format($data, $context);
    }
}
