<?

use Bitrix\Main\IO;
use Bitrix\Main\Application;

define("NOT_CHECK_PERMISSIONS", true);

$_SERVER["DOCUMENT_ROOT"] = dirname(__DIR__);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$obContext = \Bitrix\Main\Application::getInstance()->getContext();
$obRequest = $obContext->getRequest();
$strData = $obRequest->getInput();
$obData = json_decode($strData);

$obLogFile = new IO\File(Application::getDocumentRoot() . '/_system/deploy.log');
$obDebugFile = new IO\File(Application::getDocumentRoot() . '/_system/debug.log');
$obDebugFile->putContents(print_r($obData, true) . PHP_EOL, IO\File::APPEND);

if (isset($obData->push)) {
    foreach ($obData->push->changes as $obChange) {
        if ($obChange->new->name == 'master') {
            $obLogFile->putContents(PHP_EOL . '============' . PHP_EOL, IO\File::APPEND);
            $obLogFile->putContents(date('d.m.Y H:i:s') . PHP_EOL, IO\File::APPEND);
            $obLogFile->putContents('Branch: ' . $obChange->new->name . PHP_EOL, IO\File::APPEND);
            $obLogFile->putContents('Commits:' . PHP_EOL, IO\File::APPEND);
            foreach ($obChange->commits as $obCommit) {
                $obLogFile->putContents($obCommit->hash . ' [' . $obCommit->date . ']' . ' ' . $obCommit->author->user->display_name . ': ' . $obCommit->message . PHP_EOL, IO\File::APPEND);
            }
            shell_exec('~/deploy.sh');
        }
    }
}
?>
