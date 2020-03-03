<?
/** @global CMain $APPLICATION */
/** @global CDatabase $DB */

/** @global CUser $USER */
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Realweb\RealwebMainIncludeTable;

$pathProlog = realpath(__DIR__ . '/../');

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once($pathProlog . '/prolog.php');

$obConnection = Bitrix\Main\Application::getConnection();

if (!$USER->CanDoOperation('edit_other_settings') && !$USER->CanDoOperation('view_other_settings'))
    $APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));

$obRequest = \Bitrix\Main\Context::getCurrent()->getRequest();

Loader::includeModule('realweb.main.include');
$isAdmin = $USER->CanDoOperation('edit_other_settings');
Loc::loadMessages(__FILE__);

$sTableID = 'tbl_realweb_main_include';

$aTabs = array(
    array('DIV' => 'edit1', 'TAB' => Loc::getMessage('MAIN_PARAM'), 'TITLE' => Loc::getMessage('MAIN_PARAM_TITLE')),
);
$tabControl = new CAdminTabControl('tabControl', $aTabs);
$strTitle = Loc::getMessage('REALWEB.MAIN.INCLUDE.TITLE_PAGE');
$APPLICATION->SetTitle($strTitle);
$oSort = new CAdminSorting($sTableID, 'timestamp_x', 'desc');
$arOrder = (strtoupper($by) === 'ID' ? array($by => $order) : array($by => $order, 'ID' => 'ASC'));
$lAdmin = new CAdminList($sTableID, $oSort);
$arFilterFields = array(
    'find',
    'find_id',
    'find_code',
    'find_category',
);
$lAdmin->InitFilter($arFilterFields);

$arFilter = array(
    'ID' => ($find != '' && $find_type == 'id' ? $find : $find_id),
    'CODE' => ($find != '' && $find_type == 'code' ? $find : $find_code),
    'CATEGORY' => ($find != '' && $find_type == 'category' ? $find : $find_category),
);

if ($lAdmin->EditAction()) {
    foreach ($FIELDS as $ID => $arFields) {
        if (!$lAdmin->IsUpdated($ID))
            continue;

        // сохраним изменения каждого элемента
        $obConnection->startTransaction();
        $arData = RealwebMainIncludeTable::getById(IntVal($ID))->fetch();
        if ($arData) {
            foreach ($arFields as $key => $value)
                $arData[$key] = $value;
            if (!RealwebMainIncludeTable::update($ID, $arData)) {
                $lAdmin->AddGroupError(Loc::getMessage('REALWEB.MAIN.INCLUDE.ERROR_SAVE') . ' ' . $cData->LAST_ERROR, $ID);
                $obConnection->rollbackTransaction();
            }
        } else {
            $lAdmin->AddGroupError(Loc::getMessage('REALWEB.MAIN.INCLUDE.ERROR_NOT_FOUND'), $ID);
            $obConnection->rollbackTransaction();
        }
        $obConnection->commitTransaction();
    }
}

// обработка одиночных и групповых действий
if ($arID = $lAdmin->GroupAction()) {
    // если выбрано "Для всех элементов"
    if ($obRequest->get('action_target') == 'selected') {
        $rsData = RealwebMainIncludeTable::getList(array(
                'filter' => $arFilter,
        ));
        while ($arRes = $rsData->fetch())
            $arID[] = $arRes['ID'];
    }

    // пройдем по списку элементов
    foreach ($arID as $ID) {
        if (strlen($ID) <= 0)
            continue;
        $ID = IntVal($ID);

        // для каждого элемента совершим требуемое действие
        if ($obRequest->get('action_button') == 'delete') {
            $obConnection->startTransaction();
            if (!RealwebMainIncludeTable::delete($ID)) {
                $DB->Rollback();
                $lAdmin->AddGroupError(Loc::getMessage('REALWEB.MAIN.INCLUDE.ERROR_DELETE'), $ID);
            }
            $obConnection->commitTransaction();
        }
    }
}

