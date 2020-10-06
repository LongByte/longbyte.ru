<?php

namespace Api\WikiApi\Element;

/**
 * Class \Api\WikiApi\Element\Entity
 * 
 * @method mixed getId()
 * @method $this setId(mixed $mixedId)
 * @method bool hasId()
 * @method mixed getName()
 * @method $this setName(mixed $mixedName)
 * @method bool hasName()
 * @method mixed getPreviewText()
 * @method $this setPreviewText(mixed $mixedPreviewText)
 * @method bool hasPreviewText()
 * @method mixed getDetailText()
 * @method $this setDetailText(mixed $mixedDetailText)
 * @method bool hasDetailText()
 * @method mixed getDetailPageUrl()
 * @method $this setDetailPageUrl(mixed $mixedDetailPageUrl)
 * @method bool hasDetailPageUrl()
 * @method mixed getType()
 * @method $this setType(mixed $mixedType)
 * @method bool hasType()
 * @method mixed getAccess()
 * @method $this setAccess(mixed $mixedAccess)
 * @method bool hasAccess()
 * @method mixed getStatic()
 * @method $this setStatic(mixed $mixedStatic)
 * @method bool hasStatic()
 * @method mixed getReturn()
 * @method $this setReturn(mixed $mixedReturn)
 * @method bool hasReturn()
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
        'DETAIL_TEXT',
        'DETAIL_PAGE_URL',
    );

    /**
     * @var array
     */
    protected static $arProps = array(
        'TYPE',
        'ACCESS',
        'STATIC',
        'RETURN',
    );

    /**
     * 
     * @return string
     */
    public static function getModel(): string {
        return Model::class;
    }

    /**
     * 
     * @return array
     */
    public function getReturnDisplayArray(): array {

        $arResult = array();

        /** @var \Api\Core\Base\Collection $obSections */
        $obSections = \Api\WikiApi\Section\Model::getAll();

        /** @var \Api\Core\Iblock\Property\Value\Collection $obReturns */
        $obReturns = $this->getReturn(true);

        /** @var \Api\Core\Iblock\Property\Value\Entity $obReturn */
        foreach ($obReturns as $obReturn) {
            $iSectionId = $obReturn->getValue();
            /** @var \Api\WikiApi\Section\Entity $obSection */
            $obSection = $obSections->getByKey($iSectionId);
            if ($obSection->getActive() == 'Y') {
                $strLink = $obSection->getSectionPageUrl();
                $arClassPath = array();
                $arClassPath[] = $obSection->getName();
                while ($obSection->getIblockSectionId() > 0) {
                    $obSection = $obSections->getByKey($obSection->getIblockSectionId());
                    if (!is_null($obSection) && $obSection->isExists()) {
                        $arClassPath[] = $obSection->getName();
                    } else {
                        break;
                    }
                }
                $arResult[] = '<a href="' . $strLink . '">\\Api\\Core\\' . implode('\\', array_reverse($arClassPath)) . '</a>';
            } else {
                $arResult[] = $obSection->getName();
            }
        }

        return $arResult;
    }

}
