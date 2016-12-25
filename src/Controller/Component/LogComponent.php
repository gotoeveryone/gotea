<?php

namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * LOG出力用コンポーネント
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
        $this->log($arguments[0], $name);
    }
}
