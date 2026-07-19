<?php
declare(strict_types=1);

namespace Gotea\Model\Entity;

/**
 * PlayerRank Entity
 *
 * @property int $id
 * @property int $player_id
 * @property int $rank_id
 * @property \Cake\I18n\FrozenDate $promoted
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \Gotea\Model\Entity\Player $player
 * @property \Gotea\Model\Entity\Rank $rank
 */
class PlayerRank extends AppEntity
{
    use PlayerTrait;
    use RankTrait;

    /**
     * @inheritDoc
     */
    protected array $_accessible = [
        'id' => false,
        'player_id' => true,
        'rank_id' => true,
        'promoted' => true,
        'created' => false,
        'modified' => false,
    ];
}
