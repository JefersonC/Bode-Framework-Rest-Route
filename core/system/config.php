<?php

    define('DIR', $_SERVER['DOCUMENT_ROOT'] . '/sync/');
    define('URL_MAIN', 'localhost/Compiladeiro/');
    define('DIR_CORE', DIR . 'core/');
    define('DIR_CONTROLLERS', DIR . 'core/controllers/');
    define('DIR_VIEWS', DIR . 'core/views/includes/');
    define('DIR_ASSETS', URL_MAIN . 'core/assets/');

    require_once(DIR_CORE . "utils/functions.php");
    require_once(DIR_CORE . "system/autoload.php");
    require_once(DIR_CORE . "system/route.php");
