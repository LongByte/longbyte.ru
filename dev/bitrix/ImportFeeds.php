<?

use Bitrix\Main\Loader;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\Web\Uri;
use Bitrix\Main\Context;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\SectionTable;
use Bitrix\Catalog\StoreTable;
use Bitrix\Catalog\StoreProductTable;

class ImportFeeds {

    /**
     * Обвновление остатков
     * @param array
     * @return array
     */
    // <editor-fold defaultstate="collapsed" desc="updateQuantity">
    function updateQuantity($arResult) {

        if (Loader::includeModule('iblock') && Loader::includeModule('catalog')) {

            $obElement = new CIBlockElement;

            $arCreatedOffers = array();

            if (empty($arResult['PROVIDER'])) {
                return array(
                    'RESULT' => 'ERROR',
                    'TEXT' => 'Не задан поставщик'
                );
            }

            //получаем склад поставщика
            $arStore = StoreTable::getList(array(
                    'filter' => array('XML_ID' => $arResult['PROVIDER']),
                    'limit' => 1
                ))->fetch();

            if (!$arStore) {
                $rsStoreAdd = StoreTable::add(array(
                    'TITLE' => $arResult['PROVIDER'],
                    'XML_ID' => $arResult['PROVIDER'],
                    'ADDRESS' => '-',
                    'CODE' => 'EXTERNAL_'.mb_strtoupper(\CUtil::translit($arResult['PROVIDER'], 'ru', array("replace_space"=>"-","replace_other"=>"-")))
                ));

                if ($rsStoreAdd->isSuccess()) {
                    $arStore = array(
                        'ID' => $rsStoreAdd->getId()
                    );
                } else {
                    $arErrors = $rsStoreAdd->getErrorMessages();
                    return array(
                        'RESULT' => 'ERROR',
                        'TEXT' => implode('. ', $arErrors)
                    );
                }
            }

            $arOffersIBlock = IblockTable::getList(array(
                    'filter' => array('CODE' => 'offers'),
                    'limit' => 1
                ))->fetch();

            //получаем все торговые предложения поставщика
            $rsItems = CIBlockElement::GetList(
                    array(), //
                    array(
                    'IBLOCK_ID' => $arOffersIBlock['ID'],
                    'PROPERTY_EXT_ARTICLES' => $arResult['PROVIDER']
                    ), //
                    false, //
                    false, //
                    array('ID', 'IBLOCK_ID', 'XML_ID', 'NAME', 'ACTIVE')
            );

            while ($obItem = $rsItems->GetNextElement()) {
                $arItem = $obItem->GetFields();
                $arItem['PROPERTIES'] = $obItem->GetProperties(array(), array('CODE' => 'EXT_ARTICLES'));

                $strProviderArticle = '';

                foreach ($arItem['PROPERTIES']['EXT_ARTICLES']['VALUE'] as $i => $strProvider) {
                    if ($strProvider == $arResult['PROVIDER']) {
                        $strProviderArticle = $arItem['PROPERTIES']['EXT_ARTICLES']['DESCRIPTION'][$i];
                        break;
                    }
                }

                //ну что тут поделать то? нет внешнего кода вообще
                if (empty($strProviderArticle))
                    continue;

                //наличие в прайсе записи
                $bIsset = isset($arResult['ITEMS'][$strProviderArticle]);
                //количество по прайсу
                $iQuantity = 0;
                if ($bIsset) {
                    $iQuantity = $arResult['ITEMS'][$strProviderArticle]['QUANTITY'];
                }

                //если есть в прайсе в наличии, а на сайте выключен - включаем
                if ($bIsset && $iQuantity > 0 && $arItem['ACTIVE'] != 'Y') {
                    $obElement->Update($arItem['ID'], array('ACTIVE' => 'Y'));
                }

                //обновляем количество
                $arProductStore = StoreProductTable::getList(array(
                        'filter' => array(
                            'STORE_ID' => $arStore['ID'],
                            'PRODUCT_ID' => $arItem['ID']
                        ),
                        'limit' => 1
                    ))->fetch();

                if ($arProductStore) {
                    $rsStoreProduct = StoreProductTable::update($arProductStore['ID'], array(
                            'AMOUNT' => $iQuantity
                    ));
                    if (!$rsStoreProduct->isSuccess()) {
                        return array(
                            'RESULT' => 'ERROR',
                            'TEXT' => 'Произошла ошибка при обновлении товара ' . $arItem['NAME'] . ' с артикулом ' . $strProviderArticle . '. ' . implode(';', $rsStoreAdd->getErrorMessages())
                        );
                    }
                } else {
                    $rsStoreProduct = StoreProductTable::add(array(
                            'STORE_ID' => $arStore['ID'],
                            'PRODUCT_ID' => $arItem['ID'],
                            'AMOUNT' => $iQuantity
                    ));
                    if (!$rsStoreProduct->isSuccess()) {
                        return array(
                            'RESULT' => 'ERROR',
                            'TEXT' => 'Произошла ошибка при обновлении товара ' . $arItem['NAME'] . ' с артикулом ' . $strProviderArticle . '. ' . implode(';', $rsStoreAdd->getErrorMessages())
                        );
                    }
                }
                //if ($arItem['CATALOG_AVAILABLE'] == 'N') {
                    CatalogHelper::RecalcQuantity($arItem['ID']);
                //}

                unset($arResult['ITEMS'][$strProviderArticle]);
            }

            //ищем уже созданные новые торговые предложения
            $rsHaveNewOffers = CIBlockElement::GetList(
                    array(), //
                    array(
                    'IBLOCK_ID' => $arOffersIBlock['ID'],
                    'PROPERTY_EXT_ARTICLES' => $arResult['PROVIDER'],
                    'XML_ID' => array_keys($arResult['ITEMS'])  //todo: у нас они будут храниться в описании. как искать?
                    ), //
                    false, //
                    false, //
                    array('ID', 'IBLOCK_ID', 'NAME', 'XML_ID')
            );

            $arHaveNewOffers = array();
            while ($obItem = $rsItems->GetNextElement()) {
                $arItem = $obItem->GetFields();
                $arItem['PROPERTIES'] = $obItem->GetProperties(array(), array('CODE' => 'EXT_ARTICLES'));

                foreach ($arItem['PROPERTIES']['EXT_ARTICLES']['VALUE'] as $i => $strProvider) {
                    if ($strProvider == $arResult['PROVIDER']) {
                        $strProviderArticle = $arItem['PROPERTIES']['EXT_ARTICLES']['DESCRIPTION'][$i];
                        break;
                    }
                }

                //ну что тут поделать то? нет внешнего кода вообще
                if (empty($strProviderArticle))
                    continue;

                $arHaveNewOffers[$strProviderArticle] = $arItem['ID'];
            }

            $iNewSectionID = 0;
            $arNewSection = SectionTable::getList(array(
                    'filter' => array(
                        'IBLOCK_ID' => $arOffersIBlock['ID'],
                        'CODE' => 'new'
                    ),
                    'limit' => 1
                ))->fetch();

            if (!$arNewSection) {
                $rsAddResult = SectionTable::add(array(
                        'IBLOCK_ID' => $arOffersIBlock['ID'],
                        'CODE' => 'new',
                        'ACTIVE' => 'N',
                        'NAME' => 'Новые торговые предложения',
                        'TIMESTAMP_X' => new Bitrix\Main\Type\DateTime()
                ));

                if ($rsAddResult->isSuccess()) {
                    $iNewSectionID = $rsAddResult->getId();
                } else {
                    $errors = $rsAddResult->getErrorMessages();
                }
            } else {
                $iNewSectionID = $arNewSection['ID'];
            }

            if ($iNewSectionID) {

                //теперь создаем новые товары, которые мы не нашли в каталоге
                foreach ($arResult['ITEMS'] as $strProviderArticle => $arItem) {
                    if (empty($arItem['NAME']) || empty($strProviderArticle))
                        continue;

                    $intID = 0;
                    if (!isset($arHaveNewOffers[$strProviderArticle])) {
                        $intID = $obElement->Add(array(
                            'ACTIVE' => 'Y',
                            'IBLOCK_ID' => $arOffersIBlock['ID'],
                            'NAME' => $arItem['NAME'],
                            'IBLOCK_SECTION_ID' => $iNewSectionID,
                            'PROPERTY_VALUES' => array(
                                'EXT_ARTICLES' => array(
                                    array(
                                        'VALUE' => $arResult['PROVIDER'],
                                        'DESCRIPTION' => $strProviderArticle
                                    )
                                )
                            )
                        ));
                    }

                    if ($intID > 0) {
                        CCatalogProduct::Add(array(
                            'ID' => $intID,
                            'TYPE' => Bitrix\Catalog\ProductTable::TYPE_OFFER
                        ));

                        $arCreatedOffers[] = $intID;
                    }
                }
            }
        }

        return array(
            'RESULT' => 'OK',
            'TEXT' => 'Прайс успешно загружен',
            'CREATED_OFFERS' => $arCreatedOffers
        );
    }

