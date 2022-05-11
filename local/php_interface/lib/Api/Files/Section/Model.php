<?php

namespace Api\Files\Section;

/**
 * Class \Api\Files\Section\Model
 */
class Model extends \Api\Core\Iblock\Section\Model
{

    protected static int $_iblockId = IBLOCK_FILES_FILES;

    public static function getEntity(): string
    {
        return Entity::class;
    }

}
