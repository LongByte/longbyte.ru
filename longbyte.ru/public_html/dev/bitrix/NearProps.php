<?

class NearProps
{

    /**
     * Прописывает торговым предложениям примерные цвета в соответствии с основным
     * @param $arFields
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function OnAfterSaveNearColor($arFields)
    {
        if (empty($arFields['IBLOCK_ID']) || empty($arFields['ID']))
            return true;
        if (!in_array($arFields['IBLOCK_ID'], array(IBLOCK_CATALOG_CATALOG, IBLOCK_CATALOG_OFFERS, IBLOCK_CATALOG_COLORS, IBLOCK_CATALOG_FILTER_RULE)))
            return true;

        $arColorIBlock = \Bitrix\Iblock\IblockTable::getList([
            'filter' => [
                'IBLOCK_TYPE_ID' => 'catalog',
                'CODE' => 'colors'
            ],
            'limit' => 1
        ])->fetch();

//        $arCatalogIBlock = \Bitrix\Iblock\IblockTable::getList([
//            'filter' => [
//                'IBLOCK_TYPE_ID' => 'catalog',
//                'CODE' => 'catalog'
//            ],
//            'limit' => 1
//        ])->fetch();
//
//        $arOffersIBlock = \Bitrix\Iblock\IblockTable::getList([
//            'filter' => [
//                'IBLOCK_TYPE_ID' => 'catalog',
//                'CODE' => 'offers'
//            ],
//            'limit' => 1
//        ])->fetch();

        if ($arFields['IBLOCK_ID'] == $arColorIBlock['ID']) {
            //получаем свойства инфоблока цветов
            $arIBlockProps = [];
            $rsProps = \Bitrix\Iblock\PropertyTable::getList([
                'filter' => [
                    'IBLOCK_ID' => $arColorIBlock['ID']
                ],
                'select' => ['ID', 'CODE']
            ]);
            while ($arProp = $rsProps->fetch()) {
                $arIBlockProps[$arProp['CODE']] = $arProp;
            }
            //получаем подробно элемент, который обновился
            $obFieldsAdds = CIBlockElement::GetList(
                []
                , ['=ID' => $arFields['ID']]
                , false
                , ['nTopCount' => 1]
                , ['ID', 'IBLOCK_ID', 'NAME']
            )->GetNextElement(true, false);
            $arFieldsAdds = $obFieldsAdds->GetFields();
            $arFieldsAdds['PROPERTIES'] = $obFieldsAdds->GetProperties();
            $arFields = array_merge($arFieldsAdds, $arFields);
            if (!empty($arFields['PROPERTIES']['NEAR_COLOR']['VALUE'])) {
                $nearColor = [];
                foreach ($arFields['PROPERTIES']['NEAR_COLOR']['VALUE'] as &$val) {
                    if (!empty($val)) {
                        $nearColor[] = $val;
                    }
                }
                unset($val);
                //получаем свойства цветов из всех инфоблоков 
                $rsColorProps = \Bitrix\Iblock\PropertyTable::getList([
                    'filter' => [
                        'CODE' => 'COLORS',
                        'ACTIVE' => 'Y'
                    ],
                    'select' => ['ID', 'CODE', 'IBLOCK_ID', 'PROPERTY_TYPE']
                ]);
                while ($arProp = $rsColorProps->fetch()) {
                    if ($arProp['IBLOCK_ID'] == $arFields['IBLOCK_ID'])
                        continue;

                    //получаем все товары или ТП (пофиг что) с таким цветом
                    $rsOffers = CIBlockElement::GetList(
                        [], //
                        [
                            'IBLOCK_ID' => $arProp['IBLOCK_ID'],
                            'PROPERTY_' . $arProp['CODE'] => $arFields['ID']
                        ], //
                        false, //
                        false, //
                        ['ID', 'IBLOCK_ID']
                    );

                    while ($obOffer = $rsOffers->GetNextElement(true, false)) {
                        $arOffer = $obOffer->GetFields();
                        $arOffer['PROPERTIES'] = $obOffer->GetProperties([], ['CODE' => 'NEAR_COLOR']);

                        //подготавливаем значения
                        $curNearColor = $arOffer['PROPERTIES']['NEAR_COLOR']['VALUE'];
                        if (empty($curNearColor)) {
                            $curNearColor = [];
                        }
                        if (!is_array($curNearColor))
                            $curNearColor = [$curNearColor];
                        asort($curNearColor);

                        //если набор изменился, то обновляем
                        if ($nearColor != $curNearColor) {
                            CIBlockElement::SetPropertyValuesEx($arOffer['ID'], $arProp['IBLOCK_ID'], ['NEAR_COLOR' => $nearColor]);
                            self::ColorLog('OnAfterSaveNearColor colors', $arOffer['ID'], $arProp['IBLOCK_ID'], 'NEAR_COLOR', $nearColor);
                        }
                    }
                }
            }
        } else {
            //если это инфоблок товаров (не цветов)
            $arIBlockProps = array();
            //плучаем все свойства
            $rsProps = \Bitrix\Iblock\PropertyTable::getList([
                'filter' => [
                    'IBLOCK_ID' => $arFields['IBLOCK_ID']
                ],
                'select' => ['ID', 'CODE', 'PROPERTY_TYPE']
            ]);
            while ($arProp = $rsProps->fetch()) {
                $arIBlockProps[$arProp['CODE']] = $arProp;
            }

            //получаем товар подробнее
            if ($obCurElement = CIBlockElement::GetList(
                [], //
                ['IBLOCK_ID' => $arFields['IBLOCK_ID'], 'ID' => $arFields['ID']], //
                false, //
                ['nTopCount' => 1], //
                ['ID', 'IBLOCK_ID']
            )->GetNextElement(true, false)) {

                $arCurElement = $obCurElement->GetFields();
                $arCurElement['PROPERTY_VALUES'] = $obCurElement->GetProperties();

                if ($arIBlockProps['COLORS']['PROPERTY_TYPE'] == 'E') {
                    if (!empty($arFields['PROPERTY_VALUES'][$arIBlockProps['COLORS']['ID']])) {

                        //получаем значения
                        $curNearColor = [];
                        foreach ($arFields['PROPERTY_VALUES'][$arIBlockProps['NEAR_COLOR']['ID']] as &$val) {
                            if (!empty($val['VALUE'])) {
                                $curNearColor[] = $val['VALUE'];
                            }
                        }
                        unset($val);

                        $newColor = reset($arFields['PROPERTY_VALUES'][$arIBlockProps['COLORS']['ID']]);

                        $obNearColorProp = CIBlockElement::GetByID(is_array($newColor) ? $newColor['VALUE'] : $newColor)->GetNextElement(true, false);
                        if ($obNearColorProp) {
                            $arNearColorProp = $obNearColorProp->GetProperties([], ['CODE' => 'NEAR_COLOR']);
                            if ($arNearColorProp['NEAR_COLOR']['VALUE'] != $curNearColor) {
                                CIBlockElement::SetPropertyValuesEx($arFields['ID'], $arFields['IBLOCK_ID'], ['NEAR_COLOR' => $arNearColorProp['NEAR_COLOR']['VALUE']]);
                                self::ColorLog('OnAfterSaveNearColor catalog', $arFields['ID'], $arFields['IBLOCK_ID'], 'NEAR_COLOR', $arNearColorProp['NEAR_COLOR']['VALUE']);
                            }
                        }
                    }
                }

                //                // <editor-fold defaultstate="collapsed" desc="Выключено и не переписано. Для цветов, заполненных текстом">
//                  if ($arIBlockProps['COLOR']['PROPERTY_TYPE'] == 'S') {
//                    if (!empty($arFields['PROPERTY_VALUES'][$arIBlockProps['COLOR']['ID']])) {
//                        $curNearColor = array();
//                        foreach ($arFields['PROPERTY_VALUES'][$arIBlockProps['NEAR_COLOR']['ID']] as &$val) {
//                            if (!empty($val['VALUE'])) {
//                                $curNearColor[] = $val['VALUE'];
//                            }
//                        }
//                        unset($val);
//                        $textColor = reset($arFields['PROPERTY_VALUES'][$arIBlockProps['COLOR']['ID']]);
//                        $textColor = $textColor['VALUE'];
//
//                        if (!empty($textColor)) {
//
//                            if ($obLinkColor = CIBlockElement::GetList(
//                                    array(), //
//                                    array('IBLOCK_ID' => $arColorIBlock['ID'], '=NAME' => $textColor), //
//                                    false, //
//                                    array('nTopCount' => 1), //
//                                    array('ID', 'IBLOCK_ID')
//                                )->GetNextElement()) {
//                                $arLinkColor = $obLinkColor->GetFields();
//                                $arLinkColor['PROPERTIES'] = $obLinkColor->GetProperties();
//                                CIBlockElement::SetPropertyValuesEx($arFields['ID'], $arFields['IBLOCK_ID'], array('NEAR_COLOR' => $arLinkColor['PROPERTIES']['NEAR_COLOR']['VALUE']));
//                                self::ColorLog('OnAfterSaveNearColor catalog', $arFields['ID'], $arFields['IBLOCK_ID'], 'NEAR_COLOR', $arLinkColor['PROPERTIES']['NEAR_COLOR']['VALUE']);
//                            } else {
//                                $el = new CIBlockElement;
//                                self::ColorLog('OnAfterSaveNearColor catalog add', $ID, 94, 'empty', $textColor);
//                                $ID = $el->Add(array(
//                                    'IBLOCK_ID' => $arColorIBlock['ID'],
//                                    'NAME' => $textColor,
//                                    'ACTIVE' => 'Y'
//                                ));
//                            }
//                        }
//                    }
//                }
// </editor-fold>
            }
        }
    }

    /**
     * Функция работы с примерными значениями свойств для фильтра
     * @param $arFields
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function OnAfterSaveNearProp($arFields)
    {
        if (empty($arFields['IBLOCK_ID']) || empty($arFields['ID']))
            return true;
        if (!in_array($arFields['IBLOCK_ID'], array(IBLOCK_CATALOG_CATALOG, IBLOCK_CATALOG_OFFERS, IBLOCK_CATALOG_COLORS, IBLOCK_CATALOG_FILTER_RULE)))
            return true;

        if ($GLOBALS['disableEvents'] === true)
            return true;

        if (empty($arFields['IBLOCK_ID']) || empty($arFields['ID']))
            return true;

        if (empty($arFields['ACTIVE']) || empty($arFields['NAME'])) {
            $arElement = \Bitrix\Iblock\ElementTable::getList(array(
                'filter' => array('ID' => $arFields['ID']),
                'select' => array('NAME', 'ACTIVE'),
                'limit' => 1
            ))->fetch();

            $arFields['ACTIVE'] = $arElement['ACTIVE'];
            $arFields['NAME'] = $arElement['NAME'];
        }

        $el = new CIBlockElement;

        $arFilterRuleIBlock = \Bitrix\Iblock\IblockTable::getList([
            'filter' => [
                'IBLOCK_TYPE_ID' => 'catalog',
                'CODE' => 'filter_rule'
            ],
            'select' => ['ID'],
            'limit' => 1
        ])->fetch();

        //обновлен товар
        if ($arFields['IBLOCK_ID'] != $arFilterRuleIBlock['ID']) {
            /**
             * $arNearProps - список правил для подстановки для этого инфоблока. Формат: $arNearProps[исходной свойство][] = правило ($arNearValue)
             * $arThisItemProps - свойства редактируемого элемента. $arThisItemProps[код свойства] = свойство ($arProp)
             *
             * $arProp - одно свойство редактируемого элемента
             * $arNearValue - одно правило для перебираемого свойства $arProp
             */
            //получаем все правила, нацеленные на этот инфоблок
            $arFilter = array(
                'IBLOCK_ID' => $arFilterRuleIBlock['ID'],
                'ACTIVE' => 'Y',
                'PROPERTY_IBLOCK_ID' => $arFields['IBLOCK_ID'],
            );

