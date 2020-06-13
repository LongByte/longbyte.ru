<?

use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Internal\EventTable;
use Bitrix\Main\Mail\Internal\EventTypeTable;
use Bitrix\Main\Mail\Internal\EventMessageTable;

$pathProlog = realpath(__DIR__ . '/../');

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once($pathProlog . '/prolog.php');

if (!$USER->IsAdmin())
    $APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));

Loader::includeModule('realweb.bevent');
Loc::loadMessages(__FILE__);

$arRequest = Context::getCurrent()->getRequest()->toArray();

//связаные данные
$arEventTypes = array();
$rsEventTypes = EventTypeTable::getList();
while ($arEventType = $rsEventTypes->fetch()) {
    $arEventTypes[$arEventType['EVENT_NAME']][$arEventType['LID']] = $arEventType;
}

$arEventMessageToType = array();
$arEventMessages = array();
$rsEventMessages = EventMessageTable::getList();
while ($arEventMessage = $rsEventMessages->fetch()) {
    $arEventMessages[$arEventMessage['ID']] = $arEventMessage;
    $arEventMessageToType[$arEventMessage['EVENT_NAME']][] = $arEventMessage['ID'];
}

$arStatusExec = array(
    'N',
    'Y',
    'F',
    '0',
    'P',
    'D',
);

//админская таблица
$sTableID = 'tbl_realweb_bevent';

$aTabs = array(
    array('DIV' => 'edit1', 'TAB' => Loc::getMessage('MAIN_PARAM'), 'TITLE' => Loc::getMessage('MAIN_PARAM_TITLE')),
);
$tabControl = new CAdminTabControl('tabControl', $aTabs);
$strTitle = Loc::getMessage('REALWEB.BEVENT.TITLE_PAGE');
$APPLICATION->SetTitle($strTitle);

$arFilter = array();
$arHeader = array();
$arAdminFilter = array();
$arTableMap = EventTable::getMap();

$oSort = new CAdminSorting($sTableID, 'ID', 'desc');
$arOrder = (strtoupper($by) === 'ID' ? array($by => $order) : array($by => $order, 'ID' => 'ASC'));
$lAdmin = new CAdminList($sTableID, $oSort);
$arFilterFields = array();
foreach ($arTableMap as $fieldCode => $arField) {
    if ($arField instanceof Bitrix\Main\ORM\Fields\ScalarField) {
        $fieldCode = $arField->getColumnName();
    }
    $arFilterFields[] = 'find_' . $fieldCode;
}
$lAdmin->InitFilter($arFilterFields);

foreach ($arTableMap as $fieldCode => $arField) {
    if ($arField instanceof Bitrix\Main\ORM\Fields\ScalarField) {
        $fieldCode = $arField->getColumnName();
    }
    if (strlen($arRequest['find_' . $fieldCode]) > 0)
        $arFilter[$fieldCode] = $arRequest['find_' . $fieldCode];
}

foreach ($arTableMap as $fieldCode => $arField) {
    $isPrimary = false;
    if ($arField instanceof Bitrix\Main\ORM\Fields\ScalarField) {
        $isPrimary = $arField->isPrimary();
        $fieldCode = $arField->getColumnName();
    } else {
        $isPrimary = $arField['primary'];
    }

    $arHeader[] = array(
        'id' => $fieldCode,
        'content' => Loc::getMessage('REALWEB.BEVENT.DATA_' . $fieldCode),
        'sort' => $fieldCode,
        'default' => $isPrimary,
    );
}
$lAdmin->AddHeaders($arHeader);
foreach ($arTableMap as $fieldCode => $arField) {
    if ($arField instanceof Bitrix\Main\ORM\Fields\ScalarField) {
        $fieldCode = $arField->getColumnName();
    }
    $lAdmin->AddVisibleHeaderColumn($fieldCode);
}

//получение данных
$arEventRequest = array();
if (count($arFilter) > 0) {
    $arEventRequest['filter'] = $arFilter;
}
if ($arRequest['by'] && $arRequest['order']) {
    $arEventRequest['order'] = array($arRequest['by'] => $arRequest['order']);
} else {
    $arEventRequest['order'] = array('ID' => 'desc');
}

