<?php

namespace Api\Chart\Result\Element;

/**
 * Class \Api\Chart\Systems\Element\Collection
 */
class Collection extends \Api\Core\Base\Collection
{

    public function getByKey(mixed $strKey): ?Entity
    {
        return parent::getByKey($strKey);
    }

    public function getHasResultSystems(): \Api\Chart\Systems\Element\Collection
    {

        $obSystemsCollection = new \Api\Chart\Systems\Element\Collection();

        /** @var Entity $obResult */
        foreach ($this->getCollection() as $obResult) {
            if ($obResult->getResult() > 0.0) {
                $obSystemsCollection->addItem($obResult->getSystem());
            }
        }
        return $obSystemsCollection;
    }

}
