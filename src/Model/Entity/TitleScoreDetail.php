<?php
namespace App\Model\Entity;

/**
 * TitleScoreDetail Entity
 *
 * @property int $id
 * @property int $title_score_id
 * @property int $player_id
 * @property string $division
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 *
 * @property \App\Model\Entity\TitleScore $title_score
 * @property \App\Model\Entity\Player $player
 */
class TitleScoreDetail extends AppEntity
{
}
