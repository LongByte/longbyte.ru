<?
use \Realweb\RealwebMainIncludeTable;
define('BX_PUBLIC_MODE', 0);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_js.php");

$addUrl = 'lang=' . LANGUAGE_ID . ($logical == "Y" ? '&logical=Y' : '');
$useEditor3 = COption::GetOptionString('fileman', "use_editor_3", "N") == "Y";
$bFromComponent = $_REQUEST['from'] == 'main.include' || $_REQUEST['from'] == 'includefile' || $_REQUEST['from'] == 'includecomponent';
$bDisableEditor = !CModule::IncludeModule('fileman') || ($_REQUEST['noeditor'] == 'Y');

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

if(strlen($_REQUEST['CODE']) == 0){
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
                //обновим
                $data = array(
                    'TEXT'=>$filesrc
                );
                RealwebMainIncludeTable::update($row['ID'],$data);
            } else {
                //создадим
                $data = array(
                    'CODE'=>$_REQUEST['CODE'],
                    'TEXT'=>$filesrc
                );
                RealwebMainIncludeTable::add($data);
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
        }
        else {
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
}
else {
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

$obJSPopup->ShowTitlebar(GetMessage('PUBLIC_EDIT_TITLE' . ($bFromComponent ? '_COMP' : '')) . ': ' . htmlspecialcharsex($_GET['CODE']));


$obJSPopup->StartContent(
        array(
            'style' => "0px; height: 500px; overflow: hidden;",
            'class' => "bx-content-editor"
        )
);
?>
</form>
<iframe src="javascript:void(0)" name="file_edit_form_target" height="0" width="0" style="display: none;"></iframe>
<form action="/bitrix/admin/main_include_public_edit.php" name="editor_form" method="post" enctype="multipart/form-data" target="file_edit_form_target" style="margin: 0px; padding: 0px; ">
<?
if (CAutoSave::Allowed()) {
    echo CJSCore::Init(array('autosave'), true);
    $AUTOSAVE->Init();
    ?><script type="text/javascript">BX.WindowManager.Get().setAutosave();</script><?
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

        function BXFormSubmit()
        {
            ShowWaitWindow();
            var obForm = document.forms.editor_form;
            obForm.elements.submitbtn.click();
        }

        function BXSetSessionID(new_sessid)
        {
            document.forms.editor_form.sessid.value = new_sessid;
        }
    </script>

<?
if (!$bDisableEditor) {
    /*     * ************ HTML EDITOR 3.0 ************* */
    if ($useEditor3) {
        $Editor = new CHTMLEditor;
        $Editor->Show(array(
            'name' => $editor_name,
            'id' => $editor_name,
            'width' => '100%',
            'height' => '490',
            'content' => $filesrc,
            'bAllowPhp' => $USER->CanDoOperation('edit_php'),
            "limitPhpAccess" => $limit_php_access,
            "site" => $site,
            "relPath" => $relPath,
            "templateId" => $_REQUEST['templateID'],
            'showComponents' => false,
        ));
        ?>
            <script>
                (function () {
                    var
                            editorDialog = BX.WindowManager.Get(),
                            editor = top.BXHtmlEditor.Get('<?= CUtil::JSEscape($editor_name) ?>');

                    if (editor.IsInited())
                    {
                        onEditorInited();
                    } else
                    {
                        BX.addCustomEvent(editor, "OnEditorInitedAfter", onEditorInited);
                    }

                    BX.addCustomEvent(editorDialog, 'onWindowResizeExt', onEditorDialogResize);
                    BX.addCustomEvent(editorDialog, 'onBeforeWindowClose', onBeforeDialogClose);
                    BX.addCustomEvent(editorDialog, 'onWindowUnRegister', onEditorUnregister);

                    function onEditorDialogResize(params)
                    {
                        if (this.offsetTop === undefined)
                            this.offsetTop = editor.CheckBrowserCompatibility() ? 0 : 40;

                        var
                                width = params.width,
                                height = params.height - this.offsetTop;

                        editor.SetConfigHeight(height);
                        editor.ResizeSceleton(width, height);
                    }

                    function onEditorInited()
                    {
                        onEditorDialogResize(editorDialog.GetInnerPos());
                        function ConfirmExitDialog(editor)
                        {
                            var params = {
                                id: 'bx_confirm_exit',
                                width: 500,
                                resizable: false,
                                className: 'bxhtmled-confirm-exit-dialog'
                            };

                            var _this = this;
                            this.id = 'confirm_exit';
                            // Call parrent constructor
                            ConfirmExitDialog.superclass.constructor.apply(this, [editor, params]);

                            this.oDialog.ClearButtons();
                            this.oDialog.SetButtons([
                                new BX.CWindowButton(
                                        {
                                            title: '<?= GetMessageJS('PUBLIC_EDIT_SAVE_BUT') ?>',
                                            className: 'adm-btn-save',
                                            action: function ()
                                            {
                                                if (typeof window.BXFormSubmit == 'function')
                                                {
                                                    BXFormSubmit();
                                                }
                                                _this.oDialog.Close(true);
                                            }
                                        }),
                                new BX.CWindowButton(
                                        {
                                            title: '<?= GetMessageJS('PUBLIC_EDIT_EXIT_BUT') ?>',
                                            action: function ()
                                            {
                                                editorDialog.Close(true);
                                                _this.oDialog.Close(true);
                                            }
                                        }),
                                this.oDialog.btnCancel
                            ]);
                            this.SetContent("<?= GetMessageJS('PUBLIC_EDIT_DIALOG_EXIT_ACHTUNG') ?>");
                            this.SetTitle("<?= GetMessageJS('PUBLIC_EDIT_EDITOR') ?>");
                        }
                        BX.extend(ConfirmExitDialog, window.BXHtmlEditor.Dialog);
                        editor.RegisterDialog('ConfirmExit', ConfirmExitDialog);

                        BX.addCustomEvent(editor, 'OnIframeKeyDown', function (e, keyCode, target)
                        {
                            if (keyCode == 27 && !editor.IsExpanded() && !editor.IsPopupsOpened())
                            {
                                editorDialog.Close();
                            }
                        });

                        BX.addCustomEvent(editor, 'OnGetDefaultUploadImageName', function (nameObj)
                        {
                            nameObj.value = '<?= CUtil::JSEscape($imgName) ?>';
                        });
                    }

                    function onBeforeDialogClose()
                    {
                        if (editor.IsExpanded() || editor.IsPopupsOpened())
                        {
                            editorDialog.DenyClose();
                        } else if (editor.IsContentChanged() && !editor.IsSubmited())
                        {
                            editorDialog.DenyClose();
                            editor.GetDialog('ConfirmExit').Show();
                        }
                    }

                    function onEditorUnregister()
                    {
                        editor.Destroy();
                    }
                })();
            </script>
        <?
        /*         * ************ END |HTML EDITOR 3.0| END ************* */
    } else {
        /*         * ************ OLD HTML EDITOR ************* */
        CFileman::ShowHTMLEditControl($editor_name, $filesrc, Array(
            "site" => $site,
            "templateID" => $_REQUEST['templateID'],
            "bUseOnlyDefinedStyles" => COption::GetOptionString("fileman", "show_untitled_styles", "N") != "Y",
            "bWithoutPHP" => (!$USER->CanDoOperation('edit_php')),
            "toolbarConfig" => CFileman::GetEditorToolbarConfig($editor_name),
            "arTaskbars" => Array("BXPropertiesTaskbar", "BXSnippetsTaskbar"),
            "sBackUrl" => $back_url,
            "path" => $path,
            "limit_php_access" => $limit_php_access,
            'height' => '490',
            'width' => '100%',
            'light_mode' => true,
        ));
        ?>
            <script>
                var _bEdit = true;
                arEditorFastDialogs['asksave'] = function (pObj)
                {
                    return {
                        title: BX_MESS.EDITOR,
                        innerHTML: "<div style='margin-bottom: 20px; padding: 5px;'>" + BX_MESS.DIALOG_EXIT_ACHTUNG + "</div>",
                        width: 700,
                        height: 130,
                        OnLoad: function ()
                        {
                            window.oBXEditorDialog.SetButtons([
                                new BX.CWindowButton(
                                        {
                                            title: BX_MESS.DIALOG_SAVE_BUT,
                                            action: function ()
                                            {
                                                pObj.pMainObj.isSubmited = true;
                                                if (pObj.params.savetype == 'save')
                                                    BXFormSubmit();
                                                window.oBXEditorDialog.Close(true);
                                            },
                                            className: 'adm-btn-save'
                                        }),
                                new BX.CWindowButton(
                                        {
                                            title: BX_MESS.DIALOG_EXIT_BUT,
                                            action: function ()
                                            {
                                                pObj.pMainObj.isSubmited = true;
        <?= $obJSPopup->jsPopup ?>.CloseDialog();
                                                pObj.pMainObj.oPublicDialog.Close(true);
                                            }
                                        }),
                                window.oBXEditorDialog.btnCancel
                            ]);

                            BX.addClass(window.oBXEditorDialog.PARTS.CONTENT_DATA, "bxed-dialog");
                        }
                    };
                };

                function _BXOnBeforeCloseDialog()
                {
                    var pMainObj = GLOBAL_pMainObj['<?= CUtil::JSEscape($editor_name) ?>'];

                    // We need to ask user
                    if (pMainObj.IsChanged() && !pMainObj.isSubmited)
                    {
                        pMainObj.oPublicDialog.DenyClose();
                        pMainObj.OpenEditorDialog("asksave", false, 600, {window: window, savetype: _bEdit ? 'save' : 'saveas', popupMode: true}, true);
                    }
                }

                function CheckEditorFinish()
                {
                    var pMainObj = GLOBAL_pMainObj['<?= CUtil::JSEscape($editor_name) ?>'];
                    if (!pMainObj.bLoadFinish)
                        return setTimeout('CheckEditorFinish()', 100);

        <?= $obJSPopup->jsPopup ?>.AllowClose();

                    pMainObj.oPublicDialog = BX.WindowManager.Get();
                    BX.addClass(pMainObj.oPublicDialog.PARTS.CONTENT, "bx-editor-dialog-cont");
                    pMainObj.oPublicDialog.AllowClose();

                    // Hack for prevent editor visual bugs from reappending styles from core_window.css
                    BX.removeClass(BX.findParent(pMainObj.pWnd, {tagName: "DIV", className: "bx-core-dialog-content"}), "bx-core-dialog-content");

                    if (BX.browser.IsIE())
                    {
                        pMainObj.pWnd.firstChild.rows[0].style.height = '1px';
                        var sftbl;
                        if (sftbl = BX.findChild(pMainObj.oPublicDialog.PARTS.CONTENT, {tagName: "TABLE"}))
                        {
                            sftbl.cellSpacing = 0;
                            sftbl.cellPadding = 0;
                        }
                    }

                    var onWinResizeExt = function (Params)
                    {
                        var
                                topTlbrH = BX('filesrc_pub_toolBarSet0').offsetHeight || 51,
                                h = parseInt(Params.height) - 2,
                                w = parseInt(Params.width) - 3;

                        pMainObj.pWnd.style.height = h + "px";
                        pMainObj.pWnd.style.width = w + "px";
                        BX.findParent(pMainObj.cEditor, {tagName: "TABLE"}).style.height = (h - (topTlbrH + 35)) + "px";
                        pMainObj.arTaskbarSet[2]._SetTmpClass(true);
                        pMainObj.arTaskbarSet[2].Resize(false, false, false);
                        pMainObj.arTaskbarSet[3].Resize(false, false, false);

                        if (window._SetTmpClassInterval)
                            clearInterval(window._SetTmpClassInterval);
                        window._SetTmpClassInterval = setTimeout(function ()
                        {
                            pMainObj.arTaskbarSet[2]._SetTmpClass(false);
                            pMainObj.SetCursorFF();
                        }, 300);
                    }
                    onWinResizeExt(pMainObj.oPublicDialog.GetInnerPos());
                    BX.addCustomEvent(pMainObj.oPublicDialog, 'onWindowResizeExt', onWinResizeExt);
                    BX.addCustomEvent(pMainObj.oPublicDialog, 'onBeforeWindowClose', _BXOnBeforeCloseDialog);
                }

                CheckEditorFinish();

        <? if (COption::GetOptionString("fileman", "htmleditor_fullscreen", "N") == "Y"): ?>
                    BX.WindowManager.Get().__expand();
        <? endif; ?>
            </script>
        <?
        /*         * ************ END |OLD HTML EDITOR| END ************* */
    }
    ?>


        <?
    }
    else { //if ($bDisableEditor)
        ?>
        <textarea name="<?= htmlspecialcharsbx($editor_name) ?>" id="<?= htmlspecialcharsbx($editor_name) ?>" style="height: 99%; width: 100%;"><?= htmlspecialcharsex($filesrc) ?></textarea>
        <script type="text/javascript">
            var
                    border,
                    wnd = BX.WindowManager.Get();

            function TAResize(data)
            {
                var ta = BX('<?= CUtil::JSEscape($editor_name) ?>');
                if (null == border)
                    border = parseInt(BX.style(ta, 'border-left-width')) + parseInt(BX.style(ta, 'border-right-width'));

                if (isNaN(border))
                    border = 0;

                if (data.height)
                    ta.style.height = (data.height - border - 10) + 'px';
                if (data.width)
                    ta.style.width = (data.width - border - 10) + 'px';
            }

            BX.addCustomEvent(wnd, 'onWindowResizeExt', TAResize);
            TAResize(wnd.GetInnerPos());
        </script>
    <?
} //if (!$bDisableEditor)
$obJSPopup->StartButtons();
?>
    <input type="button" class="adm-btn-save" id="btn_popup_save" name="btn_popup_save" value="<?= GetMessage("JSPOPUP_SAVE_CAPTION") ?>" onclick="BXFormSubmit();" title="<?= GetMessage("JSPOPUP_SAVE_CAPTION") ?>" />
    <?
    $obJSPopup->ShowStandardButtons(array('cancel'));
    $obJSPopup->EndButtons();

    if (CAutoSave::Allowed()) {
        $AUTOSAVE->checkRestore();
    }

    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_js.php");
    ?>