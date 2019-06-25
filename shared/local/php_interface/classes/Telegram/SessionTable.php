<?

namespace LongByte\Telegram;

use Bitrix\Main;
use Bitrix\Main\Application;

/**
 * Class SessionTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> CHAT_ID string(64) optional
 * <li> TIMESTAMP_X datatime optional
 * <li> ORDER_ID var(32) optional
 * <li> MODE int optional
 * </ul>
 * */
class SessionTable extends Main\Entity\DataManager {

    const MODE_ORDER = 1;
    const MODE_CLIENT = 2;
    const MODE_CONFIRM = 3;

    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName() {
        return 'b_telegram_sessions';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap() {
        return [
            'ID' => [
                'data_type' => 'integer',
                'primary' => true,
                'title' => 'ID',
            ],
            'CHAT_ID' => [
                'data_type' => 'string',
                'title' => 'ID чата',
            ],
            'TIMESTAMP_X' => [
                'data_type' => 'datetime',
                'title' => 'Дата изменения',
            ],
        ];
    }

    /**
     * Создание таблицы в БД
     */
    public static function install() {
        $obDB = Application::getConnection();

        $sql = 'CREATE TABLE IF NOT EXISTS ' . self::getTableName() . ' ('
            . 'ID int AUTO_INCREMENT NOT NULL,'
            . 'CHAT_ID varchar(64) NOT NULL,'
            . 'TIMESTAMP_X datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,'
            . 'PRIMARY KEY (ID)'
            . ')  ENGINE=INNODB;';

        $obDB->query($sql);
    }

    /**
     * Получение сессии по ID чата
     * @param string $chatID
     * @return boolean/array
     */
    public static function getByChatID($chatID) {

        if (!$chatID) {
            return false;
        }

        $arRow = self::getList(
                [
                    'filter' => ['CHAT_ID' => $chatID],
                    'limit' => 1,
                ]
            )->fetch();

        return $arRow;
    }

    /**
     * Получение/создание сессии
     * @param string $chatID
     * @return bool|array
     */
    public static function getSession($chatID) {

        if (!$chatID) {
            return false;
        }

        $arCurrentSession = self::getByChatID($chatID);
        if ($arCurrentSession) {
            return $arCurrentSession;
        }

        self::Add(
            [
                'CHAT_ID' => $chatID,
            ]
        );

        $arCurrentSession = self::getByChatID($chatID);
        if ($arCurrentSession) {
            return $arCurrentSession;
        } else {
            return false;
        }
    }

    /**
     * Обнуляет все в сессии кроме ID чата
     * @param string $chatID
     * @return array
     */
    public static function restartSession($chatID) {
        $arRow = self::getList(
                [
                    'filter' => ['CHAT_ID' => $chatID],
                    'limit' => 1,
                ]
            )->fetch();

        self::update(
            $arRow['ID'], [
            'MODE' => null,
            'ORDER_ID' => '',
            ]
        );

        return self::getSession($chatID);
    }

}