    // </editor-fold>

    /**
     * Функция загружает yml прайсы со внешних источников
     * @return array в формате, пригодном для функции UpdateQuantity
     */
    // <editor-fold defaultstate="collapsed" desc="loadExternalPrice">
    function loadExternalPrice($bManual = false) {
        $arResult = [
            'STATUS' => 'Ok',
            'TEXT' => [],
            'DATA' => [],
        ];

        $obElement = new CIBlockElement;

        if (!Loader::includeModule('iblock')) {
            $arResult['STATUS'] = 'Error';
            $arResult['TEXT'][] = 'Модуль информационных блоков не найден.';
        }

        if ($arResult['STATUS'] != 'Error') {
            $arIBlock = IblockTable::getList(array(
                    'filter' => array('CODE' => 'import_rules'),
                    'limit' => 1
                ))->fetch();

            if (!$arIBlock) {
                $arResult['STATUS'] = 'Error';
                $arResult['TEXT'][] = 'Инфоблок не найден.';
            }
        }

        if ($arResult['STATUS'] != 'Error') {
            $rsRules = CIBlockElement::GetList(
                    array(), //
                    array('IBLOCK_ID' => $arIBlock['ID'], 'ACTIVE' => 'Y'), //
                    false, //
                    false, //
                    array('ID', 'IBLOCK_ID', 'NAME', 'DATE_ACTIVE_FROM')
            );

            while ($obRule = $rsRules->GetNextElement()) {
                $arRule = $obRule->GetFields();
                $arRule['PROPERTIES'] = $obRule->GetProperties();
                if (time() < MakeTimeStamp($arRule['DATE_ACTIVE_FROM']) && !$bManual)
                    continue;

                $strRawData = self::getData($arRule['PROPERTIES']['URL']['VALUE']);

                if (!$strRawData) {
                    $arResult['STATUS'] = 'Error';
                    $arResult['TEXT'][] = 'Прайс недоступен или пустой. URL = ' . $arRule['PROPERTIES']['URL']['VALUE'];
                    continue;
                }

                $arProducts = [];
                try {
                    $arXml = new SimpleXMLElement($strRawData);
                } catch (Exception $e) {
                    $arResult['STATUS'] = 'Error';
                    $arResult['TEXT'][] = 'Ошибка парсинга XML. URL = ' . $arRule['PROPERTIES']['URL']['VALUE'];
                    continue;
                }
                unset($strRawData);
                $arPath = explode(',', $arRule['PROPERTIES']['XML_PATH']['VALUE']);
                $arData = $arXml;

                //запускаем рекурсивный перебор
                self::processLevels($arData, $arPath, $arProducts, $arRule);

                if (count($arProducts) > 0) {
                    $arResult['DATA'][] = [
                        'ID' => $arRule['ID'],
                        'PROVIDER' => $arRule['NAME'],
                        'ITEMS' => $arProducts
                    ];
                    unset($arXml, $arProducts);
                }

                if (!$bManual) {
                    $strNextDateTime = ConvertTimeStamp(time() + $arRule['PROPERTIES']['RUN_PERIOD']['VALUE'], 'FULL');
                    $obElement->Update($arRule['ID'], [
                        'DATE_ACTIVE_FROM' => $strNextDateTime
                    ]);
                }
            }
        }

        return $arResult;
    }

