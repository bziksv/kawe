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

		var wrap = document.createElement('div');
		wrap.innerHTML =
			'<div id="nca-cookiesaccept-line"'
			+ ' class="' + String(cfg.className).replace(/"/g, '&quot;') + '"'
			+ ' style="' + String(cfg.styleVars).replace(/"/g, '&quot;') + '"'
			+ ' data-cookie-name="' + String(cfg.cookieName).replace(/"/g, '&quot;') + '"'
			+ ' data-textver="' + String(cfg.textVer) + '"'
			+ ' role="dialog"'
			+ ' aria-live="polite"'
			+ '>'
			+ '<div class="nca-bar" id="nca-bar">'
			+ '<div class="nca-cookiesaccept-line-text">' + cfg.mainText + '</div>'
			+ '<div class="nca-cookiesaccept-line-actions">'
			+ '<button type="button" id="nca-cookiesaccept-line-accept-btn" class="nca-accept-btn"></button>'
			+ '</div></div></div>';

		var root = wrap.firstElementChild;
		if (!root) {
			return;
		}

		var btn = root.querySelector('#nca-cookiesaccept-line-accept-btn');
		if (btn) {
			btn.textContent = cfg.btnText || '';
		}

		document.body.appendChild(root);

		function hideBanner() {
			if (root && root.parentNode) {
				root.parentNode.removeChild(root);
			}
		}

		if (btn) {
			btn.addEventListener('click', function () {
				writeAcceptCookie(cfg.cookieName);
				hideBanner();
			});
		}
	}

	// Epilog часто после </html>: DOMContentLoaded уже прошёл — body обычно уже есть.
	if (document.body) {
		boot();
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
</script>
