<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

if(!$arResult["ITEMS"])
	return false;
?>
<div class="slider">
	<ul class="slider__wrapper">
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<?
			if (empty($arItem['PREVIEW_PICTURE']['SRC']))
				continue;
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			$imgSrc = $arItem['PREVIEW_PICTURE']['SRC'].'?v=20260915l';
			$imgAlt = htmlspecialcharsbx($arItem['NAME']);
			?>
			<li class="slider__item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
				<a class="slider__link" href="<?=$arItem['CODE']?>">
					<span class="slider__visual">
						<img src="<?=$imgSrc?>" alt="<?=$imgAlt?>" width="900" height="450">
					</span>
					<span class="container slider__content">
						<span class="slider__title"><?=$arItem['PREVIEW_TEXT']?></span>
					</span>
				</a>
			</li>
		<?endforeach;?>
	</ul>
</div>
