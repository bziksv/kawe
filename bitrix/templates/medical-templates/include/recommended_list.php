<?php
if(!$arParams['SECTION']['UF_RECOMMENDED_LIST'])
    return false;
?>
<hr class="hr">

<style>
    .goods__name__desc{
        font-weight: 400;
        font-size: smaller;
    }
</style>

<? foreach ($arParams['SECTION']['UF_RECOMMENDED_LIST'] as $p):
    $arSlider = explode('@', $p, 3);
    $title = $arSlider[0];
    $jProductRaw = json_decode($arSlider[1], true);
    $jProduct = [];
    if (is_array($jProductRaw)) {
        foreach ($jProductRaw as $productId => $productData) {
            $productId = trim((string)$productId);
            if ($productId === '' || !ctype_digit($productId)) {
                continue;
            }
            $jProduct[$productId] = $productData;
        }
    }

    if($jProduct && $arSlider[2]){
        $arResult = [];
        $arSelect = Array("ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PAGE_URL","PROPERTY_*");
        $arFilter = Array(
            "IBLOCK_ID" => $arParams['SECTION']['IBLOCK_ID'],
            "ID" => array_keys($jProduct),
            "ACTIVE" => "Y",
        );
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arProps = $ob->GetProperties();
            $itemId = (string)$arFields['ID'];

            $arResult['ITEMS'][$itemId] = $arFields;
            $arResult['ITEMS'][$itemId]['PREVIEW_PICTURE'] = CFile::GetPath($arFields['PREVIEW_PICTURE']);
            $arResult['ITEMS'][$itemId]['ARTICLS'] = $arProps['ARTICLS'];
            $arResult['ITEMS'][$itemId]['NAME'] = ($jProduct[$itemId][0]) ?: $arFields['NAME'];
            $arResult['ITEMS'][$itemId]['DESCRIPTION'] = $jProduct[$itemId][1];
			$arResult['ITEMS'][$itemId]['JS_HIDE'] = (isset($jProduct[$itemId][2]) && $jProduct[$itemId][2] == "Y") ? "Y" : "N";
        }

        if (empty($arResult['ITEMS'])) {
            continue;
        }
    } else {
        continue;
    }
    ?>
<div class="title"><?=$title;?></div>

    <?
    /* RECOMMENDED VIEW */
    $APPLICATION->IncludeFile(SITE_TEMPLATE_PATH."/include/recommended_view/". trim($arSlider[2]) .".php", Array("DATA" => $arResult), Array(
        "MODE"      => "php",
        "NAME"      => "",
        "TEMPLATE"  => "",
        "SHOW_BORDER" => false,
    ));
    ?>

<? endforeach;?>
