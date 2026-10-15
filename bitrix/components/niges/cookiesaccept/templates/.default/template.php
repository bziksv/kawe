<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

/** @var CBitrixComponentTemplate $this */
/** @var array $arResult */
/** @var CMain $APPLICATION */

if (method_exists($this, 'setFrameMode')) {
	$this->setFrameMode(true);
}

$this->addExternalCss($this->GetFolder().'/style.css');

$cookieName = (string)$arResult['COOKIE_NAME'];
$textVer = (int)$arResult['TEXTVER'];
$padding = (int)$arResult['PADDINGSIZE'];
$opacity = (string)$arResult['OPACITY'];
$zIndex = (int)$arResult['ZINDEX'];
$topOffset = (int)$arResult['TOP'];
$styleId = (int)$arResult['SETSTYLE'];
$position = (string)$arResult['POSITION'];
$animation = (string)$arResult['ANIMATION'];
$btnText = (string)$arResult['TEXTBTN'];
// MAINTEXT already sanitized in helper — safe subset of HTML
$mainText = (string)$arResult['MAINTEXT'];

$classes = array(
	'nca-cookiesaccept-line',
	'style-'.$styleId,
	'nca-position-'.$position,
	'nca-animation-'.$animation,
);
if ($arResult['HIDE_MOBILE'] === 'Y') {
	$classes[] = 'nca-hidden-mobile';
}
if ($arResult['HIDE_PC'] === 'Y') {
	$classes[] = 'nca-hidden-pc';
}
if ($arResult['TOPORBOTTOM'] === '1') {
	$classes[] = 'nca-place-top';
} else {
	$classes[] = 'nca-place-bottom';
}

$styleVars = sprintf(
	'--nca-padding:%dpx;--nca-opacity:%s;--nca-zindex:%d;--nca-top:%d%%;',
	$padding,
	$opacity,
	$zIndex,
	$topOffset
);

// Не печатаем <div> в поток (OnEpilog после </html> → stray div).
// Собираем баннер в JS и вешаем на document.body (как на stelinvest.ru).
$config = array(
	'cookieName' => $cookieName,
	'textVer' => $textVer,
	'className' => implode(' ', $classes),
	'styleVars' => $styleVars,
	'mainText' => $mainText,
	'btnText' => $btnText,
);
$configJson = \Bitrix\Main\Web\Json::encode($config);
?>
<script>
(function () {
	var cfg = <?=$configJson?>;

	function boot() {
		if (window !== window.top) {
			return;
		}
		if (!cfg || !cfg.cookieName || !document.body) {
			return;
		}
		if (document.getElementById('nca-cookiesaccept-line')) {
			return;
		}

		function readCookie(name) {
			var parts = ('; ' + document.cookie).split('; ' + name + '=');
			if (parts.length === 2) {
				return parts.pop().split(';').shift();
			}
			return '';
		}

		function writeAcceptCookie(name) {
			var maxAge = 60 * 60 * 24 * 400; // ~400 days (browser caps)
			var parts = [
				name + '=Y',
				'path=/',
				'max-age=' + maxAge,
				'SameSite=Lax'
			];
			if (window.location.protocol === 'https:') {
				parts.push('Secure');
			}
			document.cookie = parts.join('; ');
		}

		if (readCookie(cfg.cookieName) === 'Y') {
			return;
		}

		// Build via DOM API so source has no HTML end-tag literals inside script.
		var root = document.createElement('div');
		root.id = 'nca-cookiesaccept-line';
		root.className = String(cfg.className || '');
		root.setAttribute('style', String(cfg.styleVars || ''));
		root.setAttribute('data-cookie-name', String(cfg.cookieName || ''));
		root.setAttribute('data-textver', String(cfg.textVer));
		root.setAttribute('role', 'dialog');
		root.setAttribute('aria-live', 'polite');

		var bar = document.createElement('div');
		bar.className = 'nca-bar';
		bar.id = 'nca-bar';

		var text = document.createElement('div');
		text.className = 'nca-cookiesaccept-line-text';
		text.innerHTML = cfg.mainText || '';

		var actions = document.createElement('div');
		actions.className = 'nca-cookiesaccept-line-actions';

		var btn = document.createElement('button');
		btn.type = 'button';
		btn.id = 'nca-cookiesaccept-line-accept-btn';
		btn.className = 'nca-accept-btn';
		btn.textContent = cfg.btnText || '';

		actions.appendChild(btn);
		bar.appendChild(text);
		bar.appendChild(actions);
		root.appendChild(bar);
		document.body.appendChild(root);

		function hideBanner() {
			if (root && root.parentNode) {
				root.parentNode.removeChild(root);
			}
		}

		btn.addEventListener('click', function () {
			writeAcceptCookie(cfg.cookieName);
			hideBanner();
		});
	}

	// Epilog часто после закрытия документа: DOMContentLoaded уже прошёл — body обычно уже есть.
	if (document.body) {
		boot();
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
</script>
