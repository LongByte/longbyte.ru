<?php

namespace Api\Files\Section;

/**
 * Class \Api\Files\Section\Entity
 * 
 */
class Entity extends \Api\Core\Iblock\Section\Entity {

    /**
     *
     * @var array
     */
    protected static $arFields = array(
        'ID',
        'NAME',
        'CODE',
        'SECTION_CODE_PATH'
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
    public function toArray(): array {
        $arData = parent::toArray();

        $arData['section_page_url'] = $this->getSectionPageUrl();

        return $arData;
    }

}
