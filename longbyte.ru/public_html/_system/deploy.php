<?

$_SERVER["DOCUMENT_ROOT"] = dirname(__DIR__);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$obContext = \Bitrix\Main\Application::getInstance()->getContext();
$obRequest = $obContext->getRequest();
$strData = $obRequest->getInput();
$obData = json_decode($strData);

if ($obData->pullrequest->destination->branch->name == 'master') {
    
}

//file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/_system/input.log', print_r($arData, true));
