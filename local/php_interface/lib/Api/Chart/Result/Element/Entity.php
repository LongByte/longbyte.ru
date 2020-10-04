<?php

namespace Api\Chart\Result\Element;

/**
 * Class \Api\Chart\Result\Element\Entity
 * 
 * @method int getId()
 * @method string getName()
 * @method $this setName(string $strName)
 * @method mixed getInfo()
 * @method $this setInfo(mixed $mixedInfo)
 * @method mixed getTestTypeId()
 * @method mixed getTestId()
 * @method $this setTestId(mixed $mixedTestId)
 * @method mixed getSystemId()
 * @method $this setSystemId(mixed $mixedSystemId)
 * @method mixed getResult()
 * @method $this setResult(mixed $mixedResult)
 * @method mixed getResult2()
 * @method $this setResult2(mixed $mixedResult2)
 * @method mixed getResult3()
 * @method $this setResult3(mixed $mixedResult3)
 */
class Entity extends \Api\Core\Iblock\Element\Entity {

    /**
     *
     * @var \Api\Chart\Systems\Element\Entity
     */
    protected $obSystem = null;

    /**
     *
     * @var \Api\Chart\Tests\Element\Entity
     */
    protected $obTest = null;

    public static function getModel() {
        return Model::class;
    }

    /**
     * 
     * @return \Api\Chart\Systems\Element\Entity
     */
    public function getSystem() {
        return $this->obSystem;
    }

    /**
     * 
     * @param \Api\Chart\Tests\Element\Entity $obTest
     */
    public function setTest(\Api\Chart\Tests\Element\Entity $obTest) {
        $this->obTest = $obTest;
        return $this;
    }

    /**
     * 
     * @param \Api\Chart\Systems\Element\Entity $obSystem
     */
    public function setSystem(\Api\Chart\Systems\Element\Entity $obSystem) {
        $this->obSystem = $obSystem;
        return $this;
    }

}
