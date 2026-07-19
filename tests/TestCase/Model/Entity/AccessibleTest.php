<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Model\Entity;

use Cake\TestSuite\TestCase;
use Gotea\Model\Entity\AppEntity;
use Gotea\Model\Entity\Country;
use Gotea\Model\Entity\Notification;
use Gotea\Model\Entity\Organization;
use Gotea\Model\Entity\Player;
use Gotea\Model\Entity\PlayerRank;
use Gotea\Model\Entity\PlayerScore;
use Gotea\Model\Entity\Rank;
use Gotea\Model\Entity\RetentionHistory;
use Gotea\Model\Entity\TableTemplate;
use Gotea\Model\Entity\Title;
use Gotea\Model\Entity\TitleScore;
use Gotea\Model\Entity\TitleScoreDetail;
use Gotea\Model\Entity\UpdatedPoint;
use Gotea\Model\Entity\User;

/**
 * Entity の mass assignment 設定テスト
 */
class AccessibleTest extends TestCase
{
    /**
     * Entity ごとの mass assignment 許可範囲
     *
     * @return void
     */
    public function testAccessibleFields(): void
    {
        $fields = [
            AppEntity::class => [
                'allowed' => [],
                'denied' => ['id', 'created', 'modified', 'newest'],
            ],
            Country::class => [
                'allowed' => ['code', 'name', 'name_english', 'has_title'],
                'denied' => ['id', 'created', 'modified'],
            ],
            Organization::class => [
                'allowed' => ['country_id', 'name'],
                'denied' => ['id', 'created', 'modified'],
            ],
            Player::class => [
                'allowed' => [
                    'country_id', 'rank_id', 'organization_id', 'name',
                    'name_english', 'name_other', 'sex', 'joined_year',
                    'joined_month', 'joined_day', 'birthday', 'remarks',
                    'is_retired', 'retired',
                ],
                'denied' => ['id', 'created', 'created_by', 'modified', 'modified_by'],
            ],
            PlayerRank::class => [
                'allowed' => ['player_id', 'rank_id', 'promoted'],
                'denied' => ['id', 'created', 'modified', 'newest'],
            ],
            PlayerScore::class => [
                'allowed' => [
                    'player_id', 'rank_id', 'target_year', 'win_point',
                    'lose_point', 'draw_point', 'win_point_world',
                    'lose_point_world', 'draw_point_world',
                ],
                'denied' => ['id', 'created', 'created_by', 'modified', 'modified_by'],
            ],
            Rank::class => [
                'allowed' => ['name', 'rank_numeric'],
                'denied' => ['id', 'created', 'modified'],
            ],
            RetentionHistory::class => [
                'allowed' => [
                    'title_id', 'player_id', 'country_id', 'holding',
                    'target_year', 'name', 'win_group_name', 'is_team',
                    'acquired', 'is_official', 'broadcasted',
                ],
                'denied' => ['id', 'created', 'modified', 'newest'],
            ],
            TableTemplate::class => [
                'allowed' => ['title', 'content'],
                'denied' => ['id', 'created', 'modified'],
            ],
            Title::class => [
                'allowed' => [
                    'country_id', 'name', 'name_english', 'holding',
                    'sort_order', 'html_file_name', 'html_file_holding',
                    'html_file_modified', 'remarks', 'is_team', 'is_closed',
                    'is_output', 'is_official',
                ],
                'denied' => ['id', 'created', 'created_by', 'modified', 'modified_by'],
            ],
            TitleScore::class => [
                'allowed' => [
                    'country_id', 'title_id', 'name', 'result', 'started',
                    'ended', 'is_world', 'is_official',
                ],
                'denied' => ['id', 'created', 'modified', 'title_score_details'],
            ],
            TitleScoreDetail::class => [
                'allowed' => ['title_score_id', 'player_id', 'player_name', 'division'],
                'denied' => ['id', 'created', 'modified', 'target_year'],
            ],
            UpdatedPoint::class => [
                'allowed' => ['country_id', 'target_year', 'score_updated'],
                'denied' => ['id', 'created', 'created_by', 'modified', 'modified_by'],
            ],
            Notification::class => [
                'allowed' => [
                    'title', 'content', 'is_draft', 'published', 'is_permanent',
                ],
                'denied' => ['id', 'created', 'modified', 'status'],
            ],
            User::class => [
                'allowed' => ['account', 'name', 'password'],
                'denied' => ['id', 'is_admin', 'last_logged', 'created', 'modified'],
            ],
        ];

        foreach ($fields as $class => $access) {
            $entity = new $class();
            foreach ($access['allowed'] as $field) {
                $this->assertTrue($entity->isAccessible($field), "$class::$field");
            }
            foreach ($access['denied'] as $field) {
                $this->assertFalse($entity->isAccessible($field), "$class::$field");
            }
        }
    }
}
