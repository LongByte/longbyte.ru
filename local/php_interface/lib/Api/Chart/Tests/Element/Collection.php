<?php

namespace Api\Chart\Tests\Element;

/**
 * Class \Api\Chart\Tests\Element\Collection
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
    public function getFilterCollection(): \Api\Chart\Systems\Element\Collection {
        $obCollection = new \Api\Chart\Systems\Element\Collection();

        /** @var \Api\Chart\Tests\Element\Entity $obTest */
        foreach ($this->getCollection() as $obTest) {
            $obSystemsCollection = $obTest->getResults()->getHasResultSystems();
            /** @var \Api\Chart\Systems\Element\Entity $obSystem */
            foreach ($obSystemsCollection as $obSystem) {
                if (!$obCollection->getByKey($obSystem->getId())) {
                    $obCollection->addItem($obSystem);
                }
            }
        }

        $arSortCollection = $obCollection->getCollection();

        usort($arSortCollection, function($obSystem1, $obSystem2) use ($obTest) {
            /** @var \Api\Chart\Systems\Element\Entity $obSystem1 */
            /** @var \Api\Chart\Systems\Element\Entity $obSystem2 */
            return $obSystem1->getClearFullName($obTest->getTestType()) <=> $obSystem2->getClearFullName($obTest->getTestType());
        });

        $obCollection = new \Api\Chart\Systems\Element\Collection();
        foreach ($arSortCollection as $obSystem) {
            $obCollection->addItem($obSystem);
        }

        return $obCollection;
    }

}
