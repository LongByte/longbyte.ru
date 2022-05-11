<?php

namespace Api\WikiApi\Section;

/**
 * Class \Api\WikiApi\Section\Entity
 *
 * @method mixed getId()
 * @method $this setId(mixed $mixedId)
 * @method bool hasId()
 * @method mixed getActive()
 * @method $this setActive(mixed $mixedActive)
 * @method bool hasActive()
 * @method mixed getName()
 * @method $this setName(mixed $mixedName)
 * @method bool hasName()
 * @method mixed getCode()
 * @method $this setCode(mixed $mixedCode)
 * @method bool hasCode()
 * @method mixed getIblockSectionId()
 * @method $this setIblockSectionId(mixed $mixedIblockSectionId)
 * @method bool hasIblockSectionId()
 * @method mixed getSectionCodePath()
 * @method $this setSectionCodePath(mixed $mixedSectionCodePath)
 * @method bool hasSectionCodePath()
 * @method mixed getDescription()
 * @method $this setDescription(mixed $mixedDescription)
 * @method bool hasDescription()
 * @method mixed getUfExtends()
 * @method $this setUfExtends(mixed $mixedUfExtends)
 * @method bool hasUfExtends()
 */
class Entity extends \Api\Core\Iblock\Section\Entity
{

    protected static array $arFields = array(
        'ID',
        'ACTIVE',
        'NAME',
        'CODE',
        'IBLOCK_SECTION_ID',
        'SECTION_CODE_PATH',
        'DESCRIPTION',
        'UF_EXTENDS',
    );

    public static function getModel(): string
    {
        return Model::class;
    }

    public function getClassLink(): string
    {

        /** @var \Api\Core\Base\Collection $obSections */
        $obSections = \Api\WikiApi\Section\Model::getAll();

        $strLink = $this->getSectionPageUrl();
        $arClassPath = array();
        $arClassPath[] = $this->getName();
        $iParentSectionId = $this->getIblockSectionId();
        while ($iParentSectionId > 0) {
            /** @var \Api\WikiApi\Section\Entity $obSection */
            $obSection = $obSections->getByKey($iParentSectionId);
            if (!is_null($obSection) && $obSection->isExists()) {
                $arClassPath[] = $obSection->getName();
                $iParentSectionId = $obSection->getIblockSectionId();
            } else {
                break;
            }
        }
        return '<a href="' . $strLink . '">\\Api\\Core\\' . implode('\\', array_reverse($arClassPath)) . '</a>';
    }

}
