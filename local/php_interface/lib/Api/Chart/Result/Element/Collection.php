<?php

namespace Api\Chart\Result\Element;

/**
 * Class \Api\Chart\Systems\Element\Collection
 */
class Collection extends \Api\Core\Base\Collection {

    /**
     * 
     * @param type $strKey
     * @return Entity
     */
    public function getByKey($strKey): ?Entity {
        return parent::getByKey($strKey);
    }

    /**
     * 
     * @return \Api\Chart\Systems\Element\Collection
     */
    public function getHasResultSystems(): \Api\Chart\Systems\Element\Collection {

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
