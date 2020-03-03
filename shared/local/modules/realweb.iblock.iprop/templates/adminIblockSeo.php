<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Localization\Loc;
?>
<tr class="heading">
    <td colspan="2"><?= Loc::getMessage('REALWEB.IBLOCK.IPROP.TEMPLATE_NAME') ?></td>
</tr>
<tr class="adm-detail-valign-top">
    <td width="40%"><?= Loc::getMessage('REALWEB.IBLOCK.IPROP.TEMPLATE_TITLE') ?></td>
    <td width="60%"><? echo IBlockInheritedPropertyInput($iIblockId, 'IBLOCK_META_TITLE', $arIPropertyTemplates, 'S') ?></td>
</tr>
<tr class="adm-detail-valign-top">
    <td width="40%"><?= Loc::getMessage('REALWEB.IBLOCK.IPROP.TEMPLATE_KEYWORDS') ?></td>
    <td width="60%"><? echo IBlockInheritedPropertyInput($iIblockId, 'IBLOCK_META_KEYWORDS', $arIPropertyTemplates, 'S') ?></td>
</tr>
<tr class="adm-detail-valign-top">
    <td width="40%"><?= Loc::getMessage('REALWEB.IBLOCK.IPROP.TEMPLATE_DESCRIPTION') ?></td>
    <td width="60%"><? echo IBlockInheritedPropertyInput($iIblockId, 'IBLOCK_META_DESCRIPTION', $arIPropertyTemplates, 'S') ?></td>
</tr>
<tr class="adm-detail-valign-top">
    <td width="40%"><?= Loc::getMessage('REALWEB.IBLOCK.IPROP.TEMPLATE_PAGETITLE') ?></td>
    <td width="60%"><? echo IBlockInheritedPropertyInput($iIblockId, 'IBLOCK_PAGE_TITLE', $arIPropertyTemplates, 'S') ?></td>
</tr>