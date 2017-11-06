<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("EDVANCE_IBLOCK_SECTION_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("EDVANCE_IBLOCK_SECTION_TEMPLATE_DESCRIPTION"),
	"ICON" => "/images/cat_list.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 30,
	"PATH" => array(
		"ID" => "content",
		"CHILD" => array(
			"ID" => "catalog",
			"SORT" => 30,
			"CHILD" => array(
				"ID" => "catalog_cmpx",
			),
		),
	),
);

?>