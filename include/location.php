<? if(!$arCityes = include $_SERVER['DOCUMENT_ROOT'].'/.cityes.php') return false;

// LOCATION_PART: li | popup | all (default).
// li — только пункт меню (валидно внутри <ul>); popup — модалка+script снаружи <ul>.
// Стили города — в medical-templates/css/main.css (.city .item-city), не <style> в body.
$part = isset($LOCATION_PART) ? (string)$LOCATION_PART : 'all';
if ($part === '') {
	$part = 'all';
}
?>

<? if ($part === 'li' || $part === 'all'): ?>
<li class="nav__item">
    <a href="#" class="nav__link" id="location_btn">
        <?=($_COOKIE['city']) ?: 'Выбор города'?>
        <i class="icon-arrow_down" style="transform: none;"></i>
    </a>
</li>
<? endif; ?>

<? if ($part === 'popup' || $part === 'all'): ?>
<div class="mfeedback-p" id="location" style="max-width: 500px;">
    <span class="button b-close"><span>&times;</span></span>
    <div class="mfeedback-p-head">Выбор города</div>

    <? if($arCityes['show'] && $arCityes['hide']): ?>
    <div class="city">
        <? if($arCityes['show']): ?>
        <div class="item-city">
            <? foreach ($arCityes['show'] as $s):?>
            <a href="javascript:void(0)" class="c"><?=$s?></a>
            <? endforeach; ?>
        </div>
        <? endif; ?>

        <div class="item-city">
            <a href="javascript:void(0)" class="city-show" onclick="$(this).closest('.city').find('.item-city:last-child').toggleClass('active'); return false;">Показать все города</a>
        </div>

        <? if($arCityes['hide']): ?>
        <div class="item-city">
            <? foreach ($arCityes['hide'] as $h):?>
            <a href="javascript:void(0)" class="c"><?=$h?></a>
            <? endforeach; ?>
        </div>
        <? endif; ?>
    </div>
    <? endif; ?>
</div>

<script>
    $('#location_btn').click(function(){
        $('#location').bPopup({
            zIndex:1000,
            position: ['auto', 50]
        });
    });

    $('.city .item-city a.c').click(function(){
        var city = $(this).text();
        document.cookie = "city=" + city + "; path=/;";
        $('#location_btn').html(city + '<i class="icon-arrow_down" style="transform: none;"></i>');

        var bPopup = $('#location').bPopup();
        bPopup.close();
    });
</script>
<? endif; ?>
