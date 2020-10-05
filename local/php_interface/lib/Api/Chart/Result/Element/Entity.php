<?php

namespace Api\Chart\Result\Element;

/**
 * Class \Api\Chart\Result\Element\Entity
 * 
 * @method int getId()
 * @method string getName()
 * @method mixed getPreviewText()
 * @method mixed getTestId()
 * @method mixed getSystemId()
 * @method mixed getResult()
 * @method mixed getResult2()
 * @method mixed getResult3()
 */
class Entity extends \Api\Core\Iblock\Element\Entity {

    /**
     *
     * @var array
     */
    protected static $arFields = array(
        'ID',
        'NAME',
        'PREVIEW_TEXT',
    );

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

    /**
     * 
     * @return string
     */
    public static function getModel(): string {
        return Model::class;
    }

    /**
     * 
     * @return string
     */
    public static function getCollection(): string {
        return Collection::class;
    }

    /**
     * 
     * @return \Api\Chart\Tests\Element\Entity
     */
    public function getTest(): ?\Api\Chart\Tests\Element\Entity {
        return $this->obTest;
    }

    /**
     * 
     * @param \Api\Chart\Tests\Element\Entity $obTest
     * @return \self
     */
    public function setTest(\Api\Chart\Tests\Element\Entity $obTest): self {
        $this->obTest = $obTest;
        return $this;
    }

    /**
     * 
     * @return \Api\Chart\Systems\Element\Entity
     */
    public function getSystem(): ?\Api\Chart\Systems\Element\Entity {
        return $this->obSystem;
    }

    /**
     * 
     * @param \Api\Chart\Systems\Element\Entity $obSystem
     * @return \self
     */
    public function setSystem(\Api\Chart\Systems\Element\Entity $obSystem): self {
        $this->obSystem = $obSystem;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getFullName(): string {

        $strName = '';
        $strName .= '<span';
        if (!empty($this->getPreviewText())) {
            $strName .= ' title="' . nl2br($this->getPreviewText()) . '"';
        }
        $strName .= '>';
        $strName .= $this->getSystem()->getFullName($this->getTest()->getTestType());
        $strName .= '</span>';

        return $strName;
    }

    /**
     * 
     * @return string
     */
    public function getColor(): string {
        $strColor = '127, 127, 127';
        $isActual = empty($this->getSystem()->getActualFor()) && $this->getSystem()->getActual() || !empty($this->getSystem()->getActualFor()) && in_array($this->getTest()->getTestType()->getId(), $this->getSystem()->getActualFor());
        if ($isActual) {

            switch ($this->getTest()->getTestType()->getCode()) {
                case 'GPU':
                    $strColor = $this->getSystem()->getGpuFirm()->getActiveColor();
                    break;
                case 'CPU':
                case 'RAM':
                    $strColor = $this->getSystem()->getCpuFirm()->getActiveColor();
                    break;
                case 'DRIVE':
                    $strColor = $this->getSystem()->getHdFirm()->getActiveColor();
                    break;
            }
        } else {

            switch ($this->getTest()->getTestType()->getCode()) {
                case 'GPU':
                    $strColor = $this->getSystem()->getGpuFirm()->getPassiveColor();
                    break;
                case 'CPU':
                case 'RAM':
                    $strColor = $this->getSystem()->getCpuFirm()->getPassiveColor();
                    break;
                case 'DRIVE':
                    $strColor = $this->getSystem()->getHdFirm()->getPassiveColor();
                    break;
            }
        }

        return $strColor;
    }

}
