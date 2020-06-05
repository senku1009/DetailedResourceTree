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
        'published' => ($row['published'] === '1')? '[+lang.yes+]': '[+lang.no+]',
        'parent_id' => $row['parent'],
        'parent_or_child' => '[+lang.child+]',
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
    $output_array[$_key]['parent_or_child'] = '[+lang.parent+]';
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

$t_row = <<< HTM
<tr class="%s">
    <td><a href="/manager/index.php?a=27&id=%s">%s</a></td>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
</tr>
HTM;

foreach ($resources as $value) {
    $tr_class = '';
    if ($value['parent_or_child'] === '[+lang.parent+]'){
        $tr_class = 'table-primary';
    }
    $t_rows .= sprintf($t_row
        , $tr_class
        , $value['id']
        , $value['id']
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
$modx->setPlaceholder("tbody", $t_rows);
unset($t_rows);