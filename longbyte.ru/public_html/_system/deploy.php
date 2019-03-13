<?

$_SERVER["DOCUMENT_ROOT"] = dirname(__DIR__);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$obContext = \Bitrix\Main\Application::getInstance()->getContext();
$obRequest = $obContext->getRequest();
$strData = $obRequest->getInput();
$obData = json_decode($strData);

if ($obData->pullrequest->destination->branch->name == 'master') {
    shell_exec('~/deploy.sh');
}

?>