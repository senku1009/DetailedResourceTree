<?php
if (IN_MANAGER_MODE !== true) {
    die('<h1>ERROR:</h1><p>Please use the MODx Content Manager instead of accessing this file directly.</p>');
}

$contents_count = $DRT->getSiteContentAI();

$DRT->tamplateNameList();

$output_array = [];
for ($i = 0; $i < $contents_count; $i++) {
    $row = $DRT->selectResource($i);
    // データが存在しない場合は処理を飛ばす
    if (empty($row)) {
        continue;
    }

    $output_array[] = [
        'id' => $row['id'],
        'type' => $row['type'],
        'alias' => $row['alias'],
        'pagetitle' => $row['pagetitle'],
        'published' => ($row['published'] === '1')? 'yes': 'no',
        'parent_id' => $row['parent'],
        'parent_or_child' => 'child',
        'template' => $DRT->template_list[$row['template']],
        'url' => $DRT->makeURL($i, $modx->config['site_url'])
    ];
}


// 親に親ステータスを付与
$id_array = [];
foreach ($output_array as $key => $value) {
    $id_array[$key] = $value['id'];
}
foreach ($output_array as $key => $value) {
    if($value['parent_id'] === 0) {
        continue;
    }
    $_key = array_search($value['parent_id'], $id_array);
    $output_array[$_key]['parent_or_child'] = 'parent';
}

// ドキュメントルート直下の親だけを抽出
$seed_parent = [];
foreach ($output_array as $key => $value) {
    if($value['parent_id'] === '0') {
        $seed_parent[] = $value;
    }
}

$resources = [];
foreach ($seed_parent as $key => $value) {
    $value['flag'] = 1;
    $resources[] = $value;
    // 子を探す再帰関数
    $_childs = $DRT->searchChild($value['id'], $output_array);
    foreach ($_childs as $value) {
        $resources[] = $value;
    }
}
unset($seed_parent, $output_array);

// @todo    ドキュメントルート直下をsingleに属性変更

$h_row = array('id', 'type', 'pagetitle', 'published', 'parent_id', 'parent_or_child', 'template', 'alias', 'url');

$t_rows[] = $h_row;

foreach ($resources as $value) {
    $t_rows[] = array(
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
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=resources_data.csv');

$stream = fopen('php://output', 'w');
foreach($t_rows as $fields){
    fputcsv($stream, $fields);
}
exit();