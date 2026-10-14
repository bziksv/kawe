<?php
IncludeModuleLangFile(__FILE__);

if (!$USER->IsAdmin()) {
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
}

if (!CModule::IncludeModule('niges.cookiesaccept')) {
	ShowError(GetMessage('Oshibka_ustanovki_modulya'));
	return;
}

$APPLICATION->SetTitle(GetMessage('Nastrojka_resheniya'));

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php';

$moduleId = CNigesCookiesAcceptHelper::MODULE_ID;
$arSites = array();
$l = CLang::GetList($by = 'sort', $order = 'asc');
while ($row = $l->Fetch()) {
	$arSites[$row['LID']] = $row;
}

$siteId = null;
$requestSiteId = isset($_REQUEST['SITE_ID']) ? (string)$_REQUEST['SITE_ID'] : '';

if (count($arSites) === 1) {
	$only = reset($arSites);
	$siteId = $only['LID'];
} elseif ($requestSiteId !== '') {
	$siteId = CNigesCookiesAcceptHelper::resolveSiteId($requestSiteId, $arSites);
}

$saveOk = false;
$saveError = '';

if (
	$siteId
	&& isset($_REQUEST['set_cookiesaccept_props'])
	&& $_SERVER['REQUEST_METHOD'] === 'POST'
) {
	if (!check_bitrix_sessid()) {
		$saveError = GetMessage('NCA_SESSID_ERROR') ?: 'Ошибка проверки сессии. Обновите страницу и попробуйте снова.';
	} else {
		$active = (isset($_REQUEST['ACTIVE']) && $_REQUEST['ACTIVE'] === 'Y') ? 'Y' : 'N';
		COption::SetOptionString($moduleId, 'ACTIVE', $active, false, $siteId);

		$props = array();
		if (isset($_REQUEST['NCA_PROP']) && is_array($_REQUEST['NCA_PROP'])) {
			$props = $_REQUEST['NCA_PROP'];
		}

		// Checkboxes absent from POST => N
		foreach (array('HIDE_MOBILE', 'HIDE_PC', 'FIXED', 'NOINDEX', 'BTNSHADOW') as $flag) {
			if (!isset($props[$flag])) {
				$props[$flag] = 'N';
			}
		}

		foreach ($props as $name => $value) {
			$name = (string)$name;
			if (!CNigesCookiesAcceptHelper::isAllowedOption($name)) {
				continue;
			}
			$stored = CNigesCookiesAcceptHelper::prepareForStorage($name, $value);
			COption::SetOptionString($moduleId, $name, $stored, false, $siteId);
		}

		$saveOk = true;
	}
}

$settings = $siteId ? CNigesCookiesAcceptHelper::loadSettings($siteId) : CNigesCookiesAcceptHelper::getDefaults();
$isActive = $siteId
	? (COption::GetOptionString($moduleId, 'ACTIVE', 'N', $siteId) === 'Y')
	: false;

