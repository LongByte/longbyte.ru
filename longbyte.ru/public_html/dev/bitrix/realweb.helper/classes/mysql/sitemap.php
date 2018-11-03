<?

class CSiteMapRealweb extends CAllSiteMapRealweb {

    protected $arRules = array();

    function GetURLs($site_id, $ID, $limit = 0) {
        $DB = CDatabase::GetModuleConnection('search');
        $strSql = "
		SELECT
			sc.ID
			,sc.MODULE_ID
			,sc.ITEM_ID
			,sc.TITLE
			,sc.PARAM1
			,sc.PARAM2
			,sc.UPD
			,sc.DATE_FROM
			,sc.DATE_TO
			,L.DIR
			,L.SERVER_NAME
			,sc.URL as URL
			,scsite.URL as SITE_URL
			,scsite.SITE_ID
			," . $DB->DateToCharFunction("sc.DATE_CHANGE") . " as FULL_DATE_CHANGE
			," . $DB->DateToCharFunction("sc.DATE_CHANGE", "SHORT") . " as DATE_CHANGE
		FROM	b_search_content sc
			INNER JOIN b_search_content_site scsite ON sc.ID=scsite.SEARCH_CONTENT_ID
			INNER JOIN b_lang L ON scsite.SITE_ID=L.LID
			INNER JOIN b_search_content_right scg ON sc.ID=scg.SEARCH_CONTENT_ID
		WHERE
			scg.GROUP_CODE='G2'
			AND scsite.SITE_ID='" . $DB->ForSQL($site_id, 2) . "'
			AND (sc.DATE_FROM is null OR sc.DATE_FROM <= " . $DB->CurrentTimeFunction() . ")
			AND (sc.DATE_TO is null OR sc.DATE_TO >= " . $DB->CurrentTimeFunction() . ")
			AND sc.ID > " . intval($ID) . "
		ORDER BY
			sc.ID
		";
        if (intval($limit) > 0) {
            $strSql .= "LIMIT " . intval($limit);
        }
        $r = $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        parent::CDBResult($r->result);
    }

    function LoadRules() {

        if (!CModule::IncludeModule('iblock'))
            return;

        $arRulesIBlock = CIBlock::GetList(array(), array(
                'CODE' => 'sitemap'
            ))->Fetch();

        if (!$arRulesIBlock)
            return;

        $rsRules = CIBlockElement::GetList(
                array('SORT' => 'DESC', 'ID' => 'DESC'), //
                array('IBLOCK_ID' => $arRulesIBlock['ID'], 'ACTIVE' => 'Y'), //
                false, //
                false, //
                array('ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_PRIORITY', 'PROPERTY_CHANGEFREQ', 'PROPERTY_BANRULE')
        );

        while ($arRule = $rsRules->fetch()) {
            $this->arRules[] = array(
                'URL' => $arRule['NAME'],
                'PRIORITY' => $arRule['PROPERTY_PRIORITY_VALUE'],
                'CHANGEFREQ' => $arRule['PROPERTY_CHANGEFREQ_VALUE'],
                'BANRULE' => $arRule['PROPERTY_BANRULE_VALUE'],
            );
        }
    }

}

?>