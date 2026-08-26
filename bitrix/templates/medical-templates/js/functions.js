
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

function validateConsentForm(formEl, checkboxSelector, wrapSelector) {
    var $form = $(formEl);
    var $checkbox = $form.find(checkboxSelector);
    if (!$checkbox.length) {
        $checkbox = $(checkboxSelector);
    }
    var $wrap = $(wrapSelector);
    var $error = $wrap.find('.mf-consent-error');

    if (!$checkbox.length || !$checkbox.is(':checked')) {
        $error.show();
        if (typeof alertify !== 'undefined') {
            alertify.error('Необходимо дать согласие на обработку персональных данных');
        }
        return false;
    }

    $error.hide();
    return true;
}

function initCallbackFormFields() {
    $('#callback input[name="PHONE"]').mask('+7 (999) 999 99 99');
}

function updateCallbackFromHtml(html) {
    var $parsed = $($.parseHTML(html, document, true));
    var $new = $parsed.filter('#callback').add($parsed.find('#callback')).first();
    if (!$new.length) {
        return false;
    }

    var $callback = $('#callback');
    $callback.attr('data-params-hash', $new.attr('data-params-hash') || '');
    $callback.html($new.html());
    initCallbackFormFields();
    return true;
}

function submitCallbackFormAjax(form) {
    var $form = $(form);
    var $btn = $form.find('[type="submit"]').prop('disabled', true);

    $.ajax({
        url: $form.attr('action') || window.location.pathname,
        type: 'POST',
        data: $form.serialize(),
        dataType: 'html'
    }).done(function(html) {
        if (!updateCallbackFromHtml(html)) {
            document.open();
            document.write(html);
            document.close();
            return;
        }

        ensureCallbackPopupOpen();

        if ($('#callback .mf-ok-text').length) {
            if (typeof alertify !== 'undefined') {
                alertify.success($.trim($('#callback .mf-ok-text').text()) || 'Спасибо, ваше сообщение принято.');
            }
        } else if ($('#callback .errortext').length) {
            if (typeof alertify !== 'undefined') {
                alertify.error($.trim($('#callback .errortext').first().text()));
            }
        }
    }).fail(function() {
        if (typeof alertify !== 'undefined') {
            alertify.error('Ошибка отправки. Попробуйте ещё раз.');
        }
    }).always(function() {
        $btn.prop('disabled', false);
    });
}

$(document).on('submit', '#callback form', function(e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    var form = this;
    var $form = $(form);
    ensureBitrixSessid($form);
    if (!validateConsentForm(form, '#callback-consent', '#callback-consent-wrap')) {
        var consentWrap = document.getElementById('callback-consent-wrap');
        if (consentWrap) {
            consentWrap.scrollIntoView({ block: 'center', behavior: 'smooth' });
        }
        return false;
    }
    submitCallbackFormAjax(form);
    return false;
});

$(document).on('change', '#callback-consent', function() {
    $('#callback-consent-wrap .mf-consent-error').hide();
});

function submitFeedbackForm(form) {
    if (form._consentSubmitting) {
        return;
    }
    form._consentSubmitting = true;
    HTMLFormElement.prototype.submit.call(form);
}

$(document).on('submit', '.mfeedback form', function(e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    var form = this;
    var $form = $(form);
    ensureBitrixSessid($form);
    if (!validateConsentForm(form, '#feedback-consent', '#feedback-consent-wrap')) {
        return false;
    }
    submitFeedbackForm(form);
    return false;
});

$(document).on('change', '#feedback-consent', function() {
    $('#feedback-consent-wrap .mf-consent-error').hide();
});

function openCallbackPopup() {
    if (!$.fn.bPopup) {
        return;
    }
    var $el = $('#callback');
    if ($el.is(':visible')) {
        return;
    }
    $el.bPopup({ zIndex: 1000 });
}

function ensureCallbackPopupOpen() {
    openCallbackPopup();
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