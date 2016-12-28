<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Datasource\ConnectionManager;

/**
 * トランザクション管理用コンポーネント
 * 
 * @author  Kazuki Kamizuru
 * @since   2016/12/28
 */
class TransactionComponent extends Component
{
    public $components = ['Log'];

    /**
     * @var \
     */
    private $__conn = null;

    /**
     * @var bool
     */
    private $__isRollback = false;

    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        // コネクションを初期化
        $this->__conn = ConnectionManager::get('default');
    }

    /**
     * {@inheritdoc}
     */
    public function __call($name, $arguments)
    {
        // 指定処理のみログ出力
        switch ($name) {
            case 'commit':
                $this->Log->debug(__('トランザクションをコミットしました。'));
                break;
            case 'rollback':
                $this->Log->error(__('ロールバックがマークされたので、トランザクションをロールバックしました。'));
                break;
            default:
                break;
        }

        if (isset($arguments[0]) && is_callable($arguments[0])) {
            $func = $arguments[0];
            return $this->__conn->$name($func);
        }

        return $this->__conn->$name($arguments);
    }

    /**
     * コネクションを取得します。
     * 
     * @deprecated
     * @return \Cake\Datasource\ConnectionInterface
     */
    public function getConnection()
    {
        return $this->__conn;
    }

    /**
     * ロールバックをマークします。
     */
    public function markToRollback()
    {
        $this->__isRollback = true;
    }

    /**
     * コミット or ロールバックを判定
     * 
     * @return bool true on success, false otherwise
     */
    public function commitOrRollback()
    {
        return ($this->__isRollback ? $this->rollback() : $this->commit());
    }
}