$arHeader = array(
    array(
        'id' => 'ID',
        'content' => 'ID',
        'title' => Loc::getMessage('REALWEB.MAIN.INCLUDE.ID'),
        'default' => false,
    ),
    array(
        'id' => 'CODE',
        'content' => 'Код',
        'title' => Loc::getMessage('REALWEB.MAIN.INCLUDE.CODE'),
        'default' => false,
    ),
    array(
        'id' => 'CATEGORY',
        'content' => 'Категория',
        'title' => Loc::getMessage('REALWEB.MAIN.INCLUDE.CATEGORY'),
        'default' => false,
    ),
    array(
        'id' => 'TEXT',
        'content' => 'Текст',
        'title' => Loc::getMessage('REALWEB.MAIN.INCLUDE.TEXT'),
        'default' => false,
    ),
    array(
        'id' => 'DESCRIPTION',
        'content' => Loc::getMessage('REALWEB.MAIN.INCLUDE.DESCRIPTION'),
        'title' => Loc::getMessage('REALWEB.MAIN.INCLUDE.DESCRIPTION'),
        'default' => false,
    )
);

$lAdmin->AddHeaders($arHeader);
$lAdmin->AddVisibleHeaderColumn('ID');
$lAdmin->AddVisibleHeaderColumn('CODE');
$lAdmin->AddVisibleHeaderColumn('CATEGORY');
$lAdmin->AddVisibleHeaderColumn('DESCRIPTION');

if (strlen($arFilter['ID']) <= 0) {
    unset($arFilter['ID']);
}
if (strlen($arFilter['CODE']) <= 0) {
    unset($arFilter['CODE']);
}
if (strlen($arFilter['CATEGORY']) <= 0) {
    unset($arFilter['CATEGORY']);
} elseif ($arFilter['CATEGORY'] == '0') {
    $arFilter['CATEGORY'] = '';
}
$rsData = RealwebMainIncludeTable::getList(array(
        'filter' => $arFilter
    ));

$rsData = new CAdminResult($rsData, $sTableID);

$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(Loc::getMessage('REALWEB.MAIN.INCLUDE.nav')));
while ($arRes = $rsData->NavNext(true, 'f_')) {
    $row = & $lAdmin->AddRow($f_ID, $arRes);
    $row->AddViewField("ID", '<a href="main_include_edit.php?ID=' . $f_ID . '&amp;lang=' . LANG . '" title="' . Loc::getMessage("area_act_edit") . '">' . $f_ID . '</a>');
    $row->AddInputField('CODE', array('size' => 20));

    $arActions = Array(
        array(
            'ICON' => 'edit',
            'DEFAULT' => true,
            'TEXT' => Loc::getMessage('REALWEB.MAIN.INCLUDE.CONTEXT_EDIT'),
            'ACTION' => $lAdmin->ActionRedirect('main_include_edit.php?ID=' . $f_ID)
        ),
        array(
            'ICON' => 'delete',
            'TEXT' => GetMessage('REALWEB.MAIN.INCLUDE.CONTEXT_DELETE'),
            'ACTION' => "if(confirm('" . Loc::getMessage('REALWEB.MAIN.INCLUDE.CONTEXT_DELETE') . "?')) " . $lAdmin->ActionDoGroup($f_ID, "delete")
        )
    );

    $row->AddActions($arActions);
}

$lAdmin->AddFooter(
    array(
        array('title' => GetMessage('MAIN_ADMIN_LIST_SELECTED'), 'value' => $rsData->SelectedRowsCount()),
        array('counter' => true, 'title' => GetMessage('MAIN_ADMIN_LIST_CHECKED'), 'value' => '0'),
    )
);
$lAdmin->AddGroupActionTable(Array(
    'delete' => GetMessage('MAIN_ADMIN_LIST_DELETE'),
));

