<?php

namespace Api\Sensors\Alert;

/**
 * Class \Api\Sensors\Alert\Collection
 */
class Collection extends \Api\Core\Base\Collection
{
    public function getByKey(mixed $strKey): ?Entity
    {
        return parent::getByKey($strKey);
    }
}