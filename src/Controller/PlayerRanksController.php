<?php

namespace Gotea\Controller;

use Cake\Network\Exception\BadRequestException;

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
     * 登録処理
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function add()
    {
        // POST以外は許可しない
        $this->request->allowMethod(['post']);

        // タイトルIDが取得できなければエラー
        if (!($id = $this->request->getData('player_id'))) {
            throw new BadRequestException(__('棋士IDは必須です。'));
        }

        // バリデーション
        $ranks = $this->PlayerRanks->newEntity($this->request->getParsedBody());
        if (!$this->PlayerRanks->save($ranks)) {
            $this->_setErrors($ranks->errors());
        } else {
            $this->_setMessages(__("昇段情報を登録しました。"));
        }

        return $this->redirect([
            'controller' => 'Players',
            'action' => 'detail',
            '?' => ['tab' => 'ranks'],
            $id,
        ]);
    }
}
