<?php
if (IN_MANAGER_MODE !== true) {
    die('<h1>ERROR:</h1><p>Please use the MODx Content Manager instead of accessing this file directly.</p>');
}

$_lang = array();
include(MODX_BASE_PATH . 'assets/modules/DetailedResourceTree/lang/english.php');
if (file_exists(MODX_BASE_PATH . 'assets/modules/DetailedResourceTree/lang/' . $modx->config['manager_language'] . '.php')) {
    include(MODX_BASE_PATH . 'assets/modules/DetailedResourceTree/lang/' . $modx->config['manager_language'] . '.php');
}

include MODX_MANAGER_PATH . 'includes/version.inc.php';
if( $modx_branch !== 'Evolution CMS') die('Error: A module for Evolution CMS.');

require_once(MODX_BASE_PATH . 'assets/modules/DetailedResourceTree/DetailedResourceTree.class.inc.php');

$DRT = new DetailedResourceTree($modx);

// CSV出力
if (isset($_POST['dl_pagelist_csv']) && $_POST['dl_pagelist_csv'] === '1') {
    include __DIR__ . '/resources.download_csv.php';
}
if (isset($_POST['dl_tv_csv']) && $_POST['dl_tv_csv'] === '1') {
    include __DIR__ . '/templvars.download_csv.php';
}

// HTML出力
include __DIR__ . '/resources.php';
include __DIR__ . '/templvars.php';

$html_head = $DRT->getTemplate('header.tpl');
$html_foot = $DRT->getTemplate('footer.tpl');
$html_body = $DRT->getTemplate('main.tpl');

$html_body = $modx->parseText($html_body, $modx->placeholders);

$lang = array();
foreach ($_lang as $key => $value) {
    $lang['lang.' . $key] = $value;
}
$html_body = $modx->parseText($html_body, $lang);

echo $html_head . PHP_EOL . $html_body . PHP_EOL . $html_foot;
