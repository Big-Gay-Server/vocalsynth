<?php
// this site's root
define('BASE_PATH', realpath(__DIR__) . '/');
define('BASE_DOMAIN', "lunarconstruct.net");

// The root of all sites (e.g., /usr/share/nginx/html/)
define('GLOBAL_ROOT', realpath(__DIR__ . '/../') . '/');

// Specific shortcuts for your shared libraries
define('VOCALSYNTH_PATH', GLOBAL_ROOT . 'vocalsynth/');
define('VOCALSYNTH_DOMAIN', 'https://vocalsynth.' . BASE_DOMAIN);
define('LUNATINE_PATH', GLOBAL_ROOT . 'lunatine/');