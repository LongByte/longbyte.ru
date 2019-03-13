<?
/**
 * @global CMain $APPLICATION
 * @global CUser $USER
 */
if (!array_key_exists("component_name", $_GET)) {
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/public/component_props.php");
    die();
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_js.php");

$FN = preg_replace("/[^a-z0-9_\\[\\]:]/i", "", $_REQUEST["FORM_NAME"]);
$FC = preg_replace("/[^a-z0-9_\\[\\]:]/i", "", $_REQUEST["PROPERTY_INPUT"]);
$JSON_INPUT = preg_replace("/[^a-z0-9_\\[\\]:]/i", "", $_REQUEST["JSON_INPUT"]);


$APPLICATION->ShowAjaxHead();

function PageParams($bUrlEncode = true) {
    $amp = $bUrlEncode ? '&amp;' : '&';
    return
            'component_name=' . urlencode(CUtil::addslashes($_GET["component_name"])) .
            $amp . 'component_template=' . urlencode(CUtil::addslashes($_GET["component_template"]));
}

$io = CBXVirtualIo::GetInstance();

$src_path = $io->CombinePath("/", $_GET["src_path"]);
$src_line = intval($_GET["src_line"]);

if (!$USER->CanDoOperation('edit_php') && !$USER->CanDoFileOperation('fm_lpa', array($_GET["src_site"], $src_path))) {
    die(GetMessage("ACCESS_DENIED"));
}

$bLimitPhpAccess = !$USER->CanDoOperation('edit_php');

CModule::IncludeModule("fileman");

$componentName = $_GET["component_name"];
$componentTemplate = $_GET["component_template"];
$templateId = $_GET["template_id"];
//$relPath = $io->ExtractPathFromPath($src_path);
CComponentParamsManager::Init(array(
    'requestUrl' => '/bitrix/admin/fileman_component_params.php',
));

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/public/component_props2.php');

CUtil::JSPostUnescape();

$obJSPopup = new CJSPopup('', array(
    'TITLE' => GetMessage("comp_prop_title")
        )
);

$obJSPopup->ShowTitlebar();

$strWarning = "";
$arValues = array();
$arTemplate = false;
$arComponent = false;
$arComponentDescription = false;
$arParameterGroups = array();
$filesrc = "";
$abs_path = "";
$curTemplate = "";

if (!CComponentEngine::CheckComponentName($componentName))
    $strWarning .= GetMessage("comp_prop_error_name") . "<br>";

if ($strWarning == "") {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && $_GET["action"] == "refresh") {
        // parameters were changed by "ok" button
        // we need to refresh the component description with new values
        $arValues = array_merge($arValues, $_POST);
    }

    $curTemplate = (isset($_POST["COMPONENT_TEMPLATE"])) ? $_POST["COMPONENT_TEMPLATE"] : $componentTemplate;

    $data = CComponentParamsManager::GetComponentProperties(
                    $componentName, $curTemplate, $templateId, $arValues
    );
    $data['description'] = CComponentUtil::GetComponentDescr($componentName);
    foreach ($data['templates'] as $templates_key => $_template) {
        if (strlen($_template['TITLE']) > 0) {
            $_template['DISPLAY_NAME'] = $_template['NAME'] . ' (' . $_template['TITLE'] . ')';
            $data['templates'][$templates_key] = $_template;
        }
    }
    /* save parameters to file */
    if ($_SERVER["REQUEST_METHOD"] == "POST" && $_GET["action"] == "save") {
        if (!check_bitrix_sessid()) {
            $strWarning .= GetMessage("comp_prop_err_save") . "<br>";
        } else {
            $aPostValues = array_merge($arValues, $_POST);
            unset($aPostValues["sessid"]);
            unset($aPostValues["bxpiheight"]);
            unset($aPostValues["bxpiwidth"]);

            CComponentUtil::PrepareVariables($aPostValues);
            foreach ($aPostValues as $name => $value) {
                if (is_array($value) && count($value) == 1 && isset($value[0]) && $value[0] == "") {
                    $aPostValues[$name] = array();
                } elseif (substr($value, 0, 2) == '={' && substr($value, -1) == '}') {
                    $value = substr($value, 2);
                    $value = substr($value, 0, -1);
                    $aPostValues[$name] = $value;
                }
            }

            //check template name
            $sTemplateName = "";
            $arComponentTemplates = CComponentUtil::GetTemplatesList($componentName, $templateId);
            foreach ($arComponentTemplates as $templ) {
                if ($templ["NAME"] == $_POST["COMPONENT_TEMPLATE"]) {
                    $sTemplateName = $templ["NAME"];
                    break;
                }
            }

            foreach ($aPostValues as &$aPostValue) {
                if (is_array($aPostValue)) {
                    foreach ($aPostValue as &$aPostValueValue) {
                        $aPostValueValue = str_replace("'", '"', $aPostValueValue);
                    }
                } elseif (is_string($aPostValue)) {
                    $aPostValue = str_replace("'", '"', $aPostValue);
                }
            }

            $DATA = array(
                'TYPE' => 'COMPONENT',
                'COMPONENT_NAME' => $componentName,
                'TEMPLATE' => $sTemplateName,
                'arParams' => $aPostValues,
            );
            $SER_DATA = serialize($DATA);
            $SER_DATA = str_replace('"', '\"', $SER_DATA);
            $SER_DATA = str_replace(PHP_EOL, '', $SER_DATA);

            $JSON_DATA = json_encode($DATA['arParams']);
            $JSON_DATA = str_replace('"', '\"', $JSON_DATA);
            if (isset($_POST['COMPONENT_PARAMS_CALLBACK'])) {
                if(is_callable($_POST['COMPONENT_PARAMS_CALLBACK'])){
                    call_user_func_array($_POST['COMPONENT_PARAMS_CALLBACK'], array($DATA));
                }
            }
            ?>
            <script>
                top.<? echo $FN; ?>["<? echo $FC; ?>"].value = '<?php echo $SER_DATA; ?>';
                top.<? echo $FN; ?>["<? echo $JSON_INPUT; ?>"].value = '<?php echo $JSON_DATA; ?>';
            </script>
            <?php
            $obJSPopup->Close(false);
        }
    }
}

