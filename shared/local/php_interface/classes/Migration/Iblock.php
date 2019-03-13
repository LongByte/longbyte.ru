<?php

namespace Realweb\Migration;

use Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Main\Entity\Query;
use Bitrix\Main\UserFieldTable;
use CUserTypeEntity;


class Iblock extends \WS\ReduceMigrations\Builder\Entity\Iblock
{

    public function getEnumUserFieldId($xmlId, $fieldId = false)
    {
        $id = false;
        $cUserFieldsEnum = new \CUserFieldEnum();
        $arFilter=Array("XML_ID" => $xmlId,);
        if($fieldId){
            $arFilter["USER_FIELD_ID"]= $fieldId;
        }

        $arId = $cUserFieldsEnum->GetList(Array(), $arFilter)->Fetch();



        if (is_array($arId)) {
            $id = $arId["ID"];
        }

        return $id;
    }

    public function getEnumPropertyId($xmlId, $propertyCode = false)
    {
        $arFields["IBLOCK_ID"] = $this->getId();
        $id = false;
        $rsQuery = new Query(PropertyEnumerationTable::getEntity());
        $rsQuery->addSelect("ID");

        $rsQuery->addFilter("PROPERTY.IBLOCK_ID", $this->getId());
        if ($propertyCode) {
            $rsQuery->addFilter("PROPERTY.CODE", $propertyCode);
        }
        $rsQuery->addFilter("XML_ID", $xmlId);

        $arId = $rsQuery->exec()->fetchRaw();
        if (is_array($arId)) {
            $id = $arId["ID"];
        }
        return $id;
    }

    /**
     * создает сущности  $entityClass и  вовзращает ее ID.
     * если с таким XML_ID уже сущность есть, то она обновляется
     * @param $arFields
     * @param string $entityClass
     * @return mixed
     * @throws \Exception
     */

    public function createUpdateIblockEntity($arFields, $entityClass = "CIBlockElement")
    {
        $arFields["IBLOCK_ID"] = $this->getId();
        /**
         * @var \CIBlockElement $entityClass
         */
        $rsEntity = $entityClass::getList(Array(), Array("XML_ID" => $arFields["XML_ID"],"IBLOCK_ID"=> $arFields["IBLOCK_ID"]));

        $e = new $entityClass();

        if ($arEntity = $rsEntity->Fetch()) {
            $idEntity = $arEntity["ID"];
        }
        if ($idEntity > 0) {
            $e->Update($idEntity, $arFields);
        } else {
            //var_dump(__LINE__);

            $idEntity = $e->Add($arFields);
            //var_dump($idEntity,$e);
            //var_dump(__LINE__,$idEntity);
            if (!$idEntity) {
                throw new \Exception($e->LAST_ERROR);
            }
        }
        return $idEntity;
    }

    public function deleteIblockEntityIfExist($arFields, $entityClass = "CIBlockElement")
    {
        $arFields["IBLOCK_ID"] = $this->getId();
        /**
         * @var \CIBlockElement $entityClass
         */
        $rsEntity = $entityClass::getList(Array(), Array("XML_ID" => $arFields["XML_ID"]));

        $e = new $entityClass();
        $idEntity = 0;
        if ($arEntity = $rsEntity->Fetch()) {
            $idEntity = $arEntity["ID"];
        }
        if ($idEntity > 0) {
            $e->delete($idEntity, $arFields);
        }
        return $idEntity;
    }

public function deleteIblockPropertyByCode($code){
    $rsProperty = \CIBlockProperty::GetList(Array(), Array("CODE" => $code, "IBLOCK_ID" => $this->getId()));

    if ($arField = $rsProperty->Fetch()) {
        $p = new \CIBlockProperty();
        $p->Delete($arField["ID"]);
    }
}


