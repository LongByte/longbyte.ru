<?
die();
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');

$rsResults = CIBlockElement::GetList(
        array(), //
        array('IBLOCK_ID' => 4, 'SECTION_ID' => 40), //
        false, //
        false, //
        array('ID', 'IBLOCK_ID', 'NAME', 'CODE')
);

$el = new CIBlockElement;

$con = array(
    41 => 36,
    42 => 37,
    43 => 38,
    44 => 39,
    45 => 40,
    46 => 41,
    47 => 42,
    
    
);

while ($obRes = $rsResults->GetNextElement()) {
    $arRes = $obRes->GetFields();

    $arNew = $arRes;
    unset($arNew['ID']);
    unset($arNew['~ID']);
    unset($arNew['IBLOCK_SECTION_ID']);
    unset($arNew['~IBLOCK_SECTION_ID']);

    $arNew['PROPERTY_VALUES'] = array();

    $arRes['PROPERTIES'] = $obRes->GetProperties();

    foreach ($arRes['PROPERTIES'] as $arProp) {
        $arNew['PROPERTY_VALUES'][$arProp['CODE']] = $arProp['VALUE'];
    }
    $arNew['PROPERTY_VALUES']['RESULT'] = '0';
    
    for ($i = 41; $i <= 47; $i++) {
        $arNew['IBLOCK_SECTION_ID'] = $i;
        $arNew['PROPERTY_VALUES']['TEST'] = $con[$i];
        echo "<pre>";
        print_r($arNew);
        echo "</pre>";
        $el->Add($arNew);
    }
}
?>