<?

//http://dev.1c-bitrix.ru/community/webdev/group/78/blog/1657/
IncludeModuleLangFile(__FILE__);

class CEdvancecurrencyrate {

    public static function UpdateRates($bAgent = true) {
        $queryStr = 'date_req=' . date('d.m.Y');
        $arCurr = array("USD", "EUR");
        $adminDate = date($GLOBALS['DB']->DateFormatToPHP(CLang::GetDateFormat('SHORT')));
        require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/xml.php');
        $strQueryText = QueryGetData('www.cbr.ru', 80, '/scripts/XML_daily.asp', $queryStr, $errno, $errstr);
        $objXML = new CDataXML();
        if ($objXML->LoadString($strQueryText)) {
            $arData = $objXML->GetArray();
            if (!empty($arData) && is_array($arData)) {
                foreach ($arData['ValCurs']['#']['Valute'] as $arC) {
                    if (in_array($arC["#"]["CharCode"][0]["#"], $arCurr)) {
                        $arNewRate = array(
                            'CURRENCY' => $arC["#"]["CharCode"][0]["#"],
                            'RATE_CNT' => intval($arC['#']['Nominal'][0]['#']),
                            'RATE' => doubleval(str_replace(',', '.', $arC['#']['Value'][0]['#'])),
                            'DATE_RATE' => $adminDate,
                        );
                        COption::SetOptionString("edvance.currencyrate", $arNewRate["CURRENCY"], $arNewRate["RATE"]);
                    }
                }
                
                if (CModule::IncludeModule("iblock")) {
                //Обновляем цены у товаров
                    $rsElements = CIBlockElement::GetList(
                        array(), 
                        array("IBLOCK_CODE" => "catalog", ">PROPERTY_PRICE_USD" => 0), 
                        false, 
                        false, 
                        array("ID", "IBLOCK_ID", "NAME")
                    );

                    while ($arElement = $rsElements->GetNext()) {
                        OnAfterIBlockElementAddUpdate(array("IBLOCK_ID" => $arElement["IBLOCK_ID"], "ID" => $arElement["ID"]));
                    }
                }
            }
        }

        if ($bAgent)
            return 'CEdvancecurrencyrate::UpdateRates();';
    }

}

?>