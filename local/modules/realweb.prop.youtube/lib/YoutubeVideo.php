<?php

namespace Realweb\PropYoutube;

class YoutubeVideo
{

    // инициализация пользовательского свойства для главного модуля
    public static function GetMainUserTypeDescription()
    {
        return array(
            "USER_TYPE_ID" => "YoutubeVideo",
            "CLASS_NAME" => "\Realweb\PropYoutube\YoutubeVideo",
            "DESCRIPTION" => "Видео Youtube",
            "BASE_TYPE" => "string",
        );
    }

    public static function GetDBColumnType($arUserField)
    {
        global $DB;
        switch (strtolower($DB->type)) {
            case "mysql":
                return "text";
            case "oracle":
                return "varchar2(2000 char)";
            case "mssql":
                return "varchar(2000)";
        }
    }

    public static function GetUserTypeDescription()
    {
        return array(
            "PROPERTY_TYPE" => "S",
            "USER_TYPE" => "YoutubeVideo",
            "DESCRIPTION" => "Видео Youtube",
            "GetPropertyFieldHtml" => array("\Realweb\PropYoutube\YoutubeVideo", "GetPropertyFieldHtml"),
        );
    }

    // редактирование свойства в форме (главный модуль)
    public static function GetEditFormHTML($arUserField, $arHtmlControl)
    {
        $chars = array(
            "abcdefghijklnmopqrstuvwxyz",
            "ABCDEFGHIJKLNMOPQRSTUVWXYZ",
            "0123456789",
        );
        $random_string = randString(6, $chars);
        $id = md5($arHtmlControl['VALUE'] . $random_string);
        $html = '<table><tbody><tr><td><input id="youtube_input_' . $id . '" size="30" type="text" name="' . $arHtmlControl['NAME'] . '" value="' . $arHtmlControl["VALUE"] . '" /></td>';
        $html .= '<td id="youtube_frame_' . $id . '" style="padding-left:5px;"><iframe ' . (strlen($arHtmlControl['VALUE']) > 0 ? '' : 'style="display:none;"') . ' width="200" height="100" src="https://www.youtube.com/embed/' . $arHtmlControl['VALUE'] . '" frameborder="0" allowfullscreen></iframe></td>';
        $html .= '</tr></tbody></table>';
        $html .= self::ReturnScript($id);
        return $html;
    }

    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        $COL_COUNT = intval($arProperty['COL_COUNT'] > 0 ? $arProperty['COL_COUNT'] : '30');
        $chars = array(
            "abcdefghijklnmopqrstuvwxyz",
            "ABCDEFGHIJKLNMOPQRSTUVWXYZ",
            "0123456789",
        );
        $random_string = randString(6, $chars);
        $id = md5($value['VALUE'] . $random_string);

        $html = '<table><tbody><tr><td><input id="youtube_input_' . $id . '" size="' . $COL_COUNT . '" type="text" name="' . $strHTMLControlName['VALUE'] . '" value="' . $value["VALUE"] . '" /></td>';
        $html .= '<td id="youtube_frame_' . $id . '" style="padding-left:5px;"><iframe ' . (strlen($value['VALUE']) > 0 ? '' : 'style="display:none;"') . ' width="200" height="100" src="https://www.youtube.com/embed/' . $value['VALUE'] . '" frameborder="0" allowfullscreen></iframe></td>';
        $html .= '</tr></tbody></table>';
        $html .= self::ReturnScript($id);


        return $html;
    }

    public static function ReturnScript($id)
    {
        ob_start();
        ?>
        <script>
            BX.ready(function () {
                BX.bind(
                    BX('youtube_input_<?php echo $id; ?>'), 'change',
                    function (e) {
                        if (!e) {
                            e = window.event;
                        }
                        iframe = BX.findChild(BX('youtube_frame_<?php echo $id; ?>'), {
                                "tag": "iframe",
                            },
                            false
                        );
                        BX.adjust(iframe, {style: {display: "none"}});

                        last = this.value.lastIndexOf('v=');
                        if (parseInt(last) > 0) {
                            this.value = this.value.slice(last + 2);
                        }

                        if (this.value.length > 0) {
                            BX.adjust(iframe, {props: {src: "https://www.youtube.com/embed/" + this.value}});
                            BX.adjust(iframe, {style: {display: "block"}});
                        }
                        return BX.PreventDefault(e);
                    }
                );

            });
        </script>
        <?php
        $script = ob_get_contents();
        ob_end_clean();
        return $script;
    }

}
