<?

namespace LongByte;

use Bitrix\Main\EventManager;
use Bitrix\Main\Context;

class Babel {

    const BABEL_DISABLE = 0;
    const BABEL_CLIENT = 1;
    const BABEL_SERVER = 2;
    const BABEL_SERVER_CLIENT = 3;

    public static $rootDir = '~/web/'; //not document root
    public static $babelMode = self::BABEL_DISABLE;

    public static function includeBabel($mode) {
        self::$babelMode = $mode;
        switch (self::$babelMode) {
            case self::BABEL_CLIENT:
                EventManager::getInstance()->addEventHandler('main', 'onEpilog', array('\LongByte\Babel', 'onEpilog_Client'));
                EventManager::getInstance()->addEventHandler('main', 'OnEndBufferContent', array('\LongByte\Babel', 'OnEndBufferContent_Client'));
                break;
            case self::BABEL_SERVER:
            case self::BABEL_SERVER_CLIENT:
                EventManager::getInstance()->addEventHandler('main', 'OnEndBufferContent', array('\LongByte\Babel', 'OnEndBufferContent_ServerClient'));
                break;
        }
    }

    public static function onEpilog_Client() {
        if (\Site::isIE()) {
            Asset::getInstance()->addString('<script src="https://unpkg.com/babel-standalone@6/babel.min.js"></script>');
        } else {
            Asset::getInstance()->addString('<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>');
        }
    }

    public static function OnEndBufferContent_Client(&$content) {
        if (\Site::isIE()) {
            $content = preg_replace('/(<script\s+type="text\/)javascript("\s+src="\/bitrix\/cache\/js\/' . SITE_ID . '\/' . SITE_TEMPLATE_ID . '\/template_[^"]+\.js\?\d+"><\/script>)/i', '$1babel$2', $content);
            $content = preg_replace('/(<script\s+type="text\/)javascript("\s+src="\/bitrix\/cache\/js\/' . SITE_ID . '\/' . SITE_TEMPLATE_ID . '\/page_[^"]+\.js\?\d+"><\/script>)/i', '$1babel$2', $content);
        }
    }

    public static function OnEndBufferContent_ServerClient(&$content) {

        $obServer = Context::getCurrent()->getServer();
        $arReplaces = array();
        $bSuccess = true;

        if (preg_match_all('/<script\s+type="text\/javascript"\s+src="(\/bitrix\/cache\/js\/' . SITE_ID . '\/' . SITE_TEMPLATE_ID . '\/(template|page)_[^"]+\.js)\?\d+"><\/script>/i', $content, $arMatches)) {
            foreach ($arMatches[1] as $match) {
                $sourceFile = $match;
                $destFile = str_replace('.js', '.es.js', $sourceFile);
                if (!file_exists($obServer->getDocumentRoot() . $destFile)) {
                    /*
                     * Предварительно в папке self::$rootDir надо выполнить:
                     * npm install --save-dev babel-cli babel-preset-env
                     */
                    $cmd = 'cd ' . self::$rootDir . ' && npx babel ' . $obServer->getDocumentRoot() . $sourceFile . ' --presets babel-preset-env --out-file ' . $obServer->getDocumentRoot() . $destFile . '';
                    exec($cmd);
                }
                if (file_exists($obServer->getDocumentRoot() . $destFile)) {
                    $arReplaces[$sourceFile] = $destFile;
                } else {
                    $bSuccess = false;
                    if (self::$babelMode == self::BABEL_SERVER_CLIENT) {
                        self::emergencyEnableClientBabel($content);
                    }
                    break;
                }
            }
        }

        if ($bSuccess && count($arReplaces) > 0) {
            $content = str_replace(array_keys($arReplaces), array_values($arReplaces), $content);
        }
    }

    private static function emergencyEnableClientBabel(&$content) {
        if (\Site::isIE()) {
            $content = str_replace('</body>', '<script src="https://unpkg.com/babel-standalone@6/babel.min.js"></script></body>', $content);
        } else {
            $content = str_replace('</body>', '<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script></body>', $content);
        }
        $content = str_replace('</body>', '<script>console.log(\'Babel не смог отработать на сервере, включена клиентская версия\');</script></body>', $content);
    }

}