$aContext = array(
    array(
        'TEXT' => GetMessage('MAIN_ADD'),
        'LINK' => 'main_include_edit.php?lang=' . LANG,
        'TITLE' => GetMessage('REALWEB.MAIN.INCLUDE.BTN_NEW'),
        'ICON' => 'btn_new',
    ),
);
$lAdmin->AddAdminContextMenu($aContext);
$lAdmin->CheckListMode();
require_once ($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
$oFilter = new CAdminFilter(
    $sTableID . '_filter', array(
    'find' => Loc::getMessage('REALWEB.MAIN.INCLUDE.FIND'),
    'id' => Loc::getMessage('REALWEB.MAIN.INCLUDE.ID'),
    'code' => Loc::getMessage('REALWEB.MAIN.INCLUDE.CODE'),
    'category' => Loc::getMessage('REALWEB.MAIN.INCLUDE.CATEGORY'),
    )
);
?>

<form name='find_form' id='find_form'  method='get' action='<? echo $APPLICATION->GetCurPage(); ?>'>
    <?
    $oFilter->Begin();
    ?>
    <tr>
        <td><b><?= Loc::getMessage('REALWEB.MAIN.INCLUDE.FIND') ?>:</b></td>
        <td>
            <input type='text' size='25' name='find' value='<? echo htmlspecialcharsbx($find) ?>' title='<?= Loc::getMessage('REALWEB.MAIN.INCLUDE.FIND_TITLE') ?>'>
            <?
            $arr = array(
                'reference' => array(
                    Loc::getMessage('REALWEB.MAIN.INCLUDE.ID'),
                    Loc::getMessage('REALWEB.MAIN.INCLUDE.CODE'),
                    Loc::getMessage('REALWEB.MAIN.INCLUDE.CATEGORY'),
                ),
                'reference_id' => array(
                    'id',
                    'code',
                    'category',
                )
            );
            echo SelectBoxFromArray('find_type', $arr, $find_type, '', '');
            ?>
        </td>
    </tr>
    <tr>
        <td><?= Loc::getMessage('REALWEB.MAIN.INCLUDE.ID') ?>:</td>
        <td>
            <input type='text' name='find_id' size='47' value='<? echo htmlspecialcharsbx($find_id) ?>'>
            &nbsp;<?= ShowFilterLogicHelp() ?>
        </td>
    </tr>
    <tr>
        <td><?= Loc::getMessage('REALWEB.MAIN.INCLUDE.CODE') ?>:</td>
        <td>
            <input type='text' name='find_code' size='47' value='<? echo htmlspecialcharsbx($find_code) ?>'>
            &nbsp;<?= ShowFilterLogicHelp() ?>
        </td>
    </tr>
    <tr>
        <td><?= Loc::getMessage('REALWEB.MAIN.INCLUDE.CATEGORY') ?>:</td>
        <td>
            <?php
            $arFields = RealwebMainIncludeTable::getMap();
            $obCategory = $arFields['CATEGORY'];
            $arCategoryValues = array(
                'reference' => array(),
                'reference_id' => array(),
            );
            foreach (\Realweb\RealwebMainIncludeCategoryTable::getAll() as $strValue) {
                $arCategoryValues['reference'][] = $strValue;
                $arCategoryValues['reference_id'][] = $strValue;
                if (strlen($strValue) == 0) {
                    $arCategoryValues['reference'][] = 'Без категории';
                    $arCategoryValues['reference_id'][] = '0';
                }
            }
            ?>
            <?= SelectBoxFromArray('find_category', $arCategoryValues, $find_category, '', ''); ?>
            &nbsp;<?= ShowFilterLogicHelp() ?>
        </td>
    </tr>
    <?
    $oFilter->Buttons(array('table_id' => $sTableID, 'url' => $APPLICATION->GetCurPage(), 'form' => 'find_form'));
    $oFilter->End();
    ?>
</form>

<? $lAdmin->DisplayList(); ?>

<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
