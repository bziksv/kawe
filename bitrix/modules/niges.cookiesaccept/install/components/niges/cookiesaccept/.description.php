<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("NCA_NAME"),
	"DESCRIPTION" => GetMessage("NCA_DESC"),
	"CACHE_PATH" => "Y", 
	"PATH" => array(
		"ID" => "service",
		"CHILD" => array(
			"ID" => "advertising",
			"NAME" => GetMessage("NCA_THE")
		)
	),
);
?>