<?php

namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * LOG出力用コンポーネント
 *
 * @author      Kazuki Kamizuru
 * @since       2016/12/25
 */
class LogComponent extends Component
{
    /**
     * ログを出力します。
     *
     * @param type $name
     * @param type $arguments
     */
    public function __call($name, $arguments)
    {
        if (!isset($arguments[1])) {
            $arguments[1] = [];
        }
        $arguments[1]['url'] = $this->request->here();
        $this->log($arguments[0], $name, $arguments);
    }
}
