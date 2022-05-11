<?php

namespace Api\Files\Section;

/**
 * Class \Api\Files\Section\Entity
 */
class Entity extends \Api\Core\Iblock\Section\Entity
{

    protected static array $arFields = array(
        'ID',
        'NAME',
        'CODE',
        'SECTION_CODE_PATH'
    );

    public static function getModel(): string
    {
        return Model::class;
    }

    public function toArray(): array
    {
        $arData = parent::toArray();
        $arData['section_page_url'] = $this->getSectionPageUrl();
        return $arData;
    }

}
