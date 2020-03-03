<?

use Bitrix\Main\Context;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$obRequest = Context::getCurrent()->getRequest();

$strModule = $obRequest->get('module');
$strController = $obRequest->get('controller');
$strClassName = '\\Api\\' . $strModule . '\\' . $strController;
if (class_exists($strClassName)) {
    $obController = new $strClassName;
    $strMethod = strtolower($obRequest->getRequestMethod());
    if (method_exists($obController, $strMethod)) {
        echo $obController->$strMethod();
    }
}
