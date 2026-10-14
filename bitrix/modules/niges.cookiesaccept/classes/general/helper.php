<?php
/**
 * Helpers for niges.cookiesaccept: option whitelist, sanitization, cookie name.
 */
class CNigesCookiesAcceptHelper
{
	const MODULE_ID = 'niges.cookiesaccept';
	const COOKIE_PREFIX = 'NCA_COOKIE_ACCEPT_';

	/** @var string[] */
	private static $optionKeys = array(
		'TEXTVER',
		'MAINTEXT',
		'TEXTBTN',
		'PADDINGSIZE',
		'TOP',
		'TOPORBOTTOM',
		'FIXED',
		'POSITION',
		'ZINDEX',
		'NOINDEX',
		'HIDE_MOBILE',
		'HIDE_PC',
		'SETSTYLE',
		'ANIMATION',
		'BTNOPACITY',
		'BTNSHADOW',
		'BTNSHADOWCOLOR',
	);

	/** @var array */
	private static $defaults = array(
		'TEXTVER' => '1',
		'MAINTEXT' => '',
		'TEXTBTN' => '',
		'PADDINGSIZE' => '12',
		'TOP' => '0',
		'TOPORBOTTOM' => '2',
		'FIXED' => 'Y',
		'POSITION' => 'left',
		'ZINDEX' => '99999',
		'NOINDEX' => 'N',
		'HIDE_MOBILE' => 'N',
		'HIDE_PC' => 'N',
		'SETSTYLE' => '1',
		'ANIMATION' => 'none',
		'BTNOPACITY' => '100',
		'BTNSHADOW' => 'N',
		'BTNSHADOWCOLOR' => '#A8A8A8',
	);

	public static function getOptionKeys()
	{
		return self::$optionKeys;
	}

	public static function getDefaults()
	{
		return self::$defaults;
	}

	public static function isAllowedOption($name)
	{
		return in_array((string)$name, self::$optionKeys, true);
	}

	public static function getCookieName($textVer)
	{
		return self::COOKIE_PREFIX.(int)$textVer;
	}

