<?

namespace Realweb\PageType;

class PageType {

    public static $globals = array(
        '$_SERVER', '$_ENV', '$GLOBALS',
        '$_REQUEST', '$_GET', '$_POST',
        '$_COOKIE', '$_FILES', '$_SESSION',
    );

    function GetUserTypeDescription() {
        return array(
            "PROPERTY_TYPE" => "S",
            "USER_TYPE" => "PageType",
            "DESCRIPTION" => "Тип страницы",
            "PrepareSettings" => Array("Realweb\PageType\PageType", "PrepareSettings"),
            "GetSettingsHTML" => Array("Realweb\PageType\PageType", "GetSettingsHTML"),
            "GetPropertyFieldHtml" => Array("Realweb\PageType\PageType", "GetPropertyFieldHtml"),
            "GetAdminListViewHTML" => Array("Realweb\PageType\PageType", "GetAdminListViewHTML"),
            "ConvertToDB" => Array("Realweb\PageType\PageType", "ConvertToDB"),
            "ConvertFromDB" => Array("Realweb\PageType\PageType", "ConvertFromDB"),
        );
    }

    function PrepareSettings($arFields) {

        return array(
            "NOT_SHOW_COMPONENTS" => $arFields['USER_TYPE_SETTINGS']['NOT_SHOW_COMPONENTS'],
            "TEMPLATE_ID" => $arFields["USER_TYPE_SETTINGS"]["TEMPLATE_ID"]
        );
    }