            //Ищем все правила для данного инфоблока и раздела
            $arNearProps = [];
            $rsNearProps = CIBlockElement::GetList(
                [], //
                $arFilter, //
                false, //
                false, //
                ['ID', 'IBLOCK_ID', 'NAME', 'CODE']
            );

            while ($obNearValue = $rsNearProps->GetNextElement(true, false)) {
                $arNearValue = $obNearValue->GetFields();
                $arNearValue['PROPERTIES'] = $obNearValue->GetProperties();

                if (empty($arNearValue['PROPERTIES']['RAW_PROPERTY_CODE']['VALUE']))
                    continue;

                if (empty($arNearValue['CODE']))
                    continue;

                $arNearProps[$arNearValue['PROPERTIES']['RAW_PROPERTY_CODE']['VALUE']][] = $arNearValue;
            }

            //если нет ни одного правила, выходим и продолжаем жить своей жизнью
            if (count($arNearProps) == 0)
                return true;

            //Получаем адекватные свойства данного элемента
            $arThisItemProps = CIBlockElement::GetList(
                [], //
                ['IBLOCK_ID' => $arFields['IBLOCK_ID'], 'ID' => $arFields['ID']], //
                false, //
                ['nTopCount' => 1], //
                ['ID', 'IBLOCK_ID']
            )->GetNextElement(true, false)->GetProperties();