    public function deleteUserFieldsIfExist($FIELD_NAME)
    {
        $__entityID = "IBLOCK_" . $this->getId() . "_SECTION";


        $rsUserField = CUserTypeEntity::GetList(Array(), Array("FIELD_NAME" => $FIELD_NAME, "ENTITY_ID" => $__entityID));

        if ($arField = $rsUserField->Fetch()) {
            $CUserTypeEntity = new CUserTypeEntity();
            $CUserTypeEntity->Delete($arField["ID"]);
        }
    }

    public function createUpdateUserFields($FIELD_NAME, $typeField, $arParams)
    {
        global $APPLICATION;

        $__entityID = "IBLOCK_" . $this->getId() . "_SECTION";

        $rsUserField = CUserTypeEntity::GetList(Array(), Array("FIELD_NAME" => $FIELD_NAME, "ENTITY_ID" => $__entityID));
        $userFieldId = false;
        if ($arField = $rsUserField->Fetch()) {
            $userFieldId = $arField["ID"];
        }

        $aUserFields = array(
            'ENTITY_ID' => $__entityID,
            /* Код поля. Всегда должно начинаться с UF_ */
            'FIELD_NAME' => $FIELD_NAME,
            /* Указываем, что тип нового пользовательского свойства строка */
            'USER_TYPE_ID' => $typeField,
            /*
             * XML_ID пользовательского свойства.
             * Используется при выгрузке в качестве названия поля
             */
            'XML_ID' => $__entityID . $FIELD_NAME,
            /* Сортировка */
            'SORT' => $arParams["SORT"],
            /* Является поле множественным или нет */
            'MULTIPLE' => $arParams["MULTIPLE"]=="Y"?"Y":"N",
            /* Обязательное или нет свойство */
            'MANDATORY' => 'N',
            /*
             * Показывать в фильтре списка. Возможные значения:
             * не показывать = N, точное совпадение = I,
             * поиск по маске = E, поиск по подстроке = S
             */
            'SHOW_FILTER' => 'I',
            /*
             * Не показывать в списке. Если передать какое-либо значение,
             * то будет считаться, что флаг выставлен (недоработка разработчиков битрикс).
             */
            'SHOW_IN_LIST' => '',
            /*
             * Не разрешать редактирование пользователем.
             * Если передать какое-либо значение, то будет считаться,
             * что флаг выставлен (недоработка разработчиков битрикс).
             */
            'EDIT_IN_LIST' => '',
            /* Значения поля участвуют в поиске */
            'IS_SEARCHABLE' => 'N',
            /*
             * Дополнительные настройки поля (зависят от типа).
             * В нашем случае для типа string
             */



            /* Подпись в форме редактирования */
            'EDIT_FORM_LABEL' => array(
                'ru' => $arParams["NAME"],
                'en' => $arParams["NAME"],
            ),
            /* Заголовок в списке */
            'LIST_COLUMN_LABEL' => array(
                'ru' => $arParams["NAME"],
                'en' => $arParams["NAME"],
            ),
            /* Подпись фильтра в списке */
            'LIST_FILTER_LABEL' => array(
                'ru' => $arParams["NAME"],
                'en' => $arParams["NAME"],
            ),
            /* Сообщение об ошибке (не обязательное) */
            'ERROR_MESSAGE' => array(
                'ru' => 'Ошибка при заполнении пользовательского свойства',
                'en' => 'An error in completing the user field',
            ),


            /* Дополнительные поля настроек */
            'SETTINGS' => is_array($arParams["SETTINGS"]) ? $arParams["SETTINGS"] : array(),


        );

        $сUserTypeEntity = new CUserTypeEntity();
        if (!$userFieldId) {
            $userFieldId = $сUserTypeEntity->Add($aUserFields); // int
        } else {
            $сUserTypeEntity->Update($userFieldId, $aUserFields); // int
        }
        if (!$userFieldId) {
            // var_dump($aUserFields);
            $ex = $APPLICATION->GetException();
            throw new \Exception($ex->GetString());
        }
        return $userFieldId;
    }

    public function saveIblockField($arFields)
    {
        \CIBlock::SetFields($this->getId(), $arFields);
    }


}
