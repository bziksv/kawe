
$('input[name="PHONE"]').mask('+7 (999) 999 99 99');
$('input[autocomplete="tel"]').mask('+7 (999) 999 99 99');

$('.cart__content .cart__radio').change(function(){
    $('.cart__content > p.article').text("Арт: " + $(this).val());
    $('.cart__content > .cart__price').text($(this).attr('data-price'));
    $('.cart__content > .cart_old_price').text($(this).attr('data-old-price'));
});

$('.cart__content > p.article').text("Арт: " + $('.cart__content .cart__radio:checked').val());

if($('.cart__content .cart__radio:checked').attr('data-price') != 'Цена 0 ₽'){
		$('.cart__content > .cart__price').text($('.cart__content .cart__radio:checked').attr('data-price'));
}

$('.cart__content > .cart_old_price').text($('.cart__content .cart__radio:checked').attr('data-old-price'));


$('.callback-btn').click(function(){
    $('#callback').bPopup({
        zIndex:1000
    });
});

function ensureBitrixSessid($form) {
    var $sessid = $form.find('input[name="sessid"]');
    if ($sessid.length && !$sessid.val() && typeof BX !== 'undefined' && typeof BX.bitrix_sessid === 'function') {
        $sessid.val(BX.bitrix_sessid());
    }
}

function validateConsentForm($form, checkboxSelector, wrapSelector) {
    var $checkbox = $form.find(checkboxSelector);
    var $wrap = $form.find(wrapSelector);
    var $error = $wrap.find('.mf-consent-error');

    if ($checkbox.length && !$checkbox.is(':checked')) {
        $error.show();
        if (typeof alertify !== 'undefined') {
            alertify.error('Необходимо дать согласие на обработку персональных данных');
        }
        return false;
    }

    $error.hide();
    return true;
}

$(document).on('submit', '#callback form', function(e) {
    var $form = $(this);
    ensureBitrixSessid($form);
    if (!validateConsentForm($form, '#callback-consent', '#callback-consent-wrap')) {
        e.preventDefault();
        return false;
    }
});

$(document).on('change', '#callback-consent', function() {
    $('#callback-consent-wrap .mf-consent-error').hide();
});

$(document).on('submit', '.mfeedback form', function(e) {
    var $form = $(this);
    ensureBitrixSessid($form);
    if (!validateConsentForm($form, '#feedback-consent', '#feedback-consent-wrap')) {
        e.preventDefault();
        return false;
    }
});

$(document).on('change', '#feedback-consent', function() {
    $('#feedback-consent-wrap .mf-consent-error').hide();
});

function openCallbackPopup() {
    if ($.fn.bPopup) {
        $('#callback').bPopup({ zIndex: 1000 });
    }
}

function handleCallbackFormResult() {
    var params = new URLSearchParams(window.location.search);
    var successHash = params.get('success');
    var expectedHash = $('#callback').data('params-hash');
    var okText = $.trim($('#callback .mf-ok-text').text());

    if (successHash && expectedHash && successHash === expectedHash) {
        openCallbackPopup();
        if (typeof alertify !== 'undefined') {
            alertify.success(okText || 'Спасибо, ваше сообщение принято.');
        }
        if (window.history && window.history.replaceState) {
            var url = new URL(window.location.href);
            url.searchParams.delete('success');
            window.history.replaceState({}, document.title, url.pathname + url.search + url.hash);
        }
        return;
    }

    if ($('#callback .errortext').length) {
        openCallbackPopup();
        if (typeof alertify !== 'undefined') {
            alertify.error($.trim($('#callback .errortext').first().text()));
        }
        return;
    }
}

$(function() {
    handleCallbackFormResult();
});



var path = "/bitrix/templates/medical-templates/ajax/";

function replaseBasketTop() {
    $.ajax({
        url: path + 'basket.php',
        type: 'get',
        success: function (data) {
            $('.header__basket').replaceWith(data);
        }
    })
}

function replaseBasketMobileTop() {
    $.ajax({
        url: path + 'basket.mobile.php',
        type: 'get',
        success: function (data) {
            $('.header__basket_mobile').replaceWith(data);
        }
    })
}


function addToBasket2(idel, quantity,el) {
    var $root = $(el).closest('.cart__content, .goods__item'),
        art = $root.find('.cart__radio:checked').val();

    if (!art)
        art = $root.find('input[name="article"]').val();

    if (!art || art === 'undefined' || art === 'null')
        art = '';

    var color = $.trim($root.find('.cart__radio:checked').parent().text());
    if (!color)
        color = $root.find('input[name="color"]').val();

    if (!color || color === 'undefined' || color === 'null')
        color = 0;

    var requestData = {
        id: idel,
        quantity: quantity
    };

    if (art)
        requestData.art = art;

    if (color)
        requestData.color = color;

    $.ajax({
        url: path + "add.php",
        type: 'get',
        data: requestData,
        success: function (data) {
            console.log(data);
            if (data == 'Товар успешно добавлен в корзину') {
                replaseBasketTop();
                replaseBasketMobileTop();
                alertify.success(data);
            } else {
                alertify.error(data);
            }
        }
    });
    return false;
}


$( function() {
    $( ".cart__price.tooltip,.goods__price.tooltip" ).tooltip({
        show: null,
        content: "<noindex>Цена зависит от комплектации прибора и/или наличия на складе. Для уточнения стоимости необходимо отправить запрос по электронной почте (запросить КП),  либо оформить заказ на сайте  и менеджер сам вам перезвонит. Если указанная цена вас не устроит, Вы можете отказаться от товара до момента его оплаты.</noindex>",
        items: "div[class]",
        position: {
            my: "left top",
            at: "left bottom"
        },
        open: function( event, ui ) {
            ui.tooltip.animate({ top: ui.tooltip.position().top + 10 }, "fast" );
        }
    });
} );

window.roistatVisitCallback = function(visitId) {
    var mail = visitId + "@" + window.location.hostname;
    var roi = $('.roi_visit');
    roi.text(mail);
    roi.attr('href','mailto:' + mail);
};