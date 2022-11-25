<?

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;

$localPath = getLocalPath("");
$bxRoot = strlen($localPath) > 0 ? rtrim($localPath, "/\\") : BX_ROOT;

require_once(Application::getDocumentRoot() . $bxRoot . '/modules/realweb.bevent/prolog.php'); // пролог модуля

class realweb_bevent extends CModule
{
    // Обязательные свойства.

    /**
     * Имя партнера - автора модуля.
     * @var string
     */
    var $PARTNER_NAME;

    /**
     * URL партнера - автора модуля.
     * @var string
     */
    var $PARTNER_URI;

    /**
     * Версия модуля.
     * @var string
     */
    var $MODULE_VERSION;

    /**
     * Дата и время создания модуля.
     * @var string
     */
    var $MODULE_VERSION_DATE;

    /**
     * Имя модуля.
     * @var string
     */
    var $MODULE_NAME;

    /**
     * Описание модуля.
     * @var string
     */
    var $MODULE_DESCRIPTION;

    /**
     * ID модуля.
     * @var string
     */
    var $MODULE_ID = 'realweb.bevent';
    private $bxRoot = BX_ROOT;

    /**
     * Конструктор класса. Задаёт начальные значения свойствам.
     */
    function __construct()
    {
        $this->PARTNER_NAME = 'Realweb';
        $this->PARTNER_URI = 'http://www.realweb.ru';
        $this->errors = array();
        $this->result = array();
        $arModuleVersion = array();

        $path = str_replace('\\', '/', __FILE__);
        $path = substr($path, 0, strlen($path) - strlen('/index.php'));
        include($path . '/version.php');

        $localPath = getLocalPath("");
        $this->bxRoot = strlen($localPath) > 0 ? rtrim($localPath, "/\\") : BX_ROOT;

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = GetMessage('REALWEB.BEVENT.NAME');
        if (Loader::includeModule($this->MODULE_ID)) {
            $this->MODULE_DESCRIPTION = GetMessage('REALWEB.BEVENT.INSTALL_DESCRIPTION');
        } else {
            $this->MODULE_DESCRIPTION = GetMessage('REALWEB.BEVENT.PREINSTALL_DESCRIPTION');
        }
    }

    function DoInstall()
    {
        $this->InstallFiles();
        if (!ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            ModuleManager::registerModule($this->MODULE_ID);
            Loader::includeModule($this->MODULE_ID);
        }
        return true;
    }

    function DoUninstall()
    {
        if (ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            ModuleManager::unRegisterModule($this->MODULE_ID);
        }

        $this->UnInstallFiles();
        return true;
    }

    function InstallFiles()
    {
        CopyDirFiles(Application::getDocumentRoot() . $this->bxRoot . "/modules/" . $this->MODULE_ID . "/install/admin/", Application::getDocumentRoot() . "/bitrix/admin/", true, true);
        CopyDirFiles(Application::getDocumentRoot() . $this->bxRoot . "/modules/" . $this->MODULE_ID . "/install/components/", Application::getDocumentRoot() . $this->bxRoot . "/components/", true, true);
        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFiles(Application::getDocumentRoot() . $this->bxRoot . "/modules/" . $this->MODULE_ID . "/install/admin/", Application::getDocumentRoot() . "/bitrix/admin/");
        DeleteDirFiles(Application::getDocumentRoot() . $this->bxRoot . "/modules/" . $this->MODULE_ID . "/install/components/", Application::getDocumentRoot() . $this->bxRoot . "/components/");
        return true;
    }

}

?>
