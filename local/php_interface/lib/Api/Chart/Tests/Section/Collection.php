<?php

namespace Api\Chart\Tests\Section;

/**
 * Class \Api\Chart\Tests\Section\Collection
 */
class Collection extends \Api\Core\Base\Collection
{

    public function getByKey($strKey): ?Entity
    {
        return parent::getByKey($strKey);
    }

}
