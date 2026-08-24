<? if(!CSite::InDir('/index.php')): ?>
</div>
<?endif;?>

<div class="footer">
    <div class="container">
        <div class="footer__row">
            <div class="footer__col">

                <?$APPLICATION->IncludeComponent("bitrix:menu", "bottom.menu", Array(
                    "ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
                    "CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
                    "DELAY" => "N",	// Откладывать выполнение шаблона меню
                    "MAX_LEVEL" => "2",	// Уровень вложенности меню
                    "MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
                    "MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
                    "MENU_CACHE_TYPE" => "N",	// Тип кеширования
                    "MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
                    "ROOT_MENU_TYPE" => "bottom",	// Тип меню для первого уровня
                    "USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
                    "COMPONENT_TEMPLATE" => "catalog_horizontal",
                    "MENU_THEME" => "site",	// Тема меню
                ),
                    false
                );?>

            </div>
            <div class="footer__col flex-3">
                <div class="footer__phone icon-phone">
                    <img src="<?=SITE_TEMPLATE_PATH?>/img/footer_phone.jpg">
                    <span>88005555550</span>
                </div>
                <a href="javascript:void(0);" class="footer__callback footer__link callback-btn">Заказать звонок</a>
                <div class="footer__email icon-email">
                    <a href="mailto:<?=tplvar('email');?>" class="footer__link roi_visit"><?=tplvar('email', true);?></a>
                    (Для заказов)
                </div>
            </div>
            <div class="footer__col flex-2">
                <div class="footer__copy icon-copyright"><?=date("Y")?> Все права защищены.</div>
<br>



<a href="https://prime-ltd.su/?from=kawe.su" target="_blank" style="position: relative; right: 11px;">
  <img src="https://prime-ltd.su/logo/white.svg" style="width: 60%; height: auto;" title="Продвижение сайтов" alt="Продвижение сайтов">
</a>
            </div>
        </div>




		<div><noindex>
Наш сайт использует <a target="_blank" style="color: white; text-decoration: underline" href="/upload/legal/legal-cookie.png">cookies</a> для обеспечения работоспособности и сбора статистики. С их помощью мы анализируем пользовательскую активность, улучшаем работу сайта и делаем рекламу более релевантной. Оставаясь на сайте, вы даёте согласие на обработку ваших персональных данных в соответствии с <a target="_blank" style="color: white; text-decoration: underline" href="/upload/legal/legal-consent.png">Согласием на обработку персональных данных</a>. Подробнее об обработке персональных данных — в <a target="_blank" style="color: white; text-decoration: underline" href="/upload/legal/legal-personal-data.png">Политике обработки персональных данных</a>. Вы можете отключить сохранение cookies в настройках браузера в любой момент. На сайте также применяются <a target="_blank" style="color: white; text-decoration: underline" href="/upload/legal/legal-recommendation.png">рекомендательные технологии</a>.
</noindex></div>
    </div>
</div>






<?
$APPLICATION->IncludeComponent(
	"nbrains:main.feedback", 
	"popup-callback", 
	array(
		"EMAIL_TO" => "info@kawe.su",
		"EVENT_MESSAGE_ID" => array(
			0 => "53",
		),
		"ROI_VISIT" => $_COOKIE['roistat_visit'],
		"IBLOCK_ID" => "37",
		"IBLOCK_TYPE" => "feedback",
		"OK_TEXT" => "Спасибо, ваше сообщение принято.",
		"PROPERTY_CODE" => array(
			0 => "NAME",
			1 => "URL",
			2 => "PHONE",
			3 => "MAIL",
			4 => "QUERY",
		),
		"USE_CAPTCHA" => "N",
		"COMPONENT_TEMPLATE" => "popup-callback",
		"COMPOSITE_FRAME_MODE" => "N",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>




<script src="<?=SITE_TEMPLATE_PATH?>/js/main.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/alertify.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.maskinput.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.bpopup.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/lightgallery.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/vendor/jquery-ui/jquery-ui.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/functions.js"></script>


<!-- Yandex.Metrika counter от Prime -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(48001034, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        trackHash:true,
        ecommerce:"dataLayer"
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/48001034" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->




</body>
</html>