// Defaults for empty content fields (display only)
if ($siteId) {
	if ($settings['MAINTEXT'] === '') {
		$settings['MAINTEXT'] = GetMessage('main_text_ex');
	}
	if ($settings['TEXTBTN'] === '') {
		$settings['TEXTBTN'] = GetMessage('Osn_text_btn_val');
	}
}
?>
<div class="adm-detail-content-wrap">
	<?php if ($saveOk): ?>
		<div class="adm-info-message-wrap adm-info-message-green">
			<div class="adm-info-message"><?=htmlspecialcharsbx(GetMessage('NCA_SAVED') ?: 'Настройки сохранены')?></div>
		</div>
	<?php endif; ?>
	<?php if ($saveError !== ''): ?>
		<div class="adm-info-message-wrap adm-info-message-red">
			<div class="adm-info-message"><?=htmlspecialcharsbx($saveError)?></div>
		</div>
	<?php endif; ?>

	<form method="post" action="">
		<?=bitrix_sessid_post()?>
		<div class="adm-detail-content" id="edit1">
			<div class="adm-detail-title"><?=htmlspecialcharsbx(GetMessage('Nastrojka_resheniya'))?></div>
			<div class="adm-detail-content-item-block">
				<table class="adm-detail-content-table edit-table" id="edit1_edit_table">
					<tbody>
					<?php if (!$siteId): ?>
						<tr>
							<td width="50%" class="adm-detail-content-cell-l">
								<?=htmlspecialcharsbx(GetMessage('Vyberite_sajt'))?>
							</td>
							<td width="50%" class="adm-detail-content-cell-r">
								<select name="SITE_ID">
									<?php foreach ($arSites as $lid => $arVal): ?>
										<option value="<?=htmlspecialcharsbx($arVal['LID'])?>">
											[<?=htmlspecialcharsbx($arVal['LID'])?>] <?=htmlspecialcharsbx($arVal['NAME'])?>
										</option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div class="adm-info-message-wrap">
									<div class="adm-info-message"><?=htmlspecialcharsbx(GetMessage('Najmite_sohranit'))?></div>
								</div>
							</td>
						</tr>
					<?php else: ?>
						<tr>
							<td width="50%" class="adm-detail-content-cell-l"><?=htmlspecialcharsbx(GetMessage('Sajt'))?></td>
							<td width="50%" class="adm-detail-content-cell-r">
								<input type="hidden" name="SITE_ID" value="<?=htmlspecialcharsbx($siteId)?>">
								<b>[<?=htmlspecialcharsbx($siteId)?>] <?=htmlspecialcharsbx($arSites[$siteId]['NAME'])?></b>
							</td>
						</tr>
						<tr>
							<td width="50%" class="adm-detail-content-cell-l"><?=htmlspecialcharsbx(GetMessage('Aktivnost'))?></td>
							<td width="50%" class="adm-detail-content-cell-r">
								<input type="checkbox" name="ACTIVE" value="Y"<?=$isActive ? ' checked' : ''?>>
							</td>
						</tr>

						<tr class="heading"><td colspan="2"><?=htmlspecialcharsbx(GetMessage('Soderzh'))?></td></tr>

						<tr>
							<td class="adm-detail-content-cell-l"><?=htmlspecialcharsbx(GetMessage('Osn_text_ver'))?></td>
							<td class="adm-detail-content-cell-r">
								<input type="number" min="1" max="999999" name="NCA_PROP[TEXTVER]" value="<?=htmlspecialcharsbx($settings['TEXTVER'])?>">
							</td>
						</tr>
						<tr>
							<td class="adm-detail-content-cell-l"><?=htmlspecialcharsbx(GetMessage('Osn_text'))?></td>
							<td class="adm-detail-content-cell-r">
								<textarea rows="5" cols="100" name="NCA_PROP[MAINTEXT]"><?=htmlspecialcharsbx($settings['MAINTEXT'])?></textarea>
							</td>
						</tr>
						<tr>
							<td class="adm-detail-content-cell-l"><?=htmlspecialcharsbx(GetMessage('Osn_text_btn'))?></td>
							<td class="adm-detail-content-cell-r">
								<input type="text" maxlength="120" name="NCA_PROP[TEXTBTN]" value="<?=htmlspecialcharsbx($settings['TEXTBTN'])?>">
							</td>
						</tr>

						<tr class="heading"><td colspan="2"><?=htmlspecialcharsbx(GetMessage('Obschie_nastrojki'))?></td></tr>

						<tr>
							<td class="adm-detail-content-cell-l"><?=htmlspecialcharsbx(GetMessage('Polozhenie'))?></td>
							<td class="adm-detail-content-cell-r">
								<label style="display:block;margin:5px 0;">
									<input type="radio" value="1" name="NCA_PROP[TOPORBOTTOM]"<?=$settings['TOPORBOTTOM'] === '1' ? ' checked' : ''?>>
									<?=htmlspecialcharsbx(GetMessage('Polozhenie_sv'))?>
								</label>
								<label style="display:block;margin:5px 0;">
									<input type="radio" value="2" name="NCA_PROP[TOPORBOTTOM]"<?=$settings['TOPORBOTTOM'] !== '1' ? ' checked' : ''?>>
									<?=htmlspecialcharsbx(GetMessage('Polozhenie_sn'))?>
								</label>
							</td>
						</tr>
						<tr>
							<td class="adm-detail-content-cell-l"><?=htmlspecialcharsbx(GetMessage('Zindex'))?></td>
							<td class="adm-detail-content-cell-r">
								<input type="number" min="-1" max="9999999" name="NCA_PROP[ZINDEX]" value="<?=htmlspecialcharsbx($settings['ZINDEX'])?>">
							</td>
						</tr>

						<tr class="heading"><td colspan="2"><?=htmlspecialcharsbx(GetMessage('Nastrojki_vneshnego_vida'))?></td></tr>

						<tr>
							<td class="adm-detail-content-cell-l"><?=htmlspecialcharsbx(GetMessage('Style'))?></td>
							<td class="adm-detail-content-cell-r">
								<?php for ($i = 1; $i <= 8; $i++): ?>
									<label style="margin-top:15px;display:block;margin-right:15px;">
										<img
											style="height:62px;border:1px solid #a9a9a9;border-radius:4px;"
											src="/bitrix/components/niges/cookiesaccept/templates/.default/images/style-option-<?=$i?>.png"
											alt=""
										>
										<br>
										<input type="radio" value="<?=$i?>" name="NCA_PROP[SETSTYLE]"<?=((int)$settings['SETSTYLE'] === $i || ((int)$settings['SETSTYLE'] < 1 && $i === 1)) ? ' checked' : ''?>>
										<?=htmlspecialcharsbx(GetMessage('Style_'.$i))?>
									</label>
								<?php endfor; ?>
							</td>
						</tr>
						<tr>
							<td class="adm-detail-content-cell-l"><?=htmlspecialcharsbx(GetMessage('Razmer'))?></td>
							<td class="adm-detail-content-cell-r">
								<input type="number" min="10" max="250" name="NCA_PROP[PADDINGSIZE]" value="<?=htmlspecialcharsbx($settings['PADDINGSIZE'])?>">
							</td>
						</tr>
						<tr>
							<td class="adm-detail-content-cell-l"><?=htmlspecialcharsbx(GetMessage('Prozrachnost'))?></td>
							<td class="adm-detail-content-cell-r">
								<input type="number" min="0" max="100" name="NCA_PROP[BTNOPACITY]" value="<?=htmlspecialcharsbx($settings['BTNOPACITY'])?>">
							</td>
						</tr>
					<?php endif; ?>
					</tbody>
				</table>

				<div style="padding:20px 0 0;line-height:1.5;text-align:center;margin-top:25px;border-top:1px solid #e2e8ea;">
					<?=GetMessage('Bolshe_vozm')?>
					<a href="/bitrix/admin/update_system_market.php?module=niges.cookiesacceptpro&amp;lang=ru" target="_blank" rel="noopener noreferrer">
						<?=GetMessage('Bolshe_vozm_anch')?>
					</a>
				</div>
			</div>
		</div>
		<div class="adm-detail-content-btns">
			<?php if ($siteId): ?>
				<input class="adm-btn-save" type="submit" name="set_cookiesaccept_props" value="<?=htmlspecialcharsbx(GetMessage('Sohranit'))?>">
				<input type="button" value="<?=htmlspecialcharsbx(GetMessage('Otmenit'))?>" name="cancel"
					onclick="window.location.href='/bitrix/admin/settings.php?lang=ru&amp;mid=niges.cookiesaccept'">
			<?php else: ?>
				<input class="adm-btn-save" type="submit" name="set_site" value="<?=htmlspecialcharsbx(GetMessage('Vibrat'))?>">
			<?php endif; ?>
		</div>
	</form>
</div>
