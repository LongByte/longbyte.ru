<?php

namespace Api\Chart\Result\Section;

/**
 * Class \Api\Chart\Result\Section\Entity
 */
class Entity extends \Api\Core\Iblock\Section\Entity {

    /**
     * 
     * @return string
     */
    public static function getModel(): string {
        return Model::class;
    }

}
