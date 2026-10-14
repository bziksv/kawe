<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>

<div class="catalog">
	<div class="catalog__head">Каталог товаров</div>
<ul class="catalog__menu">
<?
$previousLevel = 0;
$deleteIndex = (isset($arResult['PROPERTIES']['UF_DELETE_INDEX']) && is_array($arResult['PROPERTIES']['UF_DELETE_INDEX']))
	? $arResult['PROPERTIES']['UF_DELETE_INDEX']
	: array();

foreach($arResult as $key => $arItem):
	// result_modifier кладёт PROPERTIES в $arResult — не пункт меню (DEPTH_LEVEL нет → лишний </ul></li>).
	if (!is_array($arItem) || !isset($arItem['DEPTH_LEVEL']))
		continue;
?>
	<?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
		<?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
	<?endif?>

	<?if ($arItem["IS_PARENT"]):?>
			<li<?if($arItem["CHILD_SELECTED"] !== true):?> <?endif?> class="catalog__item" <?=(!empty($arItem["PARAMS"]["HIDE_SECTION"])) ? 'style="display:none"' : ''?>>

                <? if(!empty($arItem["PARAMS"]["ID"]) && in_array($arItem["PARAMS"]["ID"], $deleteIndex)): ?>
                    <a href="<?=$arItem["LINK"]?>" class="catalog__link" data-text="<?=$arItem["TEXT"]?>"></a>
                <? else: ?>
                    <a href="<?=$arItem["LINK"]?>" class="catalog__link"><?=$arItem["TEXT"]?></a>
                <? endif; ?>

                <i class="catalog__submenu_toggle icon-arrow_down"></i>
				<ul class="catalog__submenu">
	<?else:?>

		<?if ($arItem["PERMISSION"] > "D"): ?>
				<li <?if($arItem["DEPTH_LEVEL"] == 1):?>class="catalog__item"<?endif;?> <?=(!empty($arItem["PARAMS"]["HIDE_SECTION"])) ? 'style="display:none"' : ''?>>
                    <? if(!empty($arItem["PARAMS"]["ID"]) && in_array($arItem["PARAMS"]["ID"], $deleteIndex)): ?>
                        <a href="<?=$arItem["LINK"]?>" class="<?if($arItem["DEPTH_LEVEL"] > 1):?>catalog__submenu_link<?else:?>catalog__link<?endif;?>" data-text="<?=$arItem["TEXT"]?>"></a>
                    <? else: ?>
                        <a href="<?=$arItem["LINK"]?>" class="<?if($arItem["DEPTH_LEVEL"] > 1):?>catalog__submenu_link<?else:?>catalog__link<?endif;?>"><?=$arItem["TEXT"]?></a>
                    <? endif; ?>
				</li>
		<?endif?>

	<?endif?>
	<?$previousLevel = $arItem["DEPTH_LEVEL"];?>
<?endforeach?>

<?if ($previousLevel > 1)://close last item tags?>
	<?=str_repeat("</ul></li>", ($previousLevel-1) );?>
<?endif?>

</ul>
</div>
<?endif?>
