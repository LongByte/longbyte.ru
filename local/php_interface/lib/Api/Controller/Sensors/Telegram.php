<?php


namespace Api\Controller\Sensors;

/**
 * Class \Api\Controller\Sensors\Telegram
 */
class Telegram extends \Api\Core\Base\Controller
{

    protected static string $strHelp = "Добро пожаловать в систему мониторинга. Доступны следующие команды:
/add token - добавление отслеживания системы
/del token - прекращение отслеживания системы
/clear - прекращение отслеживания всех систем
/mute [token] - отключение уведомлений
/unmute [token] - включение уведомлений
/list - список отслеживаемых систем
/help - это сообщение";

    public function post()
    {

        $obBot = new \TelegramBot\Api\Client($this->getToken());

        $obBot->command('start', function ($obMessage) use ($obBot) {
            /** @var \TelegramBot\Api\Types\Message $obMessage */
            $obBot->sendMessage($obMessage->getChat()->getId(), static::$strHelp);
        });

        $obBot->command('help', function ($obMessage) use ($obBot) {
            /** @var \TelegramBot\Api\Types\Message $obMessage */
            $obBot->sendMessage($obMessage->getChat()->getId(), static::$strHelp);
        });

        $obBot->command('add', function ($obMessage) use ($obBot) {
            /** @var \TelegramBot\Api\Types\Message $obMessage */
            $strText = preg_replace('/^\/add\s+/', '', $obMessage->getText());
            /** @var \Api\Sensors\System\Entity $obSystem */
            $obSystem = \Api\Sensors\System\Model::getOne(array('TOKEN' => $strText));
            if (!is_null($obSystem)) {
                /** @var \Api\Sensors\Telegram\Entity $obTelegram */
                $obTelegram = \Api\Sensors\Telegram\Model::getOne(array(
                    'SYSTEM_ID' => $obSystem->getId(),
                    'CHAT_ID' => $obMessage->getChat()->getId(),
                ));
                if (!is_null($obTelegram)) {
                    $obBot->sendMessage($obMessage->getChat()->getId(), 'Система уже отслеживается');
                } else {
                    $obTelegram = new \Api\Sensors\Telegram\Entity();
                    $obTelegram
                        ->setChatId($obMessage->getChat()->getId())
                        ->setSystemId($obSystem->getId())
                        ->setActive(1)
                        ->save()
                    ;
                    $obBot->sendMessage($obMessage->getChat()->getId(), 'Система успешно добавлена');
                }
            } else {
                $obBot->sendMessage($obMessage->getChat()->getId(), 'Система не найдена');
            }
        });

        $obBot->command('del', function ($obMessage) use ($obBot) {
            /** @var \TelegramBot\Api\Types\Message $obMessage */
            $strText = preg_replace('/^\/del\s+/', '', $obMessage->getText());
            /** @var \Api\Sensors\System\Entity $obSystem */
            $obSystem = \Api\Sensors\System\Model::getOne(array('TOKEN' => $strText));
            if (!is_null($obSystem)) {
                /** @var \Api\Sensors\Telegram\Entity $obTelegram */
                $obTelegram = \Api\Sensors\Telegram\Model::getOne(array(
                    'SYSTEM_ID' => $obSystem->getId(),
                    'CHAT_ID' => $obMessage->getChat()->getId(),
                ));
                if (is_null($obTelegram)) {
                    $obBot->sendMessage($obMessage->getChat()->getId(), 'Система ранее не была добавлена');
                } else {
                    $obTelegram->delete();
                    $obBot->sendMessage($obMessage->getChat()->getId(), 'Отслеживание системы успешно прекращено');
                }
            } else {
                $obBot->sendMessage($obMessage->getChat()->getId(), 'Система не найдена');
            }
        });

        $obBot->command('clear', function ($obMessage) use ($obBot) {
            /** @var \TelegramBot\Api\Types\Message $obMessage */
            /** @var \Api\Sensors\Telegram\Collection $obTelegrams */
            $obTelegrams = \Api\Sensors\Telegram\Model::getAll(array(
                'CHAT_ID' => $obMessage->getChat()->getId(),
            ));
            /** @var \Api\Sensors\Telegram\Entity $obTelegram */
            foreach ($obTelegrams as $obTelegram) {
                $obTelegram->delete();
            }
            $obBot->sendMessage($obMessage->getChat()->getId(), 'Отслеживание всех систем успешно прекращено');
        });

        $obBot->command('mute', function ($obMessage) use ($obBot) {
            /** @var \TelegramBot\Api\Types\Message $obMessage */
            $strText = preg_replace('/^\/mute\s*/', '', $obMessage->getText());
            if (strlen($strText) > 0) {
                $obSystem = \Api\Sensors\System\Model::getOne(array('TOKEN' => $strText));
                if (!is_null($obSystem)) {
                    /** @var \Api\Sensors\Telegram\Collection $obTelegrams */
                    $obTelegrams = \Api\Sensors\Telegram\Model::getAll(array(
                        'CHAT_ID' => $obMessage->getChat()->getId(),
                        'SYSTEM_ID' => $obSystem->getId(),
                    ));
                } else {
                    $obBot->sendMessage($obMessage->getChat()->getId(), 'Система не найдена');
                }
            } else {
                /** @var \Api\Sensors\Telegram\Collection $obTelegrams */
                $obTelegrams = \Api\Sensors\Telegram\Model::getAll(array(
                    'CHAT_ID' => $obMessage->getChat()->getId(),
                ));
            }

            /** @var \Api\Sensors\Telegram\Entity $obTelegram */
            foreach ($obTelegrams as $obTelegram) {
                $obTelegram
                    ->setActive(0)
                    ->save()
                ;
            }
            $obBot->sendMessage($obMessage->getChat()->getId(), 'Оповещения выключены');
        });

        $obBot->command('unmute', function ($obMessage) use ($obBot) {
            /** @var \TelegramBot\Api\Types\Message $obMessage */
            $strText = preg_replace('/^\/unmute\s*/', '', $obMessage->getText());
            if (strlen($strText) > 0) {
                $obSystem = \Api\Sensors\System\Model::getOne(array('TOKEN' => $strText));
                if (!is_null($obSystem)) {
                    /** @var \Api\Sensors\Telegram\Collection $obTelegrams */
                    $obTelegrams = \Api\Sensors\Telegram\Model::getAll(array(
                        'CHAT_ID' => $obMessage->getChat()->getId(),
                        'SYSTEM_ID' => $obSystem->getId(),
                    ));
                } else {
                    $obBot->sendMessage($obMessage->getChat()->getId(), 'Система не найдена');
                }
            } else {
                /** @var \Api\Sensors\Telegram\Collection $obTelegrams */
                $obTelegrams = \Api\Sensors\Telegram\Model::getAll(array(
                    'CHAT_ID' => $obMessage->getChat()->getId(),
                ));
            }

            /** @var \Api\Sensors\Telegram\Entity $obTelegram */
            foreach ($obTelegrams as $obTelegram) {
                $obTelegram
                    ->setActive(1)
                    ->save()
                ;
            }
            $obBot->sendMessage($obMessage->getChat()->getId(), 'Оповещения включены');
        });

        $obBot->command('list', function ($obMessage) use ($obBot) {
            /** @var \TelegramBot\Api\Types\Message $obMessage */
            /** @var \Api\Sensors\Telegram\Collection $obTelegrams */
            $obTelegrams = \Api\Sensors\Telegram\Model::getAll(array(
                'CHAT_ID' => $obMessage->getChat()->getId(),
            ));

            if ($obTelegrams->count() > 0) {
                /** @var \Api\Sensors\System\Collection $obSystems */
                $obSystems = \Api\Sensors\System\Model::getAll(array('ID' => $obTelegrams->getSystemIds()));
                $arSystems = array();
                /** @var \Api\Sensors\System\Entity $obSystem */
                foreach ($obSystems as $obSystem) {
                    $strUrl = 'https://longbyte.ru/sensors/' . $obSystem->getNameToken() . '/';
                    $arSystems[] = '<a href="' . $strUrl . '">' . $obSystem->getName() . '</a> [' . $obSystem->getToken() . ']';
                }
                $obBot->sendMessage($obMessage->getChat()->getId(), implode("\n", $arSystems), 'html', false);
            } else {
                $obBot->sendMessage($obMessage->getChat()->getId(), 'Ни одна система не отслеживается');
            }
        });

        $obBot->run();
    }

    /**
     * @return bool|string
     */
    public function installWebhook()
    {
        $sqlUrl = 'https://api.telegram.org/bot' . $this->getToken() . '/setWebhook?url=https://longbyte.ru/api/sensors/telegram/';
        $obHttpClient = new \Api\Core\Main\HttpClient();
        return $obHttpClient->post($sqlUrl);
    }

    /**
     * @return bool|string
     */
    public function uninstallWebhook()
    {
        $sqlUrl = 'https://api.telegram.org/bot' . $this->getToken() . '/deleteWebhook';
        $obHttpClient = new \Api\Core\Main\HttpClient();
        return $obHttpClient->post($sqlUrl);
    }

    private function getToken(): string
    {
        return \Api\Sensors\Telegram\Model::getToken();
    }

}