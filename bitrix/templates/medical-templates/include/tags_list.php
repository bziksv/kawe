<?php
if(!$arParams['SECTION']['UF_TAGS_ACTIVE'])
    return false;

if (empty($arParams['SECTION']['UF_TAGS_LIST']) || !is_array($arParams['SECTION']['UF_TAGS_LIST']))
    return false;

$arValidTags = [];
$iblockId = (int)$arParams['SECTION']['IBLOCK_ID'];

foreach ($arParams['SECTION']['UF_TAGS_LIST'] as $tags) {
    $arTags = explode('@', $tags, 2);
    if (count($arTags) < 2) {
        continue;
    }

    $title = trim($arTags[0]);
    $url = trim($arTags[1]);
    if ($title === '' || $url === '') {
        continue;
    }

    $path = parse_url($url, PHP_URL_PATH);
    if ($path === null || $path === false) {
        $path = $url;
    }

    // Only hide when URL points to a known inactive catalog product.
    // Prod may rename CODE to "{old}-deactivated-{id}" while UF keeps the old path.
    // Prefer exact CODE: if an active product reuses the old CODE, keep the tile.
    // /search/, custom paths, sections without matching element — keep as-is.
    if ($iblockId > 0 && strpos($path, '/catalog/') === 0) {
        $code = basename(rtrim($path, '/'));
        if ($code !== '' && $code !== 'catalog') {
            $hideInactive = false;

            $resExact = CIBlockElement::GetList(
                [],
                [
                    'IBLOCK_ID' => $iblockId,
                    'CHECK_PERMISSIONS' => 'N',
                    '=CODE' => $code,
                ],
                false,
                ['nTopCount' => 1],
                ['ID', 'ACTIVE', 'CODE']
            );
            if ($elExact = $resExact->Fetch()) {
                if ($elExact['ACTIVE'] !== 'Y') {
                    $hideInactive = true;
                }
            } else {
                // No exact CODE: look for renamed inactive "{code}-deactivated-{id}"
                $resDeactivated = CIBlockElement::GetList(
                    [],
                    [
                        'IBLOCK_ID' => $iblockId,
                        'CHECK_PERMISSIONS' => 'N',
                        // Bitrix "?FIELD" = LIKE %value% (suffix pattern)
                        '?CODE' => $code . '-deactivated-',
                    ],
                    false,
                    ['nTopCount' => 5],
                    ['ID', 'ACTIVE', 'CODE']
                );
                while ($elDeactivated = $resDeactivated->Fetch()) {
                    if ($elDeactivated['ACTIVE'] !== 'Y') {
                        $hideInactive = true;
                        break;
                    }
                }
            }

            if ($hideInactive) {
                continue;
            }
        }
    }

    $arValidTags[] = [$title, $url];
}

if (!$arValidTags) {
    return false;
}

// CSS: medical-templates/css/main.css (.tags-list / .sub-links-2). slick.css → head (не <link> в body — cssinliner иначе делает <style> внутри div).
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/slick.css');
?>

<div class="tags-list">

    <div class="subcategories">
        <ul class="tag-slider sub-links-2">
            <? foreach ($arValidTags as $arTags): ?>
            <li><a href="<?=htmlspecialcharsbx($arTags[1])?>"><?=htmlspecialcharsbx($arTags[0])?></a></li>
            <? endforeach; ?>
        </ul>
        <div class="navi">
            <span class="open">Показать все</span>
            <span hidden class="close">Свернуть</span>
        </div>
    </div>

</div>

<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/css/slick.min.js"></script>
<script>

    $('.tag-slider').slick({
        dots: false,
        arrows: true,
        infinite: true,
        autoplay: true,
        variableWidth: true,
        centerMode: true,
        slidesToShow: 3,
        responsive: [
            {
                breakpoint: 767,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    $(document).ready(function() {
        $(".subcategories .open").click(function(){
            $(this).hide();
            $(".subcategories .close").show();
            $(".subcategories .sub-links-2").addClass("open");
            $('.tag-slider').slick('unslick');
        });
        $(".subcategories .close").click(function(){
            $(this).hide();
            $(".subcategories .open").show();
            $(".subcategories .sub-links-2").removeClass("open");
            $('.tag-slider').slick({
                dots: false,
                arrows: true,
                infinite: true,
                autoplay: true,
                variableWidth: true,
                centerMode: true,
                slidesToShow: 3,
            });
        });
    });

</script>
