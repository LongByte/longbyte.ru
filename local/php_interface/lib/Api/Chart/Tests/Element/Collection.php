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
    public function getByKey($strKey) {
        return parent::getByKey($strKey);
    }

    /**
     * 
     * @return \Api\Chart\Systems\Element\Collection
     */
    public function getFilterCollection() {
        $obCollection = new \Api\Chart\Systems\Element\Collection();

        /** @var \Api\Chart\Tests\Element\Entity $obTest */
        foreach ($this->getCollection() as $obTest) {
            $obSystemsCollection = $obTest->getResults()->getHasResultSystems();
            /** @var \Api\Chart\Systems\Element\Entity $obSystem */
            foreach ($obSystemsCollection as $obSystem) {
                $obCollection->addItem($obSystem);
            }
        }
        usort($obCollection->getCollection(), function($obSystem1, $obSystem2) {
            /** @var \Api\Chart\Systems\Element\Entity $obSystem1 */
            /** @var \Api\Chart\Systems\Element\Entity $obSystem2 */
            return $obSystem1->getName() <=> $obSystem2->getName();
        });

        return $obCollection;
    }

}