    private function processLevels($arData, $arPath, &$arProducts, $arRule) {
        $arInnerData = $arData;

        foreach ($arPath as $i => $field) {
            $fNeedRecursion = false;
            //если есть флаг рекурсии
            if (preg_match('/\&$/', $field)) {
                $field = substr($field, 0, strlen($field) - 1);
                if (isset($arInnerData->{$field})) {
                    $fNeedRecursion = true;
                    $arInnerPath = array_slice($arPath, $i);
                }
            }

            if ($arInnerData->{$field}) {
                $arInnerData = $arInnerData->{$field};

                if ($fNeedRecursion) {
                    foreach ($arInnerData as $arSection) {
                        self::processLevels($arSection, $arInnerPath, $arProducts, $arRule);
                    }
                }
            }
        }

        foreach ($arInnerData as $arOffer) {

            $strName = '';
            $strArticle = '';
            $iQuantity = 0;

            //бывали случаи
            if (empty($arOffer))
                continue;

            if ($arRule['PROPERTIES']['FIELD_NAME_IS_PARAM']['VALUE'] == 1) {
                $strName = $arOffer[$arRule['PROPERTIES']['FIELD_NAME']['VALUE']]->__toString();
            } else {
                $strName = $arOffer->{$arRule['PROPERTIES']['FIELD_NAME']['VALUE']}->__toString();
            }

            if ($arRule['PROPERTIES']['FIELD_XML_ID_IS_PARAM']['VALUE'] == 1) {
                $strArticle = $arOffer[$arRule['PROPERTIES']['FIELD_XML_ID']['VALUE']]->__toString();
            } else {
                $strArticle = $arOffer->{$arRule['PROPERTIES']['FIELD_XML_ID']['VALUE']}->__toString();
            }

            if ($arRule['PROPERTIES']['FIELD_QUANTITY_IS_PARAM']['VALUE'] == 1) {
                $iQuantity = $arOffer[$arRule['PROPERTIES']['FIELD_QUANTITY']['VALUE']]->__toString();
            } else {
                $iQuantity = IntVal($arOffer->{$arRule['PROPERTIES']['FIELD_QUANTITY']['VALUE']}->__toString());
            }

            $strArticle = trim($strArticle);
            $strName = trim($strName);

            $arProducts[$strArticle] = [
                'NAME' => $strName,
                'QUANTITY' => $iQuantity
            ];
        }
    }

