<?
define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC","Y");
define("NOT_CHECK_PERMISSIONS", true);

$HTTP_ACCEPT_ENCODING = "";
$_SERVER["HTTP_ACCEPT_ENCODING"] = "";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$cpt = new CCaptcha();

// Readable digits: wider canvas, absolute X step ~ glyph width, no noise/wave.
$cpt->SetImageSize(240, 50);
$cpt->SetTextWriting(0, 0, 14, 42, 48, 20);
$cpt->SetCodeChars(array('2','3','4','5','6','7','8','9'));
$cpt->SetWaveTransformation(false);
$cpt->SetLinesNumber(0);
$cpt->SetEllipsesNumber(0);
$cpt->SetLinesOverText(false);

if (isset($_GET["captcha_sid"]))
{
	if ($cpt->InitCode($_GET["captcha_sid"]))
		$cpt->Output();
	else
		$cpt->OutputError();
}
elseif (isset($_GET["captcha_code"]))
{
	$captchaPass = COption::GetOptionString("main", "captcha_password", "");

	if ($cpt->InitCodeCrypt($_GET["captcha_code"], $captchaPass))
		$cpt->Output();
	else
		$cpt->OutputError();
}
else
{
	$cpt->OutputError();
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
