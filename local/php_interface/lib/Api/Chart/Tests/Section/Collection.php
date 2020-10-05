<?php

namespace Api\Chart\Tests\Section;

/**
 * Class \Api\Chart\Tests\Section\Collection
 */
class Collection extends \Api\Core\Base\Collection {

    /**
     * 
     * @param type $strKey
     * @return Entity
     */
    public function getByKey($strKey): ?Entity {
        return parent::getByKey($strKey);
    }

}