if ($strWarning !== "") {
    $obJSPopup->ShowValidationError($strWarning);
    ?>
    <script>
        (function ()
        {
            if (BX && BX.WindowManager)
            {
                var oPopup = BX.WindowManager.Get();
                if (oPopup && oPopup.PARTS && oPopup.PARTS.CONTENT_DATA)
                {
                    oPopup.PARTS.CONTENT_DATA.style.display = 'none';
                }
            }
        })();
    </script>
    <?
}

$obJSPopup->StartContent();
?>

<? if ($strWarning === ""): ?>
    <script>
        (function ()
        {
            function CompDialogManager(params)
            {
                this.Init(params);
            }

            CompDialogManager.prototype =
                    {
                        Init: function (params)
                        {
                            this.pDiv = BX('bx-comp-params-wrap');
                            var oPopup = BX.WindowManager.Get();
                            oPopup.PARTS.CONTENT_DATA.className = 'bxcompprop-adm-dialog-content';

                            BX.addClass(oPopup.PARTS.CONTENT, 'bxcompprop-adm-dialog');

                            BX.addCustomEvent(oPopup, 'onWindowResize', function ()
                            {
                                BX.onCustomEvent(oBXComponentParamsManager, 'OnComponentParamsResize', [
                                    parseInt(oPopup.PARTS.CONTENT_DATA.style.width),
                                    parseInt(oPopup.PARTS.CONTENT_DATA.style.height)
                                ]);
                            });
                            params.currentValues = JSON.parse(top.<? echo $FN; ?>["<? echo $JSON_INPUT; ?>"].value);
                            oBXComponentParamsManager.params = {
                                name: params.name,
                                parent: params.parent,
                                template: params.template,
                                exParams: params.exParams,
                                currentValues: params.currentValues || {},
                                container: this.pDiv,
                                siteTemplate: params.siteTemplate
                            };

                            BX.addCustomEvent(oBXComponentParamsManager, 'onComponentParamsBuilt', function ()
                            {
                                BX.onCustomEvent(oBXComponentParamsManager, 'OnComponentParamsResize', [
                                    parseInt(oPopup.PARTS.CONTENT_DATA.style.width),
                                    parseInt(oPopup.PARTS.CONTENT_DATA.style.height)
                                ]);
                            });

                            oBXComponentParamsManager.BuildComponentParams(params.data, oBXComponentParamsManager.params);
                        }
                    };

            window.publicComponentDialogManager = new CompDialogManager(<?=
    CUtil::PhpToJSObject(array(
        'name' => $componentName,
        'template' => $curTemplate,
        'siteTemplate' => $templateId,
        'currentValues' => $arValues,
        'data' => $data
    ))
    ?>);
        })();
    </script>
    <div id="bx-comp-params-wrap" class="bxcompprop-wrap-public"></div>
    <? CComponentParamsManager::DisplayFileDialogsScripts(); ?>
<? endif; /* ($strWarning === "") */ ?>
<? $obJSPopup->StartButtons(); ?>
<input type="button" value="<?= GetMessage("comp_prop_save") ?>" onclick="<?= $obJSPopup->jsPopup ?>.PostParameters('<?= PageParams() . '&amp;action=save&amp;FORM_NAME=' . $FN . '&amp;JSON_INPUT=' . $JSON_INPUT . '&amp;PROPERTY_INPUT=' . $FC ?>');" title="<?= GetMessage("comp_prop_save_title") ?>" name="save" class="adm-btn-save" />
<input type="button" value="<?= GetMessage("comp_prop_cancel") ?>" onclick="<?= $obJSPopup->jsPopup ?>.CloseDialog()" title="<?= GetMessage("comp_prop_cancel_title") ?>" />
<? $obJSPopup->EndButtons(); ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_js.php"); ?>