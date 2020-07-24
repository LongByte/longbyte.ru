<?php

namespace Api\Chart\Tests\Section;

/**
 * Class \Api\Chart\Tests\Section\Model
 */
class Model extends \Api\Core\Iblock\Section\Model {

    /**
     * @var int
     */
    protected static $_iblockId = IBLOCK_CHART_TESTS;

    public static function getEntity() {
        return Entity::class;
    }

}
