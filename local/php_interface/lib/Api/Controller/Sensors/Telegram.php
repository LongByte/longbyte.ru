<?php


namespace Api\Controller\Sensors;


class Telegram extends \Api\Core\Base\Controller
{

    private static $token = '2013608355:AAFQx9HDR5GcOW-BBmELTn6XjtOIfYhajpU';

    public function post()
    {

        try {
            $obBot = new \TelegramBot\Api\Client($this->getToken());

            $obBot->command('ping', function ($obMessage) use ($obBot) {
                /** @var \TelegramBot\Api\Types\Message $obMessage */
                $obBot->sendMessage($obMessage->getChat()->getId(), 'pong!');
            });

            $obBot->run();

        } catch (\TelegramBot\Api\Exception $e) {
            $e->getMessage();
        }
    }

    public function installWebhook()
    {
        $sqlUrl = 'https://api.telegram.org/bot' . $this->getToken() . '/setWebhook?url=https://longbyte.ru/api/sensors-telegram/';
        $obHttpClient = new \Api\Core\Main\HttpClient();
        return $obHttpClient->post($sqlUrl);
    }

    /**
     * @return string
     */
    private function getToken(): string
    {
        return self::$token;
    }

}