<?php
if (IN_MANAGER_MODE !== true) {
    die('<h1>ERROR:</h1><p>Please use the MODx Content Manager instead of accessing this file directly.</p>');
}

// プレースホルダーの初期化
$modx->setPlaceholder('tv_head', '');
$modx->setPlaceholder('tv_body', '');

$DRT->fetchTmplVars();

if(! isset($_POST['select-tmpvars'])) {
    return;
}

// テンプレート変数のデータ格納　リソースIDをキーに設定
$tmplvarscontents = [];
foreach ($_POST['select-tmpvars'] as $post_t_id) {
    foreach ($resources as $key => $value) {
        $tmplvarscontents[$value['id']][] = $DRT->getTmplvarContent(intval($value['id']), intval($post_t_id));
    }
}

// THEADの生成
$h_row = array('id', 'type', 'pagetitle', 'published', 'parent_id', 'parent_or_child', 'template', 'alias', 'url');
foreach ($_POST['select-tmpvars'] as $value) {
    foreach ($DRT->tmplvars as $tmplvar) {
        if(intval($tmplvar['id']) === intval($value)) {
            $_name = $tmplvar['name'];
            break;
        }
    }
    $h_row[] = sprintf('[*%s*]', $_name);
}

$t_rows[] = $h_row;

foreach ($resources as $value) {
    $_row = array(
        $value['id']
        , $value['type']
        , $value['pagetitle']
        , $value['published']
        , $value['parent_id']
        , $value['parent_or_child']
        , $value['template']
        , $value['alias']
        , $value['url']
    );

    $_v = '';
    foreach ($tmplvarscontents[$value['id']] as $v) {
        $_v .= $v;
        $_row[] = $v;
    }

    // オプション　値のないリソースは出力しない
    if (isset($_POST['remove_null']) && $_POST['remove_null'] === '1' && empty($_v)) {
        continue;
    }

    $t_rows[] = $_row;
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=tv_data.csv');

$stream = fopen('php://output', 'w');
foreach($t_rows as $fields){
    fputcsv($stream, $fields);
}
exit();
