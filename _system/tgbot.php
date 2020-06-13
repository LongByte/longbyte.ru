<?

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

file_put_contents('tg.log', '');

//LongByte\Telegram\SessionTable::install();

$obTgBot = new \LongByte\Telegram\Bot(true);
$obInput = $obTgBot->getInput();

//$obTgBot->deleteWebhook();
$obTgBot->setWebhook();

//if ($obTgBot->isDevelopServer()) {
    file_put_contents('tg.log', 'Input:' . print_r($obInput, true), FILE_APPEND);
//}
?>