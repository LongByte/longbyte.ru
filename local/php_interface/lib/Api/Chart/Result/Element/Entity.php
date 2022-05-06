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
class Entity extends \Api\Core\Iblock\Element\Entity
{

    protected static array $arFields = array(
        'ID',
        'NAME',
        'PREVIEW_TEXT',
    );

    protected ?\Api\Chart\Systems\Element\Entity $obSystem = null;
    protected ?\Api\Chart\Tests\Element\Entity $obTest = null;

    public static function getModel(): string
    {
        return Model::class;
    }

    public static function getCollection(): string
    {
        return Collection::class;
    }

    public function getTest(): ?\Api\Chart\Tests\Element\Entity
    {
        return $this->obTest;
    }

    public function setTest(\Api\Chart\Tests\Element\Entity $obTest): self
    {
        $this->obTest = $obTest;
        return $this;
    }

    public function getSystem(): ?\Api\Chart\Systems\Element\Entity
    {
        return $this->obSystem;
    }

    public function setSystem(\Api\Chart\Systems\Element\Entity $obSystem): self
    {
        $this->obSystem = $obSystem;
        return $this;
    }

    public function getFullName(): string
    {

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

    public function getColor(): string
    {
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
