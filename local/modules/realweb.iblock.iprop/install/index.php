<?

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Localization\Loc;

$localPath = getLocalPath("");
$bxRoot = strlen($localPath) > 0 ? rtrim($localPath, "/\\") : BX_ROOT;

class realweb_iblock_iprop extends CModule
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
    var $MODULE_ID = 'realweb.iblock.iprop';
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
        $this->MODULE_NAME = Loc::getMessage('REALWEB.IBLOCK.IPROP.NAME');
        if (Loader::includeModule($this->MODULE_ID)) {
            $this->MODULE_DESCRIPTION = Loc::getMessage('REALWEB.IBLOCK.IPROP.INSTALL_DESCRIPTION');
        } else {
            $this->MODULE_DESCRIPTION = Loc::getMessage('REALWEB.IBLOCK.IPROP.PREINSTALL_DESCRIPTION');
        }
    }

    function DoInstall()
    {
        $this->InstallFiles();
        $this->InstallDB();
        if (!ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            ModuleManager::registerModule($this->MODULE_ID);
            Loader::includeModule($this->MODULE_ID);

            return true;
        }
    }

    function DoUninstall()
    {
        if (ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            ModuleManager::unRegisterModule($this->MODULE_ID);
        }

        $this->UnInstallDB();
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

    function InstallDB()
    {

        EventManager::getInstance()->registerEventHandler('main', 'OnAdminTabControlBegin', $this->MODULE_ID, '\Realweb\IblockIprop\IPropertyIblock', 'OnAdminTabControlBegin');
        EventManager::getInstance()->registerEventHandler('main', 'OnPageStart', $this->MODULE_ID, '\Realweb\IblockIprop\IPropertyIblock', 'OnPageStart');
    }

    function UnInstallDB()
    {
        EventManager::getInstance()->unregisterEventHandler('main', 'OnAdminTabControlBegin', $this->MODULE_ID, '\Realweb\IblockIprop\IPropertyIblock', 'OnAdminTabControlBegin');
        EventManager::getInstance()->unregisterEventHandler('main', 'OnPageStart', $this->MODULE_ID, '\Realweb\IblockIprop\IPropertyIblock', 'OnPageStart');
    }

}

?>
