<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$APPLICATION->IncludeComponent(
    "realweb:realweb.csscompiler.template", "less", array(
    'TEMPLATE_PATH' => $this->__component_epilog['templateFolder'] . '/'
    ), false, array(
    "HIDE_ICONS" => "Y"
    )
);
