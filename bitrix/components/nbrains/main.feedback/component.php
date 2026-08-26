<?php
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */



$hashParams = $arParams;
unset($hashParams['ROI_VISIT']);
$arResult["PARAMS_HASH"] = md5(serialize($hashParams).$this->GetTemplateName());
$arParams["USE_CAPTCHA"] = $arParams["USE_CAPTCHA"];

$arParams["EVENT_NAME"] = trim($arParams["EVENT_NAME"]);
if($arParams["EVENT_NAME"] == '')
	$arParams["EVENT_NAME"] = "FEEDBACK_FORM";
$arParams["EMAIL_TO"] = trim($arParams["EMAIL_TO"]);
if($arParams["EMAIL_TO"] == '')
	$arParams["EMAIL_TO"] = COption::GetOptionString("main", "email_from");
$arParams["OK_TEXT"] = trim($arParams["OK_TEXT"]);
if($arParams["OK_TEXT"] == '')
	$arParams["OK_TEXT"] = GetMessage("MF_OK_MESSAGE");

//var_dump($arParams['PROPERTY_CODE']);
//var_dump($arParams['IBLOCK_ID']);

$arPropertyFild = array();
foreach($arParams['PROPERTY_CODE'] as $code){
	$rsProp = CIBlockProperty::GetList(array(), array("ACTIVE"=>"Y", "IBLOCK_ID" => $arParams['IBLOCK_ID'],"CODE" => $code));
	if($arr = $rsProp->Fetch())
	{ $arPropertyField[] = $arr; }
}

if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["submit"] <> '')
{
	if(!isset($_POST["PARAMS_HASH"]) || $arResult["PARAMS_HASH"] !== $_POST["PARAMS_HASH"])
	{
		$arResult["ERROR_MESSAGE"] = array(GetMessage("MF_REQ") ?: "Ошибка отправки формы. Обновите страницу и попробуйте снова.");
	}
	elseif(check_bitrix_sessid())
	{
		$arResult["ERROR_MESSAGE"] = array();

		foreach($arPropertyField as $field){
			if($field['IS_REQUIRED'] == "Y"){
				if(strlen($_POST[$field['CODE']]) < 1)
					$arResult["ERROR_MESSAGE"][] = GetMessage("FIELD_ERROR").': '.$field['NAME'];
			}
		}

		$consentField = '';
		if($this->GetTemplateName() == 'popup-callback')
			$consentField = 'callback-consent';
		elseif($this->GetTemplateName() == 'feedback')
			$consentField = 'feedback-consent';

		if($consentField !== '' && empty($_POST[$consentField]))
			$arResult["ERROR_MESSAGE"][] = 'Необходимо дать согласие на обработку персональных данных';

		if(empty($arResult["ERROR_MESSAGE"])){
			$el = new CIBlockElement;
			$PROP = array();
			foreach($arPropertyField as $field) {
				if($field['PROPERTY_TYPE'] == "S"){
					if($field["USER_TYPE"]){
						$PROP[$field['CODE']] = array("VALUE" => array("TEXT" => trim(strip_tags($_POST[$field['CODE']])),"TYPE" => "text"));
					}else{
						$PROP[$field['CODE']] = trim(strip_tags($_POST[$field['CODE']]));
					}
				}elseif($field['PROPERTY_TYPE'] == "L"){
					$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID" => $arParams['IBLOCK_ID'], "CODE"=> $field["CODE"]));
					if($enum_fields = $property_enums->GetNext())
					{
						$PROP[$field['CODE']] = Array("VALUE" => $enum_fields['ID']);
					}
				}elseif($field['PROPERTY_TYPE'] == "F"){

					$img = $_FILES['FILE'];
					$fid = CFile::SaveFile($img, "vote");
					$PROP[$field['CODE']] = Array("VALUE" => $fid);

				}
			}

			$arLoadProductArray = Array(
				"IBLOCK_SECTION_ID" => false,
				"IBLOCK_ID"      => $arParams['IBLOCK_ID'],
				"PROPERTY_VALUES"=> $PROP,
				"NAME"           => trim(strip_tags($_POST[$arPropertyField[0]['CODE']])),
				"ACTIVE"         => "Y"
			);
			$PRODUCT_ID = $el->Add($arLoadProductArray);
		}


		if(empty($arResult["ERROR_MESSAGE"]))
		{
			$arFields = Array();
			$roiVisit = !empty($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : '';
			if($roiVisit !== '')
			    $arFields['ROI_VISIT'] = $roiVisit;

			foreach($arPropertyField as $field){
				if($field['CODE'] == "PRODUCT_CART"){
					$arFields[$field['CODE']] = $_POST[$field['CODE']];
					CSaleBasket::DeleteAll(CSaleBasket::GetBasketUserID(), false);
				}else{
					$arFields[$field['CODE']] = trim(strip_tags($_POST[$field['CODE']]));
				}
				$arFields["EMAIL_TO"] = $arParams["EMAIL_TO"];
			}
			if(!empty($arParams["EVENT_MESSAGE_ID"]))
			{
				foreach($arParams["EVENT_MESSAGE_ID"] as $v)
					if(IntVal($v) > 0)
						CEvent::Send($arParams["EVENT_NAME"], SITE_ID, $arFields, "N", IntVal($v));
			}
			else
			CEvent::Send($arParams["EVENT_NAME"], SITE_ID, $arFields);

			$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
				&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

			if($isAjax)
			{
				$arResult["OK_MESSAGE"] = $arParams["OK_TEXT"];
			}
			else
			{
				LocalRedirect($APPLICATION->GetCurPageParam("success=".$arResult["PARAMS_HASH"], Array("success")));
			}
		}

		foreach($arPropertyField as $field){
			$arResult[$field['CODE']] = trim(strip_tags($_POST[$field['CODE']]));
		}
	}
	else
		$arResult["ERROR_MESSAGE"] = array(GetMessage("MF_SESS_EXP"));
}
elseif($_REQUEST["success"] == $arResult["PARAMS_HASH"])
{
	$arResult["OK_MESSAGE"] = $arParams["OK_TEXT"];
}
$arResult['USER_FIELD'] = $arPropertyField;

$this->IncludeComponentTemplate();
