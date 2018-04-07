<?
global $MESS;
$PathInstall = str_replace('\\', '/', __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen('/index.php'));
IncludeModuleLangFile($PathInstall.'/install.php');
include($PathInstall.'/version.php');

if (class_exists('edvance_currencyrate')) return;

class edvance_currencyrate extends CModule
{
	public $MODULE_ID = "edvance.currencyrate";
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $PARTNER_NAME;
	public $PARTNER_URI;
	public $MODULE_GROUP_RIGHTS = 'N';

	public function __construct()
	{
		$arModuleVersion = array();

		$path = str_replace('\\', '/', __FILE__);
		$path = substr($path, 0, strlen($path) - strlen('/index.php'));
		include($path.'/version.php');

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		$this->MODULE_NAME = GetMessage('ASD_MODULE_CRT_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('ASD_MODULE_CRT_DESCRIPTION');
	}

	public function DoInstall()
	{
        RegisterModule($this->MODULE_ID);
        CAgent::AddAgent('CEdvancecurrencyrate::UpdateRates();', $this->MODULE_ID, 'Y');
        $this->ShowForm('OK', GetMessage('MOD_INST_OK'));
	}

	public function DoUninstall()
	{
		UnRegisterModuleDependences('main', 'OnAdminListDisplay', $this->MODULE_ID, 'CEdvancecurrencyrate', 'OnAdminListDisplayHandler');
		UnRegisterModuleDependences('main', 'OnBeforeProlog', $this->MODULE_ID, 'CEdvancecurrencyrate', 'OnBeforePrologHandler');
		CAgent::RemoveModuleAgents($this->MODULE_ID);
		UnRegisterModule($this->MODULE_ID);
		$this->ShowForm('OK', GetMessage('MOD_UNINST_OK'));
	}

	private function ShowForm($type, $message, $buttonName='')
	{
		$keys = array_keys($GLOBALS);
		for($i=0; $i<count($keys); $i++)
			if($keys[$i]!='i' && $keys[$i]!='GLOBALS' && $keys[$i]!='strTitle' && $keys[$i]!='filepath')
				global ${$keys[$i]};

		$PathInstall = str_replace('\\', '/', __FILE__);
		$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen('/index.php'));
		IncludeModuleLangFile($PathInstall.'/install.php');

		$APPLICATION->SetTitle(GetMessage('ASD_MODULE_CRT_NAME'));
		include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
		echo CAdminMessage::ShowMessage(array('MESSAGE' => $message, 'TYPE' => $type));
		?>
		<form action="<?= $APPLICATION->GetCurPage()?>" method="get">
		<p>
			<input type="hidden" name="lang" value="<?= LANG?>" />
			<input type="submit" value="<?= strlen($buttonName) ? $buttonName : GetMessage('MOD_BACK')?>" />
		</p>
		</form>
		<?
		include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
		die();
	}
}
?>