            //добавляем NAME
            $arThisItemProps['NAME'] = [
                'NAME' => 'Название',
                'CODE' => 'NAME',
                'VALUE' => $arFields['NAME'],
                'MULTIPLE' => 'N'
            ];

            //Перебираем свойства элемента
            foreach ($arThisItemProps as &$arProp) {
                //Если нет правил для этого свойства, то дальше
                if (!isset($arNearProps[$arProp['CODE']]))
                    continue;

                //Перебираем правила для данного свойства
                foreach ($arNearProps[$arProp['CODE']] as &$arNearValue) {
                    //если таргетное свойство множественное
                    if ($arThisItemProps[$arNearValue['CODE']]['MULTIPLE'] == 'Y') {
                        //Если значение уже присутствует, то выходим
                        if (in_array($arNearValue['NAME'], $arThisItemProps[$arNearValue['CODE']]['VALUE']))
                            continue;
                        //Перебираем шаблоны
                        foreach ($arNearValue['PROPERTIES']['TEMPLATES']['VALUE'] as &$oneTemplate) {
                            //Если подстрока совпадает, то записываем и перебираем следующее свойство
                            if (strpos($arProp['VALUE'], $oneTemplate) !== false) {
                                $arValues = $arThisItemProps[$arNearValue['CODE']]['VALUE'];
                                $arValues[] = $arNearValue['NAME'];
                                $arValues = array_unique($arValues);
                                asort($arValues);
                                CIBlockElement::SetPropertyValuesEx($arFields['ID'], $arFields['IBLOCK_ID'], [$arNearValue['CODE'] => $arValues]);
                                self::ColorLog('OnAfterSaveNearProp != Rule Multy', $arFields['ID'], $arFields['IBLOCK_ID'], $arNearValue['CODE'], $arValues);
                                $arThisItemProps[$arNearValue['CODE']]['VALUE'] = $arValues;
                                $el->Update($arFields['ID'], ['ACTIVE' => $arFields['ACTIVE']]);
                                break;
                            }
                        }
                        unset($oneTemplate);
                    } else {
                        //Если значение уже такое, то выходим
                        if ($arNearValue['NAME'] == $arProp['VALUE'])
                            break;
                        //Перебираем шаблоны
                        foreach ($arNearValue['PROPERTIES']['TEMPLATES']['VALUE'] as &$oneTemplate) {
                            //Если подстрока совпадает, то записываем и перебираем следующее свойство
                            if (strpos($arProp['VALUE'], $oneTemplate) !== false) {
                                CIBlockElement::SetPropertyValuesEx($arFields['ID'], $arFields['IBLOCK_ID'], [$arNearValue['CODE'] => $arNearValue['NAME']]);
                                self::ColorLog('OnAfterSaveNearProp != 116 Single', $arFields['ID'], $arFields['IBLOCK_ID'], $arNearValue['CODE'], $arNearValue['NAME']);
                                break 2;
                            }
                        }
                        unset($oneTemplate);
                    }
                }
                unset($arNearValue);
            }
            unset($arProp);
        } else {
            //обновление правила
            //Получаем адекватный элемент
            $obThisItemProps = CIBlockElement::GetList(
                [], //
                ['IBLOCK_ID' => $arFields['IBLOCK_ID'], 'ID' => $arFields['ID']], //
                false, //
                ['nTopCount' => 1], //
                ['ID', 'IBLOCK_ID', 'NAME', 'CODE', 'ACTIVE']
            )->GetNextElement(true, false);

            $arThisItemProps = $obThisItemProps->GetFields();
            if ($arThisItemProps['ACTIVE'] != 'Y')
                return true;
            $arThisItemProps['PROPERTIES'] = $obThisItemProps->GetProperties();
            $arThisItemProps['PROPERTIES']['NAME'] = [
                'NAME' => 'Название',
                'CODE' => 'NAME',
                'VALUE' => $arFields['NAME'],
                'MULTIPLE' => 'N'
            ];

            if (count($arThisItemProps['PROPERTIES']['TEMPLATES']['VALUE']) == 0)
                return true;

            if (empty($arThisItemProps['PROPERTIES']['IBLOCK_ID']['VALUE']))
                return true;

            if (empty($arThisItemProps['PROPERTIES']['RAW_PROPERTY_CODE']['VALUE']))
                return true;

            if (empty($arThisItemProps['CODE']))
                return true;

            $arTargetProp = \Bitrix\Iblock\PropertyTable::getList([
                'filter' => [
                    'IBLOCK_ID' => $arThisItemProps['PROPERTIES']['IBLOCK_ID']['VALUE'],
                    'CODE' => $arThisItemProps['CODE']
                ],
                'limit' => 1
            ])->fetch();

            if (!$arTargetProp)
                return true;

            //Ищем в нужном ифоблоке все подходящие для подстановки элементы
            $arFilter = array(
                'IBLOCK_ID' => $arThisItemProps['PROPERTIES']['IBLOCK_ID']['VALUE'], //Указанный инфоблок
                '!PROPERTY_' . $arThisItemProps['CODE'] => $arThisItemProps['NAME'], //Значение свойства для фильтра уже задано правильно
            );

            if ($arThisItemProps['PROPERTIES']['RAW_PROPERTY_CODE']['VALUE'] == 'NAME') {
                if (count($arThisItemProps['PROPERTIES']['TEMPLATES']['VALUE']) == 1) {
                    $arFilter['?NAME'] = reset($arThisItemProps['PROPERTIES']['TEMPLATES']['VALUE']);
                } else {
                    $arLogic = [
                        'LOGIC' => 'OR'
                    ];
                    foreach ($arThisItemProps['PROPERTIES']['TEMPLATES']['VALUE'] as $value) {
                        $arLogic[] = ['?NAME' => $value];
                    }
                    $arFilter[] = $arLogic;
                }
            } else {
                if (count($arThisItemProps['PROPERTIES']['TEMPLATES']['VALUE']) == 1) {
                    $arFilter['?PROPERTY_' . $arThisItemProps['PROPERTIES']['RAW_PROPERTY_CODE']['VALUE']] = reset($arThisItemProps['PROPERTIES']['TEMPLATES']['VALUE']);
                } else {
                    $arLogic = [
                        'LOGIC' => 'OR'
                    ];
                    foreach ($arThisItemProps['PROPERTIES']['TEMPLATES']['VALUE'] as $value) {
                        $arLogic[] = ['?PROPERTY_' . $arThisItemProps['PROPERTIES']['RAW_PROPERTY_CODE']['VALUE'] => $value];
                    }
                    $arFilter[] = $arLogic;
                }
            }

            if ($arTargetProp['MULTIPLE'] == 'Y') {
                $rsItem = CIBlockElement::GetList(
                    [], //
                    $arFilter, //
                    false, //
                    false, //
                    ['ID', 'IBLOCK_ID', 'ACTIVE', 'NAME']
                );

                while ($obItem = $rsItem->GetNextElement()) {
                    $arItem = $obItem->GetFields();
                    $arItem['NAME'] = $arItem['~NAME'];
                    $arItem['PROPERTIES'] = $obItem->GetProperties([], ['CODE' => $arThisItemProps['CODE']]);
                    self::ClearTilda($arItem);
                    $arValues = $arItem['PROPERTIES'][$arThisItemProps['CODE']]['VALUE'];
                    $arValues[] = $arThisItemProps['NAME'];
                    $arValues = array_unique($arValues);
                    asort($arValues);
                    CIBlockElement::SetPropertyValuesEx($arItem['ID'], $arItem['IBLOCK_ID'], [$arThisItemProps['CODE'] => $arValues]);
                    self::ColorLog('OnAfterSaveNearProp =rule Multy', $arItem['ID'], $arItem['IBLOCK_ID'], $arThisItemProps['CODE'], $arValues);
                    $el->Update($arItem['ID'], ['ACTIVE' => $arItem['ACTIVE'], 'NAME' => $arItem['NAME']]);
                }
            } else {
                $rsItem = CIBlockElement::GetList(
                    [], //
                    $arFilter, //
                    false, //
                    false, //
                    ['ID', 'IBLOCK_ID', 'ACTIVE', 'NAME']
                );

                while ($arItem = $rsItem->Fetch()) {
                    CIBlockElement::SetPropertyValuesEx($arItem['ID'], $arItem['IBLOCK_ID'], [$arThisItemProps['CODE'] => $arThisItemProps['NAME']]);
                    self::ColorLog('OnAfterSaveNearProp =rule Single', $arItem['ID'], $arItem['IBLOCK_ID'], $arThisItemProps['CODE'], $arThisItemProps['NAME']);
                }
            }
        }
    }

    /**
     * Обработчик события после выполнения CIBlockElement::SetPropertyValuesEx
     * @param $ELEMENT_ID
     * @param $IBLOCK_ID
     * @param $PROPERTY_VALUES
     * @param $FLAGS
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function OnAfterIBlockElementSetPropertyValuesEx($ELEMENT_ID, $IBLOCK_ID, $PROPERTY_VALUES, $FLAGS)
    {
        if (empty($IBLOCK_ID) || empty($ELEMENT_ID))
            return true;
        if (!in_array($IBLOCK_ID, array(IBLOCK_CATALOG_CATALOG, IBLOCK_CATALOG_OFFERS, IBLOCK_CATALOG_COLORS, IBLOCK_CATALOG_FILTER_RULE)))
            return true;
        self::OnAfterSaveNearProp(array(
            'ID' => $ELEMENT_ID,
            'IBLOCK_ID' => $IBLOCK_ID
        ));
    }

    public static function ColorLog($name, $id, $iblock, $prop, $value)
    {
//        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/upload/color.log', "\n" . date('d.m.Y H:i:s') . " Name=" . $name . " Update ID=" . $id . " IBLOCK_ID=" . $iblock . " PROP=" . $prop . " VALUE=" . (is_array($value) ? implode(',', $value) : $value), FILE_APPEND);
    }

    function ClearTilda(&$arArray)
    {
        foreach ($arArray as $key => &$item) {
            if (strpos($key, '~') === 0) {
                unset($arArray[$key]);
                continue;
            }
            if (is_array($item)) {
                self::ClearTilda($item);
            }
        }
        unset($item);
    }

}