$rsData = EventTable::getList($arEventRequest);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(''));
while ($arRes = $rsData->NavNext(true, 'f_')) {
    $row = & $lAdmin->AddRow($f_ID, $arRes);

    $strLanguageId = $arRes['LANGUAGE_ID'] ?: 'ru';

    $arCFields = array();
    foreach ($arRes['C_FIELDS'] as $key => $value) {
        $arCFields[] = $key . ': ' . strip_tags($value);
    }
    $row->AddViewField('EVENT_NAME', $arEventTypes[$arRes['EVENT_NAME']][$strLanguageId]['NAME'] . ' [<a target="_blank" href="/bitrix/admin/type_edit.php?lang=' . LANG . '&EVENT_NAME=' . $arRes['EVENT_NAME'] . '">' . $arRes['EVENT_NAME'] . '</a>]');
    $arMessageIds = array();
    if ($arRes['MESSAGE_ID']) {
        $arMessageIds[] = $arRes['MESSAGE_ID'];
    } else {
        $arMessageIds = $arEventMessageToType[$arRes['EVENT_NAME']];
    }
    $arMessageIdLink = array();
    foreach ($arMessageIds as $iMessageId) {
        $arMessageIdLink[] = '<a target="_blank" href="/bitrix/admin/message_edit.php?lang=' . LANG . '&ID=' . $iMessageId . '">' . $iMessageId . '</a>';
    }
    $row->AddViewField('MESSAGE_ID', implode('<br>', $arMessageIdLink));
    $row->AddViewField('C_FIELDS', implode('<br>', $arCFields));

    $row->AddViewField('SUCCESS_EXEC', Loc::getMessage('REALWEB.BEVENT.DATA_SUCCESS_EXEC.' . $arRes['SUCCESS_EXEC']) . ' [' . $arRes['SUCCESS_EXEC'] . ']');
    $row->AddViewField('DUPLICATE', ($arRes['DUPLICATE'] == 'Y' ? Loc::getMessage('REALWEB.BEVENT.YES') : Loc::getMessage('REALWEB.BEVENT.NO')) . ' [' . $arRes['DUPLICATE'] . ']');
}

//подвал
$lAdmin->AddFooter(
    array(
        array('title' => GetMessage('MAIN_ADMIN_LIST_SELECTED'), 'value' => $rsData->SelectedRowsCount()),
        array('counter' => true, 'title' => GetMessage('MAIN_ADMIN_LIST_CHECKED'), 'value' => '0'),
    )
);

$lAdmin->CheckListMode();
require_once ($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
//фильтр
foreach ($arTableMap as $fieldCode => $arField) {
    if ($arField instanceof Bitrix\Main\ORM\Fields\ScalarField) {
        $fieldCode = $arField->getColumnName();
    }
    $arAdminFilter[] = Loc::getMessage('REALWEB.BEVENT.DATA_' . $fieldCode);
}
$oFilter = new CAdminFilter($sTableID . '_filter', $arAdminFilter);
?>

<form name="find_form" id="find_form"  method="get" action="<?= $APPLICATION->GetCurPage(); ?>">
    <?
    $oFilter->Begin();
    foreach ($arTableMap as $fieldCode => $arField) {
        if ($arField instanceof Bitrix\Main\ORM\Fields\ScalarField) {
            $fieldCode = $arField->getColumnName();
        }
        ?>
        <tr>
            <td><?= Loc::getMessage('REALWEB.BEVENT.DATA_' . $fieldCode) . ":" ?></td>
            <td>

                <?
                switch ($fieldCode) {
                    case 'EVENT_NAME':
                        ?>
                        <select name="<?= 'find_' . $fieldCode ?>" >
                            <option value="">-</option>
                            <? foreach ($arEventTypes as $strEventName => $arLanguages): ?>
                                <? $strEventNameLang = array_key_exists('ru', $arLanguages) ? 'ru' : reset(array_keys($arLanguages)); ?>
                                <option <? if ($strEventName == $arRequest['find_' . $fieldCode]): ?>selected<? endif; ?> value="<?= $strEventName ?>"><?= $arLanguages[$strEventNameLang]['NAME'] ?> [<?= $strEventName ?>]</option>
                            <? endforeach; ?>
                        </select>
                        <?
                        break;
                    case 'SUCCESS_EXEC':
                        ?>
                        <select name="<?= 'find_' . $fieldCode ?>" >
                            <option value="">-</option>
                            <? foreach ($arStatusExec as $value): ?>
                                <option <? if ($value == $arRequest['find_' . $fieldCode]): ?>selected<? endif; ?> value="<?= $value ?>"><?= Loc::getMessage('REALWEB.BEVENT.DATA_SUCCESS_EXEC.' . $value) . ' [' . $value . ']' ?></option>
                            <? endforeach; ?>
                        </select>
                        <?
                        break;
                    case 'DUPLICATE':
                        ?>
                        <select name="<?= 'find_' . $fieldCode ?>" >
                            <option value="">-</option>
                            <option <? if ('Y' == $arRequest['find_' . $fieldCode]): ?>selected<? endif; ?> value="Y"><?= Loc::getMessage('REALWEB.BEVENT.YES') . ' [Y]' ?></option>
                            <option <? if ('N' == $arRequest['find_' . $fieldCode]): ?>selected<? endif; ?> value="N"><?= Loc::getMessage('REALWEB.BEVENT.NO') . ' [N]' ?></option>
                        </select>
                        <?
                        break;
                    default:
                        ?>
                        <input type="text" name="<?= 'find_' . $fieldCode ?>" size="47" value="<?= htmlspecialchars($arRequest['find_' . $fieldCode]) ?>">
                    <?
                }
                ?>
            </td>
        </tr>
        <?
    }
    $oFilter->Buttons(array("table_id" => $sTableID, "url" => $APPLICATION->GetCurPage(), "form" => "find_form"));
    $oFilter->End();
    ?>
</form>

<? $lAdmin->DisplayList(); ?>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