    function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields) {
        //все шаблоны сайта
        $html = '';
        $sites_templates = array();
        $rsSites = \CSite::GetList($by = "sort", $order = "desc", Array());
        while ($arSite = $rsSites->Fetch()) {
            $rsSiteTemplates = \CSite::GetTemplateList($arSite['LID']);
            while ($arSiteTemplate = $rsSiteTemplates->Fetch()) {
                $sites_templates[$arSiteTemplate['ID']] = $arSiteTemplate['TEMPLATE'];
            }
        }


        $html .= '<tr>
        <td>Шаблон:</td>
        <td>
        <select name="' . $strHTMLControlName["NAME"] . '[TEMPLATE_ID]">';
        foreach ($sites_templates as $sites_template) {
            $selected = '';
            if ($arProperty["USER_TYPE_SETTINGS"]["TEMPLATE_ID"] == $sites_template) {
                $selected = 'selected="selected"';
            }
            $html .= '<option value="' . $sites_template . '"' . $selected . '>' . $sites_template . '</option>';
        }
        $html .= '</select>
            </td>
        </tr>';

        $components = \CHTMLEditor::GetComponents(Array());
        usort($components['items'], function ($a, $b) {
            return strcmp($a["name"], $b["name"]);
        });

        if (!is_array($arProperty["USER_TYPE_SETTINGS"]["NOT_SHOW_COMPONENTS"])) {
            $arProperty["USER_TYPE_SETTINGS"]["NOT_SHOW_COMPONENTS"] = array();
            foreach ($components['items'] as $component) {
                $arProperty["USER_TYPE_SETTINGS"]["NOT_SHOW_COMPONENTS"][] = $component['name'];
            }
        }

        $html .= '<tr class="heading"><td colspan="2">Не показывать компоненты</td></tr><tr>
        <td>Не показывать компоненты:</td>
        <td>
        <select size="' . count($components['items']) . '" name="' . $strHTMLControlName["NAME"] . '[NOT_SHOW_COMPONENTS][]" multiple>
            <option value="empty:empty">Выбрать</option>';
        foreach ($components['items'] as $component) {
            $selected = '';
            if (in_array($component['name'], $arProperty["USER_TYPE_SETTINGS"]["NOT_SHOW_COMPONENTS"])) {
                $selected = 'selected="selected"';
            }
            $html .= '<option value="' . $component['name'] . '"' . $selected . '>' . $component['name'] . '</option>';
        }

        $html .= '</select>
            </td>
        </tr>';


        return $html;
    }

    //PARAMETERS:
    //$arProperty - b_iblock_property.*
    //$value - array("VALUE","DESCRIPTION") -- here comes HTML form value
    //strHTMLControlName - array("VALUE","DESCRIPTION")
    //return:
    //safe html
    function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName) {
        if ($strHTMLControlName['MODE'] == 'FORM_FILL') {
            if (!is_array($arProperty["USER_TYPE_SETTINGS"]["NOT_SHOW_COMPONENTS"])) {
                $arProperty["USER_TYPE_SETTINGS"]["NOT_SHOW_COMPONENTS"] = array();
            }

            $COMPONENTS = self::GetComponents($arProperty["USER_TYPE_SETTINGS"]["NOT_SHOW_COMPONENTS"]);
            $JSON_COMPONENTS = self::GetJsonComponents($COMPONENTS);
            $arValues = array(
                'NONE' => array(
                    'TYPE' => 'option',
                    'LABEL' => 'Не использовать',
                    'VALUE' => 'NONE'
                ),
                'PAGE' => array(
                    'TYPE' => 'option',
                    'LABEL' => 'Текстовая страница',
                    'VALUE' => 'PAGE'
                ),
                'EXTERNAL_LINK' => array(
                    'TYPE' => 'option',
                    'LABEL' => 'Внешняя ссылка',
                    'VALUE' => 'EXTERNAL_LINK'
                ),
                'COMPONENTS' => array(
                    'TYPE' => 'optgroup',
                    'LABEL' => 'Компоненты',
                    'VALUES' => $COMPONENTS,
                ),
            );

            $size = count($arValues);
            foreach ($arValues as $arValue) {
                if ($arValue['TYPE'] == 'optgroup') {
                    $size = $size + count($arValue['VALUES']);
                }
            }
            $JSON_DATA_NAME = str_replace('[VALUE]', '_JSON', $strHTMLControlName['VALUE']);
            $JSON_DATA_NAME = str_replace(array("[", "]", ":"), "_", $JSON_DATA_NAME);
            $SELECT_ID = str_replace('_JSON', '_SELECT', $JSON_DATA_NAME);

            $JSON_DATA = array();

            if (isset($value['VALUE']['TYPE'])) {
                if ($value['VALUE']['TYPE'] == 'COMPONENT') {
                    if (isset($value['VALUE']['arParams'])) {
                        $JSON_DATA = $value['VALUE']['arParams'];
                    }
                } elseif ($value['VALUE']['TYPE'] == 'EXTERNAL_LINK') {
                    $JSON_DATA = array(
                        'LINK' => $value['VALUE']['LINK']
                    );
                }
            } else {
                $value['VALUE']['TYPE'] = 'NONE';
            }
            $TEMPLATE = ".default";
            if (isset($value['VALUE']['TEMPLATE'])) {
                $TEMPLATE = $value['VALUE']['TEMPLATE'];
            }

            $TYPE = 'PAGE';
            if (isset($value['VALUE']['TYPE'])) {
                $TYPE = $value['VALUE']['TYPE'];
            }

            $JSON_DATA = json_encode($JSON_DATA);

            //описание шаблонов
            $arTemplatesDesc = array();
            if ($TYPE == 'COMPONENT') {
                $arTemplates = \CComponentUtil::GetTemplatesList($value['VALUE']['COMPONENT_NAME']);
                foreach ($arTemplates as $arTemplate) {
                    if (strlen($arTemplate['TITLE']) > 0) {
                        $arTemplatesDesc[$arTemplate['NAME']] = $arTemplate;
                    }
                }
            }

            ob_start();
            ?>
            <table>
                <tbody>
                    <tr>
                        <td>
                            <div size="<?php echo $size; ?>" name="" id="<?php echo $SELECT_ID; ?>" style="max-height: 100px; overflow: scroll" >
                                <?php foreach ($arValues as $arValue): ?>
                                    <?php if ($arValue['TYPE'] == 'option'): ?>
                                        <?php
                                        $selected = "";
                                        if ($value['VALUE']['TYPE'] == $arValue['VALUE']) {
                                            $selected = ' checked="checked"';
                                        }
                                        ?>
                                        <div> <input type="radio"   name="__component<?php echo $SELECT_ID; ?>" value="<?php echo $arValue['VALUE']; ?>"<?php echo $selected; ?>><?php echo $arValue['LABEL']; ?></div>
                                    <?php elseif ($arValue['TYPE'] == 'optgroup'): ?>
                                        <div label="<?php echo $arValue['LABEL']; ?>" style="margin-left: 20px;">
                                            <?php echo $arValue['LABEL']; ?>
                                            <?php foreach ($arValue['VALUES'] as $arValueValue): ?>
                                                <?php
                                                $selected = "";
                                                if ($value['VALUE']['TYPE'] == 'COMPONENT' && $value['VALUE']['COMPONENT_NAME'] == $arValueValue['VALUE']) {
                                                    $selected = '  checked="checked"';
                                                }
                                                ?>
                                                <div> <input type="radio" name="__component<?php echo $SELECT_ID; ?>" value="<?php echo $arValueValue['VALUE']; ?>"<?php echo $selected; ?>><?php echo $arValueValue['LABEL']; ?></div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                <?php endforeach; ?>
                            </div>
                            <input name="<?php echo $strHTMLControlName['VALUE']; ?>" value='<?php echo serialize($value['VALUE']); ?>' id="<?php echo $strHTMLControlName['VALUE']; ?>" type="hidden"/>
                            <input name="<?php echo $JSON_DATA_NAME; ?>" id="<?php echo $JSON_DATA_NAME; ?>" value='<?php echo $JSON_DATA; ?>' type="hidden"/>
                            <input name="<?php echo $strHTMLControlName['DESCRIPTION']; ?>" value='<?php echo $TYPE; ?>' id="<?php echo $strHTMLControlName['DESCRIPTION']; ?>" type="hidden"/>
                            <hr>
                        </td>
                        <td style="text-align: left; padding-left: 20px;vertical-align: top;">
                            <?php if (isset($value['VALUE']['TYPE'])): ?>
                                <?php if ($value['VALUE']['TYPE'] == 'EXTERNAL_LINK'): ?>
                                    <?php if (isset($value['VALUE']['LINK'])): ?>
                                        <b><?php echo $value['VALUE']['LINK']; ?></b>
                                    <?php endif; ?>
                                <?php elseif ($value['VALUE']['TYPE'] == 'COMPONENT'): ?>
                                    <b>Компонент</b>: <?php echo $value['VALUE']['COMPONENT_NAME']; ?><br> 
                                    <b>Шаблон</b>: <?php echo $value['VALUE']['TEMPLATE']; ?>
                                    <?php if (isset($arTemplatesDesc[$value['VALUE']['TEMPLATE']])): ?>
                                        <br>
                                        <i><?php echo $arTemplatesDesc[$value['VALUE']['TEMPLATE']]['TITLE']; ?></i>
                                    <?php endif; ?>
                                <?php endif; ?>

                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php
            $html = ob_get_contents();
            ob_end_clean();
            return $html . self::GetScript($strHTMLControlName, $SELECT_ID, $JSON_DATA_NAME, $JSON_COMPONENTS, $arProperty, $TEMPLATE);
        } elseif ($strHTMLControlName['MODE'] == 'iblock_element_admin') {
            return self::GetAdminListViewHTML($arProperty, $value, $strHTMLControlName);
        } else {
            return '';
        }
    }

    public static function GetScript($strHTMLControlName, $SELECT_ID, $JSON_DATA_NAME, $JSON_COMPONENTS, $arProperty, $TEMPLATE) {
        ob_start();
        ?>
        <script>
            BX.ready(function () {
                var components = <?php echo json_encode($JSON_COMPONENTS); ?>;
                BX.bind(BX('<?php echo $SELECT_ID; ?>'), 'dblclick', function () {
                    console.log(258);
                    value = document.querySelector('#<?= $SELECT_ID ?> input:checked').value; //BX('<?php echo $SELECT_ID; ?>').value;
                    console.log(value);
                    if (components[value]) {
                        content_url = '/bitrix/admin/component_props2.php?IBLOCK_EDIT=Y&component_name=' +
                            components[value] +
                            '&component_template=<?php echo $TEMPLATE; ?>' +
                            '&template_id=<?php echo $arProperty["USER_TYPE_SETTINGS"]["TEMPLATE_ID"]; ?>' +
                            '&FORM_NAME=<?php echo $strHTMLControlName['FORM_NAME']; ?>' +
                            '&PROPERTY_INPUT=<?php echo $strHTMLControlName['VALUE']; ?>' +
                            '&JSON_INPUT=<?php echo $JSON_DATA_NAME; ?>' +
                            ''
                            ;
                        pop_up = (new BX.CDialog({'content_url': content_url, 'width': '1430', 'height': '851'}));
                        pop_up.Show();
                    } else if (value == 'EXTERNAL_LINK') {
                        content_url = '/bitrix/admin/external_link_pop_up.php?' +
                            'FORM_NAME=<?php echo $strHTMLControlName['FORM_NAME']; ?>' +
                            '&PROPERTY_INPUT=<?php echo $strHTMLControlName['VALUE']; ?>' +
                            '&JSON_INPUT=<?php echo $JSON_DATA_NAME; ?>' +
                            ''
                            ;
                        pop_up = (new BX.CDialog({'content_url': content_url, 'width': '600', 'height': '100'}));
                        pop_up.Show();
                    } else if (value == 'NONE') {

                        BX('<?php echo $strHTMLControlName['VALUE']; ?>').value = '';
                    }
                });
                BX.bind(BX('<?php echo $SELECT_ID; ?>'), 'click', function () {
                    value = BX('<?php echo $SELECT_ID; ?>').value;
                    if (components[value]) {
                        BX('<?php echo $strHTMLControlName['DESCRIPTION']; ?>').value = 'COMPONENT/' + components[value];
                    } else {
                        BX('<?php echo $strHTMLControlName['DESCRIPTION']; ?>').value = value;
                    }
                });
            });
        </script>

        <?php
        $script = ob_get_contents();
        ob_end_clean();
        return $script;
    }

    public static function GetComponents($filter = array()) {
        $components = array();
        $_components = \CHTMLEditor::GetComponents(Array());
        foreach ($_components['items'] as $component) {
            if (in_array($component['name'], $filter)) {
                continue;
            }
            $components[] = array(
                'VALUE' => $component['name'],
                'LABEL' => $component['title'] . ' [' . $component['name'] . ']'
            );
        }
        return $components;
    }

    public static function GetJsonComponents($COMPONENTS) {
        $return = array();
        foreach ($COMPONENTS as $COMPONENT) {
            $return[$COMPONENT['VALUE']] = $COMPONENT['VALUE'];
        }
        return $return;
    }

    function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName) {
        if ($value['VALUE']['TYPE'] == 'PAGE') {
            return '<div style="border: 1px dashed #B2BFC4;padding: 5px;">Текстовая страница</div>';
        } elseif ($value['VALUE']['TYPE'] == 'COMPONENT') {
            return '<div style="border: 1px dashed #B2BFC4;padding: 5px;"><b>Компонент</b>: ' . $value['VALUE']['COMPONENT_NAME'] . '<br> <b>Шаблон</b>:' . $value['VALUE']['TEMPLATE'] . '</div>';
        } elseif ($value['VALUE']['TYPE'] == 'EXTERNAL_LINK') {
            return '<div style="border: 1px dashed #B2BFC4;padding: 5px;"><b>Внешняя ссылка</b>: ' . $value['VALUE']['LINK'] . '</div>';
        }
        return '<div style="border: 1px dashed #B2BFC4;padding: 5px;">Текстовая страница</div>';
    }

    function ConvertToDB($arProperty, $value) {
        if ($value['DESCRIPTION'] == 'NONE') {
            $value['VALUE'] = "";
        } elseif ($value['DESCRIPTION'] == 'PAGE') {
            $value['VALUE'] = serialize(array('TYPE' => 'PAGE'));
        } elseif ($value['DESCRIPTION'] == 'EXTERNAL_LINK') {
            $unserialize = unserialize($value['VALUE']);
            if (is_array($unserialize)) {
                $unserialize['TYPE'] = 'EXTERNAL_LINK';
                $value['VALUE'] = serialize($unserialize);
            } else {
                $value['VALUE'] = serialize(array('TYPE' => 'EXTERNAL_LINK', 'LINK' => ""));
            }
        } elseif (strpos($value['DESCRIPTION'], 'COMPONENT/') !== false) {
            $unserialize = unserialize($value['VALUE']);
            $component = explode('/', $value['DESCRIPTION']);
            $component_name = $component[1];
            if (is_array($unserialize)) {
                $unserialize['TYPE'] = 'COMPONENT';
                $unserialize['COMPONENT_NAME'] = $component_name;
                $value['VALUE'] = serialize($unserialize);
            } else {
                $value['VALUE'] = serialize(array('TYPE' => 'COMPONENT', 'COMPONENT_NAME' => $component_name));
            }
        }
        $value['DESCRIPTION'] = "";
        return $value;
    }

    function ConvertFromDB($arProperty, $value) {
        if (strlen($value['VALUE']) > 0) {
            try {
                $VALUE = unserialize($value['VALUE']);
            } catch (\Exception $exc) {
                $VALUE = false;
            }
            if ($VALUE === false) {
                $value['VALUE'] = array(
                    'TYPE' => 'PAGE',
                    'COMPONENT_NAME' => "",
                    'TEMPLATE' => "",
                    'arParams' => array(),
                );
            } else {
                $value['VALUE'] = $VALUE;
            }
        } else {
            $value['VALUE'] = array(
                'TYPE' => 'PAGE',
                'COMPONENT_NAME' => "",
                'TEMPLATE' => "",
                'arParams' => array(),
            );
        }

        return $value;
    }

    public static function ProcessParams($arParams) {
        foreach ($arParams as &$arParam) {
            if (is_array($arParam)) {
                $arParam = self::ProcessParams($arParam);
            } else {
                $arParam = html_entity_decode($arParam);
                if (substr($arParam, 0, 1) == '$') {
                    $arParam = str_replace('$', "", $arParam);
                    $re = '/\[.*\]/';
                    $arParamTmp = $arParam;
                    $arParam = preg_replace($re, "", $arParam);
                    if (in_array('$' . $arParam, self::$globals)) {
                        $arParam = self::GetSuperGlobal($arParam);
                    } else {
                        $arParam = ${$arParam};
                    }
                    $arParam = self::GetArrKeyValue($arParam, $arParamTmp);
                }
            }
        }
        unset($arParam);
        return $arParams;
    }

    public static function GetArrKeyValue($arParam, $arParamStr) {
        if (!is_array($arParam)) {
            return $arParam;
        }
        $re = '/(\[.*\])/';
        preg_match_all($re, $arParamStr, $matches);
        if (count($matches) > 0) {
            $arr_params = explode('][', $matches[0][0]);
            array_walk($arr_params, function(&$item, $key) {
                $item = str_replace(array("[", "]", "'", '"'), "", $item);
            });
            foreach ($arr_params as $arr_param) {
                if (isset($arParam[$arr_param])) {
                    $arParam = $arParam[$arr_param];
                } else {
                    return NULL;
                }
            }
        }
        return $arParam;
    }

    public static function GetSuperGlobal($arParam) {
        $arParam = '$' . $arParam;
        switch ($arParam) {
            case '$_SERVER':
                return NULL;
                break;
            case '$_ENV':
                return NULL;
                break;
            case '$GLOBALS':
                return $GLOBALS;
                break;
            case '$_REQUEST':
                return $_REQUEST;
                break;
            case '$_GET':
                return $_GET;
                break;
            case '$_POST':
                return $_POST;
                break;
            case '$_COOKIE':
                return $_COOKIE;
                break;
            case '$_FILES':
                return NULL;
                break;
            case '$_SESSION':
                return $_SESSION;
                break;

            default:
                return NULL;
                break;
        }
    }

    public static function isElement($SEF_FOLDER, $IBLOCK_ID, &$arVariables) {
        global $APPLICATION, $CACHE_MANAGER;
        $cur_page = $APPLICATION->GetCurPage();

        $cacheId = $cur_page . "|" . SITE_ID . "|" . $IBLOCK_ID;
        $cache = new \CPHPCache;
        if ($cache->startDataCache(3600, $cacheId, "iblock_find")) {
            if (defined("BX_COMP_MANAGED_CACHE")) {
                $CACHE_MANAGER->StartTagCache("iblock_find");
                \CIBlock::registerWithTagCache($IBLOCK_ID);
            }


            $cur_page_arr = explode('/', trim($cur_page, $SEF_FOLDER));

            $path = "";
            $PREV_SECTION_CODE_PATH = "";
            foreach ($cur_page_arr as $key => $cur_page_part) {
                $path .= '/' . $cur_page_part;
                $arVariables["SECTION_CODE_PATH"] = trim($path, '/');
                if (!\CIBlockFindTools::checkSection($IBLOCK_ID, $arVariables)) {
                    if (strlen($PREV_SECTION_CODE_PATH) == 0) {
                        //проверим на корневой элемент
                        $arVariablesCheckRootElement = $arVariables;
                        $arVariablesCheckRootElement['SECTION_CODE_PATH'] = '';
                        $arVariablesCheckRootElement["ELEMENT_CODE"] = $arVariables["SECTION_CODE_PATH"];
                        if (!\CIBlockFindTools::checkElement($IBLOCK_ID, $arVariablesCheckRootElement)) {
                            if (defined("BX_COMP_MANAGED_CACHE")) {
                                $CACHE_MANAGER->AbortTagCache();
                            }
                            $cache->abortDataCache();
                            return false;
                        }
                        $arVariables = $arVariablesCheckRootElement;
                        if (defined("BX_COMP_MANAGED_CACHE")) {
                            $CACHE_MANAGER->EndTagCache();
                        }
                        $cache->endDataCache(array('IsElement' => true, 'arVariables' => $arVariables));
                        return true;
                    }
                    $arVariables["SECTION_CODE_PATH"] = $PREV_SECTION_CODE_PATH;
                    $arVariables["ELEMENT_CODE"] = $cur_page_part;
                    if (!\CIBlockFindTools::checkElement($IBLOCK_ID, $arVariables)) {
                        if (defined("BX_COMP_MANAGED_CACHE")) {
                            $CACHE_MANAGER->EndTagCache();
                        }
                        $cache->endDataCache(array('IsElement' => false, 'arVariables' => $arVariables));
                        return false;
                    }
                    //оставшееся в #SMART_FILTER_PATH#
                    $SMART_FILTER_PATH = array();
                    for ($k = $key + 1; $k <= count($cur_page_arr) - 1; $k++) {
                        $SMART_FILTER_PATH[] = $cur_page_arr[$k];
                    }
                    $arVariables['SMART_FILTER_PATH'] = implode('/', $SMART_FILTER_PATH);
                    if (defined("BX_COMP_MANAGED_CACHE")) {
                        $CACHE_MANAGER->EndTagCache();
                    }
                    $cache->endDataCache(array('IsElement' => true, 'arVariables' => $arVariables));
                    return true;
                }
                $PREV_SECTION_CODE_PATH = $arVariables["SECTION_CODE_PATH"];
            }
            if (defined("BX_COMP_MANAGED_CACHE")) {
                $CACHE_MANAGER->AbortTagCache();
            }
            $cache->abortDataCache();
            return false;
        } else {
            $vars = $cache->getVars();
            if (isset($vars['arVariables'])) {
                $arVariables = $vars['arVariables'];
            }
            return $vars['IsElement'];
        }
    }

    public static function GetExternalLink($arElement, $arParams) {
        if (isset($arParams['EXTERNAL_LINK_PROPERTY']) && strlen($arParams['EXTERNAL_LINK_PROPERTY']) > 0) {
            if (isset($arElement['PROPERTY_' . $arParams['EXTERNAL_LINK_PROPERTY'] . '_VALUE'])) {
                if (!isset($arElement['PROPERTY_' . $arParams['EXTERNAL_LINK_PROPERTY'] . '_VALUE']['TYPE'])) {
                    foreach ($arElement['PROPERTY_' . $arParams['EXTERNAL_LINK_PROPERTY'] . '_VALUE'] as $PAGE_TYPE) {
                        if ($PAGE_TYPE['TYPE'] == 'EXTERNAL_LINK') {
                            $arElement['DETAIL_PAGE_URL'] = $PAGE_TYPE['LINK'];
                            $arElement['~DETAIL_PAGE_URL'] = $PAGE_TYPE['LINK'];
                        }
                    }
                } else {
                    if ($arElement['PROPERTY_' . $arParams['EXTERNAL_LINK_PROPERTY'] . '_VALUE']['TYPE'] == 'EXTERNAL_LINK') {
                        $arElement['DETAIL_PAGE_URL'] = $arElement['PROPERTY_' . $arParams['EXTERNAL_LINK_PROPERTY'] . '_VALUE']['LINK'];
                        $arElement['~DETAIL_PAGE_URL'] = $arElement['PROPERTY_' . $arParams['EXTERNAL_LINK_PROPERTY'] . '_VALUE']['LINK'];
                    }
                }
            }
        }
        return $arElement;
    }

}
?>