	/**
	 * @param string $siteId
	 * @return array
	 */
	public static function loadSettings($siteId = SITE_ID)
	{
		$out = array();
		foreach (self::$defaults as $code => $default) {
			$raw = COption::GetOptionString(self::MODULE_ID, $code, $default, $siteId);
			$out[$code] = self::normalizeOption($code, $raw);
		}
		return $out;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return string
	 */
	public static function normalizeOption($name, $value)
	{
		$value = is_string($value) ? $value : (string)$value;

		switch ($name) {
			case 'MAINTEXT':
				return self::sanitizeHtml($value);

			case 'TEXTBTN':
				return self::sanitizePlainText($value, 120);

			case 'TEXTVER':
				$v = (int)$value;
				return (string)($v > 0 ? $v : 1);

			case 'PADDINGSIZE':
				$v = (int)$value;
				if ($v < 10) {
					$v = 10;
				}
				if ($v > 250) {
					$v = 250;
				}
				return (string)$v;

			case 'TOP':
				$v = (int)$value;
				if ($v < -100) {
					$v = -100;
				}
				if ($v > 100) {
					$v = 100;
				}
				return (string)$v;

			case 'ZINDEX':
				$v = (int)$value;
				if ($v < -1) {
					$v = -1;
				}
				if ($v > 9999999) {
					$v = 9999999;
				}
				return (string)$v;

			case 'BTNOPACITY':
				$v = (int)$value;
				if ($v < 0) {
					$v = 0;
				}
				if ($v > 100) {
					$v = 100;
				}
				return (string)$v;

			case 'TOPORBOTTOM':
				return ($value === '1') ? '1' : '2';

			case 'SETSTYLE':
				$v = (int)$value;
				if ($v < 1 || $v > 20) {
					$v = 1;
				}
				return (string)$v;

			case 'ANIMATION':
				$allowed = array('none', 'shake', 'shift');
				return in_array($value, $allowed, true) ? $value : 'none';

			case 'POSITION':
				$allowed = array('left', 'right', 'center');
				return in_array($value, $allowed, true) ? $value : 'left';

			case 'FIXED':
			case 'NOINDEX':
			case 'HIDE_MOBILE':
			case 'HIDE_PC':
			case 'BTNSHADOW':
				return ($value === 'Y') ? 'Y' : 'N';

			case 'BTNSHADOWCOLOR':
				return self::sanitizeHexColor($value, '#A8A8A8');

			default:
				return self::sanitizePlainText($value, 500);
		}
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return string
	 */
	public static function prepareForStorage($name, $value)
	{
		if ($name === 'MAINTEXT') {
			return self::sanitizeHtml(is_string($value) ? $value : '');
		}
		return (string)self::normalizeOption($name, $value);
	}

	public static function sanitizePlainText($value, $maxLen = 255)
	{
		$value = trim(strip_tags((string)$value));
		if (function_exists('mb_substr')) {
			$value = mb_substr($value, 0, $maxLen);
		} else {
			$value = substr($value, 0, $maxLen);
		}
		return $value;
	}

	public static function sanitizeHexColor($value, $fallback = '#000000')
	{
		$value = trim((string)$value);
		if (preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $value)) {
			return strtoupper($value);
		}
		return $fallback;
	}

	/**
	 * @param string $html
	 * @return string
	 */
	public static function sanitizeHtml($html)
	{
		$html = (string)$html;
		if ($html === '') {
			return '';
		}

		if (class_exists('CBXSanitizer')) {
			$sanitizer = new CBXSanitizer();
			$sanitizer->AddTags(array(
				'a' => array('href', 'title', 'target', 'rel'),
				'b' => array(),
				'strong' => array(),
				'i' => array(),
				'em' => array(),
				'u' => array(),
				'br' => array(),
				'p' => array(),
				'span' => array(),
			));
			$html = $sanitizer->SanitizeHtml($html);
		} else {
			$html = strip_tags($html, '<a><b><strong><i><em><u><br><p><span>');
		}

		$html = preg_replace_callback(
			'/<a\s+([^>]+)>/i',
			array(__CLASS__, 'sanitizeAnchorAttributes'),
			$html
		);

		return $html;
	}

	/**
	 * @param array $m
	 * @return string
	 */
	public static function sanitizeAnchorAttributes($m)
	{
		$attrs = $m[1];
		$href = '#';
		$target = '';
		$title = '';

		if (preg_match('/href\s*=\s*(["\'])(.*?)\1/is', $attrs, $hm)) {
			$href = self::sanitizeHref($hm[2]);
		} elseif (preg_match('/href\s*=\s*([^\s>]+)/i', $attrs, $hm)) {
			$href = self::sanitizeHref($hm[1]);
		}

		if (preg_match('/target\s*=\s*(["\'])(.*?)\1/i', $attrs, $tm)) {
			if (strtolower($tm[2]) === '_blank') {
				$target = ' target="_blank"';
			}
		}

		if (preg_match('/title\s*=\s*(["\'])(.*?)\1/is', $attrs, $tt)) {
			$title = ' title="'.htmlspecialcharsbx($tt[2]).'"';
		}

		$rel = ($target !== '') ? ' rel="noopener noreferrer"' : '';

		return '<a href="'.htmlspecialcharsbx($href).'"'.$title.$target.$rel.'>';
	}

	public static function sanitizeHref($href)
	{
		$charset = defined('SITE_CHARSET') ? SITE_CHARSET : 'UTF-8';
		$href = trim(html_entity_decode((string)$href, ENT_QUOTES, $charset));
		if ($href === '') {
			return '#';
		}
		if (preg_match('/^\s*(javascript|data|vbscript)\s*:/i', $href)) {
			return '#';
		}
		if (
			preg_match('/^(https?:\/\/|mailto:|\/|#|\.\/|\.\.\/)/i', $href)
			|| preg_match('/^[a-zA-Z0-9_\-\.\/\?&=%#+]+$/', $href)
		) {
			return $href;
		}
		return '#';
	}

	/**
	 * @param string $siteId
	 * @param array $arSites
	 * @return string|null
	 */
	public static function resolveSiteId($siteId, array $arSites)
	{
		$siteId = (string)$siteId;
		if ($siteId !== '' && isset($arSites[$siteId])) {
			return $siteId;
		}
		return null;
	}
}