    // </editor-fold>

    /**
     * Агент, загружающий прайсы со внешних источников
     * @param boolean $manual
     * @return string
     */
    // <editor-fold defaultstate="collapsed" desc="agentImportExternalPrice">
    function agentImportExternalPrice($manual = false) {

        if (Loader::includeModule('iblock')) {

            $arData = self::loadExternalPrice($manual);
            
            if ($manual) {
                echo '<pre>';
                print_r($arData);
                echo '</pre>';
            }

            $arCreatedOffers = array();

            foreach ($arData['DATA'] as $arPrice) {
                $arUpdateResult = self::updateQuantity($arPrice);
                if ($arUpdateResult['RESULT'] == 'OK' && count($arUpdateResult['CREATED_OFFERS']) > 0) {
                    $arCreatedOffers = array_merge($arCreatedOffers, $arUpdateResult['CREATED_OFFERS']);
                }
            }

            if (count($arCreatedOffers) > 0) {
                $strMainText = '';

                $rsOffers = CIBlockElement::GetList(
                        array(), //
                        array('ID' => $arCreatedOffers), //
                        false, //
                        false, //
                        array('ID', 'IBLOCK_ID', 'NAME')
                );

                $strServerName = Context::getCurrent()->getServer()->getHttpHost();

                while ($arOffer = $rsOffers->GetNext()) {
                    $strMainText .= "<a href=\"http://" . $strServerName . "/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=" . $arOffer['IBLOCK_ID'] . "&type=catalog&ID=" . $arOffer['ID'] . "&lang=ru\">" . $arOffer['NAME'] . "</a><br>";
                }

                Event::send(array(
                    "EVENT_NAME" => "IMPORT_EXTERNAL_PRICE_NOTIFICATION",
                    "LID" => "s1",
                    "C_FIELDS" => array(
                        "TEXT" => $strMainText
                    ),
                ));
            }
        }

        return __CLASS__ . '::' . __FUNCTION__ . '();';
    }

