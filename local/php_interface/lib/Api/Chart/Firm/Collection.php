<?php

namespace Api\Chart\Firm;

/**
 * Class \Api\Chart\Firm\Collection
 */
class Collection extends \Api\Core\Base\Collection
{

    public function getByKey(mixed $strKey): ?Entity
    {
        return parent::getByKey($strKey);
    }

}
