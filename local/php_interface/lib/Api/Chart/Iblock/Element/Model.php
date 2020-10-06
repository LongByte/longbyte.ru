<?php

namespace Api\Chart\Iblock\Element;

/**
 * Class \Api\Chart\Iblock\Element\Model
 */
abstract class Model extends \Api\Core\Iblock\Element\Model {

    /**
     * 
     * @param array $arElement
     * @param array|null $arProperties
     * @return \Api\Core\Iblock\Element\Entity
     */
    protected static function _getEntityFromElementArray(array $arElement, array $arProperties = null): \Api\Core\Iblock\Element\Entity {

        if (is_null($arProperties)) {
            $arProperties = $arElement['PROPERTIES'];
        }

        $strEntityClass = static::getEntity();
        $arAllowProps = $strEntityClass::getProps();

        foreach ($arProperties as $arProperty) {
            if (count($arAllowProps) > 0 && !in_array($arProperty['CODE'], $arAllowProps)) {
                continue;
            }
            if (!array_key_exists($arProperty['CODE'], $arElement)) {
                $arElement[$arProperty['CODE']] = $arProperty['VALUE'];
            }
        }

        /** @var \Api\Core\Iblock\Element\Entity $obEntity */
        $obEntity = new $strEntityClass($arElement['ID'], $arElement);

        return $obEntity;
    }

}
