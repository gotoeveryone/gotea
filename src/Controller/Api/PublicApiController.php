<?php
declare(strict_types=1);

namespace Gotea\Controller\Api;

use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Gotea\Collection\Iterator\NewsIterator;
use Gotea\Collection\Iterator\PublicTitleIterator;
use Gotea\Collection\Iterator\RankingIterator;
use Gotea\Model\Table\NotificationsTable;
use Gotea\Model\Table\TitlesTable;
use Throwable;

/**
 * Go to Everyone! 公開 API コントローラ。
 *
 * @property \Gotea\Model\Table\TitlesTable $Titles
 * @property \Gotea\Model\Table\NotificationsTable $Notifications
 */
class PublicApiController extends ApiController
{
    protected TitlesTable $Titles;

    protected NotificationsTable $Notifications;

    /**
     * @inheritDoc
     */
    protected array $publicActions = ['titles', 'title', 'notifications', 'ranking'];

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Titles = $this->fetchTable('Titles');
        $this->Notifications = $this->fetchTable('Notifications');
    }

    /**
     * 公開 API のアクセスキーを検証します。
     *
     * @param \Cake\Event\EventInterface $event イベント
     * @return \Cake\Http\Response|null
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        parent::beforeFilter($event);

        if ($this->isAuthorizedPublicRequest()) {
            return null;
        }

        $response = $this->renderError(401, 'Unauthorized');
        $event->setResult($response);
        $event->stopPropagation();

        return $response;
    }

    /**
     * タイトル一覧を取得します。
     *
     * @return \Cake\Http\Response
     */
    public function titles(): Response
    {
        $titles = $this->Titles->findTitles(['search_closed' => true])
            ->all()
            ->map(new NewsIterator());

        return $this->renderJson($titles);
    }

    /**
     * タイトル詳細を取得します。
     *
     * @param int $id タイトル ID
     * @return \Cake\Http\Response
     */
    public function title(int $id): Response
    {
        $title = $this->Titles->findPublicByIdWithRelation($id);
        if (!$title) {
            return $this->renderError(404);
        }

        return $this->renderJson((new PublicTitleIterator())($title, 0));
    }

    /**
     * 公開済みのお知らせを新しい順に5件取得します。
     *
     * @return \Cake\Http\Response
     */
    public function notifications(): Response
    {
        $notifications = $this->Notifications->find()
            ->where([
                'Notifications.is_draft' => false,
                'Notifications.published <=' => FrozenTime::now(),
            ])
            ->orderBy([
                'Notifications.published' => 'DESC',
                'Notifications.id' => 'DESC',
            ])
            ->limit(5)
            ->all()
            ->map(function ($item): array {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'content' => $item->content,
                    'published' => $item->published?->format('Y-m-d H:i:s'),
                    'isPermanent' => $item->is_permanent,
                ];
            })
            ->toList();

        return $this->renderJson($notifications);
    }

    /**
     * ランキングを取得します。
     *
     * @param string $country 所属国コード
     * @param int $year 対象年度
     * @param int $limit 取得上限値
     * @return \Cake\Http\Response
     */
    public function ranking(string $country, int $year, int $limit): Response
    {
        $request = $this->getRequest();
        $from = $request->getQuery('from');
        $to = $request->getQuery('to');
        $type = $request->getQuery('type', 'point');

        if (
            ($from !== null && !is_string($from))
            || ($to !== null && !is_string($to))
            || !is_string($type)
            || !$this->isValidRankingParameters($year, $limit, $from, $to, $type)
        ) {
            return $this->renderError(400, 'Invalid ranking parameters');
        }

        try {
            $data = $this->fetchTable('TitleScoreDetails')->findRankingData([
                'country' => $country,
                'year' => $year,
                'limit' => $limit,
                'from' => $from,
                'to' => $to,
                'type' => $type,
            ]);
        } catch (Throwable) {
            return $this->renderError(400, 'Invalid ranking parameters');
        }

        if (!$data) {
            return $this->renderError(404);
        }

        $players = $data['players']->mapRanking(
            $data['country']->isWorlds(),
            true,
            $data['type'],
        );

        return $this->renderJson([
            'countryCode' => $data['country']->code,
            'countryName' => $data['country']->name_english,
            'year' => $data['year'],
            'lastUpdate' => $data['lastUpdate'],
            'count' => iterator_count($players),
            'ranking' => $players->map(new RankingIterator()),
        ]);
    }

    /**
     * 公開 API のキーが正しいか判定します。
     *
     * @return bool
     */
    private function isAuthorizedPublicRequest(): bool
    {
        $configuredKey = (string)Configure::read('App.publicApi.key', '');
        if ($configuredKey === '') {
            return false;
        }

        $authorization = $this->getRequest()->getHeaderLine('Authorization');
        if (!preg_match('/^Bearer\s+(.+)$/i', $authorization, $matches)) {
            return false;
        }

        return hash_equals($configuredKey, $matches[1]);
    }

    /**
     * ランキングパラメータを検証します。
     *
     * @param int $year 年
     * @param int $limit 上限
     * @param string|null $from 開始日
     * @param string|null $to 終了日
     * @param string|null $type 集計種別
     * @return bool
     */
    private function isValidRankingParameters(
        int $year,
        int $limit,
        ?string $from,
        ?string $to,
        ?string $type,
    ): bool {
        if ($year < 1 || $limit < 1 || !in_array($type, ['point', 'percent'], true)) {
            return false;
        }

        try {
            $fromDate = $from ? FrozenDate::parse($from) : FrozenDate::create($year, 1, 1);
            $toDate = $to ? FrozenDate::parse($to) : FrozenDate::create($year, 12, 31);
        } catch (Throwable) {
            return false;
        }

        return $fromDate <= $toDate;
    }
}
