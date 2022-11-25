<?

use \Realweb\RealwebMainIncludeTable;

define('BX_PUBLIC_MODE', 0);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_js.php");

$addUrl = 'lang=' . LANGUAGE_ID . ($logical == "Y" ? '&logical=Y' : '');
$useEditor3 = COption::GetOptionString('fileman', "use_editor_3", "N") == "Y";
$bFromComponent = $_REQUEST['from'] == 'main.include' || $_REQUEST['from'] == 'includefile' || $_REQUEST['from'] == 'includecomponent';

if (!($USER->CanDoOperation('fileman_admin_files') || $USER->CanDoOperation('fileman_edit_existent_files'))) {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/fileman/include.php");

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/fileman/admin/fileman_html_edit.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/public/file_edit.php");
IncludeModuleLangFile(__FILE__);

$obJSPopup = new CJSPopup("lang=" . urlencode($_GET["lang"]) . "&site=" . urlencode($_GET["site"]) . "&back_url=" . urlencode($_GET["back_url"]) . "&path=" . urlencode($_GET["path"]) . "&name=" . urlencode($_GET["name"]), array("SUFFIX" => ($_REQUEST['subdialog'] == 'Y' ? 'editor' : '')));

$strWarning = "";
$site_template = false;
$rsSiteTemplates = CSite::GetTemplateList($site);
while ($arSiteTemplate = $rsSiteTemplates->Fetch()) {
    if (strlen($arSiteTemplate["CONDITION"]) <= 0) {
        $site_template = $arSiteTemplate["TEMPLATE"];
        break;
    }
}
CModule::IncludeModule('realweb.main.include');

$io = CBXVirtualIo::GetInstance();

$bVarsFromForm = false; // if 'true' - we will get content  and variables from form, if 'false' - from saved file
$bSessIDRefresh = false; // флаг, указывающий, нужно ли обновлять ид сессии на клиенте
$editor_name = (isset($_REQUEST['editor_name']) ? $_REQUEST['editor_name'] : 'filesrc_pub');

$site = CFileMan::__CheckSite($site);

if (CAutoSave::Allowed())
    $AUTOSAVE = new CAutoSave();


if ($new == 'Y') {
    $bEdit = false;
} else {
    $bEdit = true;
}

if (strlen($_REQUEST['CODE']) == 0) {
    $strWarning = GetMessage("REALWEB.MAIN.INCLUDE.NEED.CODE");
}

if (strlen($strWarning) <= 0) {
    if ($bEdit) {
        $oFile = $io->GetFile($abs_path);
        $filesrc_tmp = $oFile->GetContents();
    } else {
        $arTemplates = CFileman::GetFileTemplates(LANGUAGE_ID, array($site_template));
        if (strlen($template) > 0) {
            foreach ($arTemplates as $arTemplate) {
                if ($arTemplate["file"] == $template) {
                    $filesrc_tmp = CFileman::GetTemplateContent($arTemplate["file"], LANGUAGE_ID, array($site_template));
                    break;
                }
            }
        } else {
            $filesrc_tmp = CFileman::GetTemplateContent($arTemplates[0]["file"], LANGUAGE_ID, array($site_template));
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && $_REQUEST['save'] == 'Y') {
        $filesrc = $filesrc_pub;
        if (!check_bitrix_sessid()) {
            $strWarning = GetMessage("FILEMAN_SESSION_EXPIRED");
            $bVarsFromForm = true;
            $bSessIDRefresh = true;
        } else {
            //найдем запись в таблице
            $res = RealwebMainIncludeTable::getByCode($_REQUEST['CODE']);
            if ($row = $res->fetch()) {
                //удалим
                RealwebMainIncludeTable::delete($row['ID']);
            }
        }


        if (strlen($strWarning) <= 0) {
            if (CAutoSave::Allowed())
                $AUTOSAVE->Reset();
        }

        if (strlen($strWarning) <= 0) {
            ?>
            <script>
                <? if ($_REQUEST['subdialog'] != 'Y'): ?>
                top.BX.reload('<?= CUtil::JSEscape($_REQUEST["back_url"]) ?>', true);
                <? else: ?>
                if (null != top.structReload)
                    top.structReload('<?= urlencode($_REQUEST["path"]) ?>');
                <? endif; ?>
                top.<?= $obJSPopup->jsPopup ?>.Close();
            </script>
            <?
        } else {
            ?>
            <script>
                top.CloseWaitWindow();
                top.<?= $obJSPopup->jsPopup ?>.ShowError('<?= CUtil::JSEscape($strWarning) ?>');
                var pMainObj = top.GLOBAL_pMainObj['<?= CUtil::JSEscape($editor_name) ?>'];
                pMainObj.Show(true);
                <? if ($bSessIDRefresh): ?>
                top.BXSetSessionID('<?= CUtil::JSEscape(bitrix_sessid()) ?>');
                <? endif; ?>
            </script>
            <?
        }
        die();
    }
} else {
    ?>
    <script>
        top.CloseWaitWindow();
        top.<?= $obJSPopup->jsPopup ?>.ShowError('<?= CUtil::JSEscape($strWarning) ?>');
        var pMainObj = top.GLOBAL_pMainObj['<?= CUtil::JSEscape($editor_name) ?>'];
        pMainObj.Show(true);
    </script>
    <?
    die();
}

if (!$bVarsFromForm) {
    //найдем запись в таблице
    $res_find = RealwebMainIncludeTable::getByCode($_REQUEST['CODE']);
    if ($row = $res_find->fetch()) {
        $filesrc = $row['TEXT'];
    } else {
        $filesrc = '';
    }

    if ((CFileman::IsPHP($filesrc) || $isScriptExt) && !($USER->CanDoOperation('edit_php') || $limit_php_access))
        $strWarning = GetMessage("FILEMAN_FILEEDIT_CHANGE_ACCESS");
}

$obJSPopup->ShowTitlebar(GetMessage('PUBLIC_DELETE_TITLE' . ($bFromComponent ? '_COMP' : '')) . ': ' . htmlspecialcharsex($_GET['CODE']));


$obJSPopup->StartContent(
    array(
        'style' => "0px; height: 50px; overflow: hidden;",
        'class' => "",
    )
);
?>
    </form>
    <iframe src="javascript:void(0)" name="file_edit_form_target" height="0" width="0" style="display: none;"></iframe>
    <form action="/bitrix/admin/main_include_public_delete.php" name="editor_form" method="post" enctype="multipart/form-data" target="file_edit_form_target" style="margin: 0px; padding: 0px; ">
        <?
        if (CAutoSave::Allowed()) {
            echo CJSCore::Init(array('autosave'), true);
            $AUTOSAVE->Init();
            ?>
            <script type="text/javascript">BX.WindowManager.Get().setAutosave();</script><?
        }
        ?>
        <?= bitrix_sessid_post() ?>
        <input type="submit" name="submitbtn" style="display: none;" />
        <input type="hidden" name="mode" id="mode" value="public" />
        <input type="hidden" name="save" id="save" value="Y" />
        <input type="hidden" name="site" id="site" value="<?= htmlspecialcharsbx($site) ?>" />
        <input type="hidden" name="template" id="template" value="<? echo htmlspecialcharsbx($template) ?>" />
        <input type="hidden" name="templateID" id="templateID" value="<? echo htmlspecialcharsbx($_REQUEST['templateID']) ?>" />
        <input type="hidden" name="subdialog" value="<? echo htmlspecialcharsbx($_REQUEST['subdialog']) ?>" />
        <? if (is_set($_REQUEST, 'back_url')): ?>
            <input type="hidden" name="back_url" value="<?= htmlspecialcharsbx($_REQUEST['back_url']) ?>" />
        <? endif; ?>
        <? if (is_set($_REQUEST, 'edit_new_file_undo')): ?>
            <input type="hidden" name="edit_new_file_undo" value="<?= htmlspecialcharsbx($_REQUEST['edit_new_file_undo']) ?>" />
        <? endif; ?>
        <? if (!$bEdit): ?>
            <input type="hidden" name="new" id="new" value="Y" />
            <input type="hidden" name="CODE" id="CODE" value="<? echo htmlspecialcharsbx($CODE) ?>" />
        <? else: ?>
            <input type="hidden" name="CODE" id="CODE" value="<? echo htmlspecialcharsbx($CODE) ?>" />
        <? endif; ?>

        <script>
            <?= $obJSPopup->jsPopup ?>.PARTS.CONTENT.getElementsByTagName('FORM')[0].style.display = 'none'; // hack

            function BXFormSubmit() {
                ShowWaitWindow();
                var obForm = document.forms.editor_form;
                obForm.elements.submitbtn.click();
            }

            function BXSetSessionID(new_sessid) {
                document.forms.editor_form.sessid.value = new_sessid;
            }
        </script>
        <p>Вы уверены?</p>
        <style>
            .adm-workarea .adm-btn-save.adm-btn-delete {
                background-color: #dc2727 !important;
                -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .25), inset 0 1px 0 #dc2727;
                box-shadow: 0 1px 1px rgba(0, 0, 0, .25), inset 0 1px 0 #dc2727;
                border: solid 1px;
                border-color: #dc2727 #dc2727 #a50606;
                background-image: -webkit-linear-gradient(bottom, #de5a5a, #b92020) !important;
            }

            .adm-workarea .adm-btn-save.adm-btn-delete:hover {
                background-color: #b73737 !important;
                -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .25), inset 0 1px 0 #b73737;
                box-shadow: 0 1px 1px rgba(0, 0, 0, .25), inset 0 1px 0 #b73737;
                border: solid 1px;
                border-color: #b73737 #b73737 #a50606;
                background-image: -webkit-linear-gradient(bottom, #c50c0c, #b92020) !important;
            }

            .adm-workarea input.adm-btn-save.adm-btn-delete:active {
                background-color: #b73737 !important;
                -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .25), inset 0 1px 0 #b73737;
                box-shadow: 0 1px 1px rgba(0, 0, 0, .25), inset 0 1px 0 #b73737;
                border: solid 1px;
                border-color: #b73737 #b73737 #a50606;
                background-image: -webkit-linear-gradient(bottom, #c50c0c, #b92020) !important;
                background: #b92020 !important;
                border-color: transparent #b92020 #b92020 !important;
            }

        </style>
        <?php
        $obJSPopup->StartButtons();
        ?>
        <input type="button" class="adm-btn-save adm-btn-delete" id="btn_popup_save" name="btn_popup_save" value="Удалить" onclick="BXFormSubmit();" title="Удалить" />
<?
$obJSPopup->ShowStandardButtons(array('cancel'));
$obJSPopup->EndButtons();

if (CAutoSave::Allowed()) {
    $AUTOSAVE->checkRestore();
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_js.php");
?>