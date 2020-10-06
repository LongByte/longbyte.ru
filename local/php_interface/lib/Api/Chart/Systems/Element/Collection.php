<?php

namespace Api\Chart\Systems\Element;

/**
 * Class \Api\Chart\Systems\Element\Collection
 */
class Collection extends \Api\Core\Base\Collection {

    /**
     * 
     * @param type $strKey
     * @return \Api\Chart\Systems\Element\Entity|null
     */
    public function getByKey($strKey): ?Entity {
        return parent::getByKey($strKey);
    }

}
