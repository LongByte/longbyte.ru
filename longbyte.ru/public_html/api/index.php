<?

use Bitrix\Main\Context;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$obRequest = Context::getCurrent()->getRequest();

$strModule = $obRequest->get('module');
$strModule = strtoupper(substr($strModule, 0, 1)) . substr($strModule, 1);
$strController = $obRequest->get('controller');
$strController = strtoupper(substr($strController, 0, 1)) . substr($strController, 1);
$strClassName = '\\Api\\Controller\\' . $strModule . '\\' . $strController;
if (class_exists($strClassName)) {
    $obController = new $strClassName;
    $strMethod = strtolower($obRequest->getRequestMethod());
    if (method_exists($obController, $strMethod)) {
        echo $obController->$strMethod();
    }
}
