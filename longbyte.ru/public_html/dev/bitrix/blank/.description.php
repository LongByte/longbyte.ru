<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => "Пустой компонент",
	"DESCRIPTION" => "не кешируется",
	"ICON" => "/images/include.gif",
	"PATH" => array(
		"ID" => "utility",
		"CHILD" => array(
			"ID" => "include_area",
			"NAME" => "Сервис",
		),
	),
);
?>