    // </editor-fold>

    /**
     * Функция получения информации по url
     * @param string $strUrl
     * @return string
     */
    // <editor-fold defaultstate="collapsed" desc="getData">
    private function getData($strUrl) {

        if (preg_match('/^http/', $strUrl)) {
            return self::getDataByHttp($strUrl);
        } elseif (preg_match('/^ftp/', $strUrl)) {
            return self::getDataByFtp($strUrl);
        }

        return '';
    }

    // </editor-fold>

    /**
     * Фнкция получения информации по http или https
     * @param string $strUrl
     * @return string
     */
    // <editor-fold defaultstate="collapsed" desc="getDataByHttp">
    private function getDataByHttp($strUrl) {

        $strContent = '';

        if (function_exists('curl_init')) {
            $arCurlOptions = array(
                CURLOPT_RETURNTRANSFER => true, // return web page
                CURLOPT_HEADER => false, // don't return headers
                CURLOPT_FOLLOWLOCATION => true, // follow redirects
                CURLOPT_ENCODING => '', // handle all encodings
                CURLOPT_AUTOREFERER => true, // set referer on redirect
                CURLOPT_CONNECTTIMEOUT => 300, // timeout on connect
                CURLOPT_TIMEOUT => 300, // timeout on response
                CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
                CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
            );

            $ch = curl_init($strUrl);
            curl_setopt_array($ch, $arCurlOptions);
            $strContent = curl_exec($ch);
            curl_close($ch);
        }

        return $strContent;
    }

    // </editor-fold>

    /**
     * Функция получения информации по ftp
     * @param string $strUrl
     * @return string
     */
    // <editor-fold defaultstate="collapsed" desc="getDataByFtp">
    private function getDataByFtp($strUrl) {

        $strResult = '';

        if (function_exists('ftp_connect')) {
            $obUri = new Uri($strUrl);
            $obServer = Context::getCurrent()->getServer();

            $strFileName = $obUri->getPath();
            $strFileName = substr($strFileName, strpos($strFileName, '/') + 1);

            $strLocalFileName = $obServer->getDocumentRoot() . '/upload/tmp/' . $strFileName;

            $iType = FTP_ASCII;

            $obFtpServer = ftp_connect($obUri->getHost());

            if ($obFtpServer) {
                if (@ftp_login($obFtpServer, $obUri->getUser(), $obUri->getPass())) {
                    ftp_pasv($obFtpServer, true);
                    if (ftp_get($obFtpServer, $strLocalFileName, $obUri->getPath(), $iType)) {
                        $strResult = file_get_contents($strLocalFileName);
                        unlink($strLocalFileName);
                    }
                }
                @ftp_close($obFtpServer);
            }
        }

        return $strResult;
    }

    // </editor-fold>
}
