<?php
define('cookiesaccept_MODULE_ID', 'niges.cookiesaccept');

CModule::AddAutoloadClasses(cookiesaccept_MODULE_ID, array(
	'CNigesCookiesAcceptPublic' => 'classes/general/public.php',
	'CNigesCookiesAcceptHelper' => 'classes/general/helper.php',
));
