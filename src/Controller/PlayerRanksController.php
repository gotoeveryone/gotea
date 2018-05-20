<?php

namespace Gotea\Controller;

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
        return $this->renderWith("段位別棋士数表示");
    }

    /**
     * 登録処理
     *
     * @param int $playerId 棋士ID
     * @return \Cake\Http\Response|null
     */
    public function create(int $playerId)
    {
        $data = $this->getRequest()->withData('player_id', $playerId)->getParsedBody();
        $rank = $this->PlayerRanks->newEntity($data);

        // 保存
        if (!$this->PlayerRanks->save($rank)) {
            $this->setErrors(400, $rank->getErrors());
        } else {
            $this->setMessages(__('The rank history is saved'));
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
        $data = $this->getRequest()->withData('player_id', $playerId)->getParsedBody();
        $rank = $this->PlayerRanks->get($id);
        $this->PlayerRanks->patchEntity($rank, $data);

        // 保存
        if (!$this->PlayerRanks->save($rank)) {
            $this->setErrors(400, $rank->getErrors());
        } else {
            $this->setMessages(__('The rank history is saved'));
        }

        return $this->redirect([
            '_name' => 'view_player',
            '?' => ['tab' => 'ranks'],
            $playerId,
        ]);
    }
}
