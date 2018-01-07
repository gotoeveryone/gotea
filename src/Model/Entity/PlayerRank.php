<?php
namespace Gotea\Model\Entity;

use Cake\ORM\Entity;

/**
 * PlayerRank Entity
 *
 * @property int $id
 * @property int $player_id
 * @property int $rank_id
 * @property \Cake\I18n\Date $promoted
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 *
 * @property \Gotea\Model\Entity\Player $player
 * @property \Gotea\Model\Entity\Rank $rank
 */
class PlayerRank extends AppEntity
{
    use PlayerTrait;
    use RankTrait;
}
