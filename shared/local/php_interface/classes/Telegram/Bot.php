<?

namespace LongByte\Telegram;

use Bitrix\Main\Context;
use Bitrix\Main\Web\HttpClient;

class Bot {

    private $token = '851812257:AAGrqK6eXVcgQ7-MeF_kRAgR21HKDH0J2UU';
    private $urlQueryTemplate = 'https://api.telegram.org/bot#token#/#method#';
    private $urlFileTemplate = 'https://api.telegram.org/file/bot#token#/#filepath#';
    protected $obHttpClient;
    protected $obRequest;
    protected $obServer;
    protected $obInput;
    protected $arCurrentSession;

    public function __construct($bDisableAnser = false) {
        $this->obHttpClient = new HttpClient();
        $this->obRequest = Context::getCurrent()->getRequest();
        $this->obServer = Context::getCurrent()->getServer();
        $this->obInput = json_decode($this->obRequest->getInput());

        if (!$bDisableAnser && $this->isIncomeMessage()) {
            $this->doAnswer();
        }
    }

    public function isDevelopServer() {
        return true;
    }

    public function getRequest() {
        return $this->obRequest;
    }

    public function getInput() {
        return $this->obInput;
    }

    /**
     * Проверка, запуск от вебхука и верный ли токен
     * @return type
     */
    private function isIncomeMessage() {
        return $this->obRequest->isPost() && $this->obRequest->get('token') == $this->token;
    }

    /**
     * Ответ ботом
     */
    private function doAnswer() {

        if (isset($this->obInput->message)) {
            $obMessage = $this->obInput->message;
            $strMessage = $obMessage->text;
        }

        $this->arCurrentSession = SessionTable::getSession($obMessage->chat->id);

        if (empty($strMessage) && empty($obMessage->photo)) {
            return false;
        }
    }

    /**
     * Генерирует ссылку для запроса
     * @param type $strMethod
     * @param type $strParams
     * @return type
     */
    protected function getRequestUrl($strMethod, $strParams = '') {
        return str_replace(['#token#', '#method#'], [$this->token, $strMethod], $this->urlQueryTemplate) . ($strParams ? '?' . $strParams : '');
    }

    /**
     * Генерирует ссылку для скачивания файла
     * @param type $strFilepath
     * @return type
     */
    protected function getFileUrl($strFilepath) {
        return str_replace(['#token#', '#filepath#'], [$this->token, $strFilepath], $this->urlFileTemplate);
    }

    /**
     * Отправка сообщения
     * @param string $strText
     * @param array $arReplayMarkup
     * @param string $strChatID
     */
    public function sendMessage($strText, $arReplayMarkup = [], $strChatID = false) {
        $arRequest = [
            'chat_id' => $strChatID ?: $this->arCurrentSession['CHAT_ID'],
            'text' => $strText,
            'parse_mode' => 'Markdown',
        ];

        if (is_array($arReplayMarkup) && count($arReplayMarkup) > 0) {
            $arRequest['reply_markup'] = json_encode($arReplayMarkup);
        }

        $arResponse = json_decode($this->obHttpClient->post($this->getRequestUrl('sendMessage'), $arRequest));

        if ($this->isDevelopServer()) {
            file_put_contents($this->obServer->getDocumentRoot() . '/ajax/tg.log', 'Request:' . print_r($arRequest, true), FILE_APPEND);
            file_put_contents($this->obServer->getDocumentRoot() . '/ajax/tg.log', 'Response:' . print_r($arResponse, true), FILE_APPEND);
        }
    }

    /**
     * Устанавливает вебхук
     */
    public function setWebhook() {

        $arResponse = json_decode(
            $this->obHttpClient->post(
                $this->getRequestUrl('setWebHook', 'url=' . 'http' . ($this->obRequest->isHttps() ? 's' : '') . '://' . $this->obServer->getHttpHost() . '/_system/tgbot.php?token=' . $this->token)
            )
        );
        echo '<pre>setWebhook:';
        var_dump($arResponse);
        echo '</pre>';
    }

    /**
     * Удаляет вебхук
     */
    public function deleteWebhook() {
        $arResponse = json_decode($this->obHttpClient->post($this->getRequestUrl('deleteWebhook')));
        echo '<pre>deleteWebhook:';
        print_r($arResponse);
        echo '</pre>';
    }

}
