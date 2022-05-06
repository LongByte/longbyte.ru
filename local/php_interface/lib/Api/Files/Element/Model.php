<?php

namespace Api\Files\Element;

/**
 * Class \Api\Files\Element\Model
 */
class Model extends \Api\Core\Iblock\Element\Model
{

    protected static int $_iblockId = IBLOCK_FILES_FILES;

    public static function getEntity(): string
    {
        return Entity::class;
    }

}
