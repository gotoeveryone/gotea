<?php

namespace Gotea\Controller\Api;

use Cake\I18n\FrozenDate;

/**
 * API・管理対象年コントローラ
 */
class YearsController extends ApiController
{
    /**
     * 管理対象年を取得します。
     *
     * @return \Cake\Http\Response 年一覧
     */
    public function index()
    {
        $nowYear = FrozenDate::now()->year;
        $years = [];
        for ($i = $nowYear; $i >= 2013; $i--) {
            $years[] = [
                'year' => $i,
                'old' => ($i < 2017),
            ];
        }

        return $this->_renderJson($years);
    }
}
