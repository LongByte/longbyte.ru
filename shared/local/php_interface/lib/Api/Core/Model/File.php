<?php

namespace Api\Core\Model;

/**
 * Class \Api\Core\Model\File
 */
class File extends Base {

    public static function getEntity() {
        return \Api\Core\Entity\File::class;
    }

    public static function getTable() {
        return \Bitrix\Main\FileTable::class;
    }

}
