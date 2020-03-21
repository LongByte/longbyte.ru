<?php

namespace Api\Portfolio\Element;

/**
 * Class \Api\Portfolio\Element\Model
 */
class Model extends \Api\Core\Model\Element {

    /**
     * @var int
     */
    protected static $_iblockId = IBLOCK_MAIN_PORTFOLIO;

    public static function getEntity() {
        return Entity::class;
    }

    public static function getCollection() {
        return Collection::class;
    }

}
