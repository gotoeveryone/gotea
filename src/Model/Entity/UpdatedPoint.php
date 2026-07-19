<?php
declare(strict_types=1);

namespace Gotea\Model\Entity;

/**
 * 成績更新エンティティ
 */
class UpdatedPoint extends AppEntity
{
    use CountryTrait;

    /**
     * @inheritDoc
     */
    protected array $_accessible = [
        'id' => false,
        'country_id' => true,
        'target_year' => true,
        'score_updated' => true,
        'created' => false,
        'created_by' => false,
        'modified' => false,
        'modified_by' => false,
    ];
}
