<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

if (empty($arResult["ALL_ITEMS"]))
	return;
?>
	<a href="#" class="toggle__menu"><span></span></a>
	<ul class="nav">
		<? foreach($arResult["MENU_STRUCTURE"] as $key => $str):?>
		<li class="nav__item">
			<a href="<?=(!$str)?$arResult["ALL_ITEMS"][$key]['LINK']:"#"?>" class="nav__link">
				<?=$arResult["ALL_ITEMS"][$key]['TEXT']?>
				<?if($str):?>
				<i class="icon-arrow_down"></i>
				<? endif;?>
			</a>
			<?if($str):?>
			<ul class="nav__submenu">
				<? foreach($str[1] as $sub_key => $sub): ?>
				<li><a href="<?=$arResult["ALL_ITEMS"][$sub_key]['LINK']?>" class="nav__submenu_link"><?=$arResult["ALL_ITEMS"][$sub_key]['TEXT']?></a></li>
				<? endforeach;?>
			</ul>
			<? endif;?>
		</li>
		<? endforeach; ?>

        <?
        // Только <li> внутри <ul> — popup/style/script нельзя класть в список (валидатор HTML).
        $APPLICATION->IncludeFile("/include/location.php", Array("LOCATION_PART" => "li"), Array(
            "MODE"      => "php",
            "NAME"      => "Редактирование включаемой области раздела",
            "TEMPLATE"  => ""
        ));
        ?>
	</ul>
	<?
	$APPLICATION->IncludeFile("/include/location.php", Array("LOCATION_PART" => "popup"), Array(
		"MODE"      => "php",
		"NAME"      => "Редактирование включаемой области раздела",
		"TEMPLATE"  => ""
	));
	?>
