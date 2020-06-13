<?
/** @global CMain $APPLICATION */
/** @global CDatabase $DB */

/** @global CUser $USER */
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Entity;
use Realweb\RealwebMainIncludeTable;

// admin initialization
define("ADMIN_MODULE_NAME", "realweb.main.include");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$pathProlog = realpath(__DIR__ . "/../");
require_once($pathProlog . "/prolog.php");

if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

if (!CModule::IncludeModule(ADMIN_MODULE_NAME)) {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

Loc::loadMessages(__FILE__);

Loader::includeModule("realweb.main.include");

$is_create_form = true;
$is_update_form = false;

$isEditMode = true;

$fields = RealwebMainIncludeTable::getMap();

// get row
$row = null;

if (isset($_REQUEST['ID']) && $_REQUEST['ID'] > 0) {
    $row = RealwebMainIncludeTable::getById($_REQUEST['ID'])->fetch();

    if (!empty($row)) {
        $is_update_form = true;
        $is_create_form = false;
    } else {
        $row = null;
    }
}

if ($is_create_form) {
    $APPLICATION->SetTitle(Loc::getMessage('REALWEB.MAIN.INCLUDE.TITLE_PAGE_NEW'));
} else {
    $APPLICATION->SetTitle(Loc::getMessage("REALWEB.MAIN.INCLUDE.TITLE_PAGE_EDIT", array('#CODE#' => $row['CODE'])));
}
$tabNameCode = ($is_update_form ? "EDIT" : "NEW");
// form
$aTabs = array(
    array("DIV" => "edit1", "TAB" => Loc::getMessage('REALWEB.MAIN.INCLUDE.TAB_MAIN_' . $tabNameCode),
        "ICON" => "ad_contract_edit", "TITLE" => Loc::getMessage('REALWEB.MAIN.INCLUDE.TAB_MAIN_' . $tabNameCode))
);

$tabControl = new CAdminForm("realweb_main_include_edit", $aTabs);

// delete action
if ($is_update_form && isset($_REQUEST['action']) && $_REQUEST['action'] === 'delete' && check_bitrix_sessid()) {
    RealwebMainIncludeTable::delete($row['ID']);
    LocalRedirect("main_include_list.php?lang=" . LANGUAGE_ID);
}

// save action
if ((strlen($save) > 0 || strlen($apply) > 0) && $REQUEST_METHOD == "POST" && check_bitrix_sessid()) {
    $data = array();

    /**
     * @var Entity\ScalarField|Entity\DatetimeField|Entity\EnumField|Entity\BooleanField|Entity\FloatField|Entity\TextField $field
     */
    foreach ($fields as $codeField => $field) {
        if($codeField == "CATEGORY_ENTITY"){
            continue;
        }
        if (!empty($_FILES[$codeField])) {
            $data[$codeField] = $_FILES[$codeField];
        } else {
            $data[$codeField] = $_REQUEST[$codeField];
        }
    }

    /** @param Bitrix\Main\Entity\AddResult $result */
    if ($is_update_form) {
        $ID = intval($_REQUEST['ID']);
        $result = RealwebMainIncludeTable::update($ID, $data);
    } else {
        $result = RealwebMainIncludeTable::add($data);
        $ID = $result->getId();
    }

    if ($result->isSuccess()) {
        if (strlen($save) > 0) {
            LocalRedirect("main_include_list.php?lang=" . LANGUAGE_ID);
        } else {
            LocalRedirect("main_include_edit.php?ID=" . intval($ID) . "&lang=" . LANGUAGE_ID . "&" . $tabControl->ActiveTabParam());
        }
    } else {
        $errors = $result->getErrorMessages();
    }
}

// menu
$aMenu = array(
    array(
        "TEXT" => Loc::getMessage('REALWEB.MAIN.INCLUDE.RETURN_TO_LIST_BUTTON'),
        "TITLE" => Loc::getMessage('REALWEB.MAIN.INCLUDE.RETURN_TO_LIST_BUTTON'),
        "LINK" => "main_include_list.php?lang=" . LANGUAGE_ID,
        "ICON" => "btn_list",
    )
);

$context = new CAdminContextMenu($aMenu);

//view

if ($_REQUEST["mode"] == "list") {
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_js.php");
} else {
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
}

$context->Show();


if (!empty($errors)) {
    CAdminMessage::ShowMessage(join("\n", $errors));
}

$tabControl->BeginPrologContent();

echo CAdminCalendar::ShowScript();

$tabControl->EndPrologContent();
$tabControl->BeginEpilogContent();
unset($fields["ID"]);
reset($fields);
?>
<?= bitrix_sessid_post() ?>
<input type="hidden" name="ID" value="<?= htmlspecialcharsbx(!empty($row) ? $row['ID'] : '') ?>">
<input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>">

<? $tabControl->EndEpilogContent(); ?>

<? $tabControl->Begin(array("FORM_ACTION" => $APPLICATION->GetCurPage())); ?>

<? $tabControl->BeginNextFormTab(); ?>
<? $tabControl->AddViewField("ID", "ID", !empty($row) ? $row['ID'] : ''); ?>
<?
/**
 * @var Entity\ScalarField|Entity\DatetimeField|Entity\EnumField|Entity\BooleanField|Entity\FloatField|Entity\TextField $field
 */
?>
<?php foreach ($fields as $codeField => $field): ?>
    <?php
    if (strpos($codeField, "TEXT_TYPE") !== false) {
        continue;
    }
    ?>
    <?php if ($field instanceof \Bitrix\Main\Entity\TextField !== false): ?>
        <?php
        $tabControl->BeginCustomField($codeField, $field->getTitle(), $field->isRequired() === true);
        if (isset($fields[$codeField . "_TYPE"])) {
            $str_FIELD_TYPE = $fields[$codeField . "_TYPE"]->getDefaultValue() !== "html" ? "text" : "html";
        } else {
            $str_FIELD_TYPE = "text";
        }

        $str_FIELD = htmlspecialcharsbx($row[$codeField]);
        ?>
        <tr class="heading">
            <td colspan="2"><? echo $tabControl->GetCustomLabelHTML() ?></td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <?php
                CFileMan::AddHTMLEditorFrame(
                        $codeField, $str_FIELD, $codeField . "_TYPE", $str_FIELD_TYPE, array(
                    'height' => 450,
                    'width' => '100%'
                        ), "N", 0, "", "", SITE_ID, true, false, array()
                );
                ?>
            </td>
        </tr>
        <?php
        $tabControl->EndCustomField($codeField, '<input type="hidden" name="' . $codeField . '" value="' . $str_FIELD . '">' .
                '<input type="hidden" name="' . $codeField . '_TYPE" value="' . $str_FIELD_TYPE . '">'
        );
        ?>
    <?php elseif ($codeField == "CATEGORY"): ?> 
        <?php
        ?>
        <?php $tabControl->AddDropDownField($codeField, $field->getTitle(), $field->isRequired() === true, array_combine( \Realweb\RealwebMainIncludeCategoryTable::getValues(), \Realweb\RealwebMainIncludeCategoryTable::getNames()), !empty($row[$codeField]) ? $row[$codeField] : ''); ?>
    <?php elseif ($field instanceof \Bitrix\Main\Entity\EnumField !== false): ?> 
        <?php
        ?>
        <?php $tabControl->AddDropDownField($codeField, $field->getTitle(), $field->isRequired() === true, array_combine($field->getValues(), $field->getValues()), !empty($row[$codeField]) ? $row[$codeField] : ''); ?>
    <?php elseif ($codeField == "CATEGORY_ENTITY"): ?> 


    <?php else: ?>
        <?php $tabControl->AddEditField($codeField, $field->getTitle(), $field->isRequired() === true, array(), !empty($row[$codeField]) ? $row[$codeField] : ''); ?>
    <?php endif; ?>
<?php endforeach; ?>

<?php
$disable = true;
if ($isEditMode)
    $disable = false;

$tabControl->Buttons(array(
    "disabled" => $disable,
    "back_url" => "main_include_list.php?lang=" . LANGUAGE_ID
));


$tabControl->Show();
?>
</form>


<?
if ($_REQUEST["mode"] == "list")
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_js.php");
else
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");