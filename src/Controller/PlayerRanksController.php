<?php

namespace Gotea\Controller;

use Cake\Utility\Hash;

/**
 * 昇段情報コントローラ
 *
 * @author      Kazuki Kamizuru
 * @since       2017/07/22
 *
 * @property \Gotea\Model\Table\PlayerRanksTable $PlayerRanks
 */
class PlayerRanksController extends AppController
{
    /**
     * 段位別棋士数表示
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        return $this->_renderWith("段位別棋士数表示");
    }

    /**
     * 登録処理
     *
     * @param int $playerId 棋士ID
     * @return \Cake\Http\Response|null
     */
    public function create(int $playerId)
    {
        $data = Hash::insert($this->request->getData(), 'player_id', $playerId);
        $rank = $this->PlayerRanks->newEntity($data);

        // 保存
        if (!$this->PlayerRanks->save($rank)) {
            $this->_setErrors(400, $rank->getErrors());
        } else {
            $this->_setMessages(__('The rank history is saved'));
        }

        return $this->redirect([
            '_name' => 'view_player',
            '?' => ['tab' => 'ranks'],
            $playerId,
        ]);
    }

    /**
     * 更新処理
     *
     * @param int $playerId 棋士ID
     * @param int $id 昇段情報ID
     * @return \Cake\Http\Response|null
     */
    public function update(int $playerId, int $id)
    {
        $data = Hash::insert($this->request->getData(), 'player_id', $playerId);
        $rank = $this->PlayerRanks->get($id);
        $this->PlayerRanks->patchEntity($rank, $data);

        // 保存
        if (!$this->PlayerRanks->save($rank)) {
            $this->_setErrors(400, $rank->getErrors());
        } else {
            $this->_setMessages(__('The rank history is saved'));
        }

        return $this->redirect([
            '_name' => 'view_player',
            '?' => ['tab' => 'ranks'],
            $playerId,
        ]);
    }
}
