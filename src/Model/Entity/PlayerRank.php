<?php
namespace App\Model\Entity;

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
 * @property \App\Model\Entity\Player $player
 * @property \App\Model\Entity\Rank $rank
 */
class PlayerRank extends AppEntity
{
}
