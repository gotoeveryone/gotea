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
        return $this->_renderWith("段位別棋士数表示");
    }

    /**
     * 登録処理
     *
     * @param int $id 棋士ID
     * @return \Cake\Http\Response|null
     */
    public function create(int $id)
    {
        // バリデーション
        $rank = $this->PlayerRanks->newEntity($this->request->getParsedBody());
        $rank->player_id = $id;
        if (!$this->PlayerRanks->save($rank)) {
            $this->_setErrors(400, $rank->getErrors());
        } else {
            $this->_setMessages(__('昇段情報を登録しました。'));
        }

        return $this->redirect([
            '_name' => 'view_player',
            '?' => ['tab' => 'ranks'],
            $id,
        ]);
    }

    /**
     * 更新処理
     *
     * @param int $id 棋士ID
     * @param int $rowId 昇段情報ID
     * @return \Cake\Http\Response|null
     */
    public function update(int $id, int $rowId)
    {
        // バリデーション
        $rank = $this->PlayerRanks->get($rowId);
        $this->PlayerRanks->patchEntity($rank, $this->request->getParsedBody());
        if (!$this->PlayerRanks->save($rank)) {
            $this->_setErrors(400, $rank->getErrors());
        } else {
            $this->_setMessages(__('昇段情報を保存しました。'));
        }

        return $this->redirect([
            '_name' => 'view_player',
            '?' => ['tab' => 'ranks'],
            $id,
        ]);
    }
}
