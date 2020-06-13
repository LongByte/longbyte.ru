<?

use Bitrix\Main\Application;
use Realweb\RealwebMainIncludeTable;

$localPath = getLocalPath("");
$bxRoot = strlen($localPath) > 0 ? rtrim($localPath, "/\\") : BX_ROOT;

Class realweb_helper extends CModule {

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
    var $MODULE_ID = 'realweb.helper';
    private $bxRoot = BX_ROOT;

    /**
     * Конструктор класса. Задаёт начальные значения свойствам.
     */
    function realweb_helper() {
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
        $this->MODULE_NAME = 'Вспомогательный модуль сайта';
    }

    function DoInstall() {
        $this->InstallFiles();
        $included = false;
        if (!CModule::IncludeModule($this->MODULE_ID)) {
            RegisterModule($this->MODULE_ID);
            $included = CModule::IncludeModule($this->MODULE_ID);
        }
    }

    function InstallFiles() {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/", true, true);

        return true;
    }

    function DoUninstall() {
        global $DB, $APPLICATION;

        if (CModule::IncludeModule($this->MODULE_ID)) {
            UnRegisterModule($this->MODULE_ID);
        }
        $this->UnInstallFiles();
    }

    function UnInstallFiles() {
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/");
        return true;
    }

}

?>
