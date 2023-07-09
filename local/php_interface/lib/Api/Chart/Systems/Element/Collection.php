<?php

namespace Api\Chart\Systems\Element;

/**
 * Class \Api\Chart\Systems\Element\Collection
 */
class Collection extends \Api\Core\Base\Collection
{

    public function getByKey(mixed $strKey): ?Entity
    {
        return parent::getByKey($strKey);
    }

}
