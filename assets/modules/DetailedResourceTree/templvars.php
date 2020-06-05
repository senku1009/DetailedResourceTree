<?php
if (IN_MANAGER_MODE !== true) {
    die('<h1>ERROR:</h1><p>Please use the MODx Content Manager instead of accessing this file directly.</p>');
}

// プレースホルダーの初期化
$modx->setPlaceholder('tv_head', '');
$modx->setPlaceholder('tv_body', '');

$DRT->fetchTmplVars();

$checkbox_tpl = '
    <tr>
        <td>
            <div class="custom-control custom-switch">
                <input class="custom-control-input" type="checkbox" name="select-tmpvars[]" value="%d" id="%s"%s>
                <label class="custom-control-label" for="%s">
                    %s
                </label>
            </div>
        </td>
        <td>%s</td>
        <td>%s</td>
    </tr>' . PHP_EOL;

foreach ($DRT->tmplvars as $tmplvar) {
    $checked_element = '';
    if(isset($_POST['select-tmpvars']) && in_array($tmplvar['id'], $_POST['select-tmpvars'])) {
        $checked_element = ' checked="checked"';
    }

    $checkbox_html .= sprintf(
        $checkbox_tpl,
        $tmplvar['id'],
        $tmplvar['name'],
        $checked_element,
        $tmplvar['name'],
        $tmplvar['name'],
        $tmplvar['caption'],
        $tmplvar['description']
    );
}
$modx->setPlaceholder('tmplvar_checkbox', $checkbox_html);

$modx->setPlaceholder('checked_remove_null', '');
if (isset($_POST['remove_null']) && $_POST['remove_null'] === '1') {
    $modx->setPlaceholder('checked_remove_null', ' checked="checked"');
}

if(! isset($_POST['select-tmpvars'])) {
    return;
}

// テンプレート変数のデータ格納　リソースIDをキーに設定
$tmplvarscontents = [];
foreach ($_POST['select-tmpvars'] as $post_t_id) {
    foreach ($resources as $key => $value) {
        $_ = $DRT->getTmplvarContent(intval($value['id']), intval($post_t_id));
        $_ = htmlspecialchars($_, ENT_QUOTES);
        $tmplvarscontents[$value['id']][] = nl2br($_);
    }
}

// THEADの生成
$h_row = <<< HTM
<tr>
    <th>[+lang.id+]</th>
    <th>[+lang.type+]</th>
    <th>[+lang.pagetitle+]</th>
    <th>[+lang.published+]</th>
    <th>[+lang.parent_id+]</th>
    <th>[+lang.parent_or_child+]</th>
    <th>[+lang.template+]</th>
    <th>[+lang.alias+]</th>
    <th>[+lang.url+]</th>
HTM;

foreach ($_POST['select-tmpvars'] as $value) {
    foreach ($DRT->tmplvars as $tmplvar) {
        if(intval($tmplvar['id']) === intval($value)) {
            $_name = $tmplvar['name'];
            break;
        }
    }
    $h_row .= sprintf('    <th>[*%s*]</th>' . PHP_EOL, $_name);
}
$h_row .= '</tr>' . PHP_EOL;
$modx->setPlaceholder('tv_head', $h_row);
unset($h_row);

// TBODYの生成
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
HTM;

$t_rows = '';

foreach ($resources as $value) {
    $tr_class = '';
    if ($value['parent_or_child'] === '[+lang.parent+]'){
        $tr_class = 'table-primary';
    }

    $_row = sprintf($t_row
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

    $_v = '';
    foreach ($tmplvarscontents[$value['id']] as $v) {
        $_v .= $v;
        $_row .= '<td>' . $v . '</td>' . PHP_EOL;
    }
    $_row .= '</tr>' . PHP_EOL;

    // オプション　値のないリソースは出力しない
    if (isset($_POST['remove_null']) && $_POST['remove_null'] === '1' && empty($_v)) {
        continue;
    }

    $t_rows .= $_row;
}
$modx->setPlaceholder('tv_body', $t_rows);
unset($t_rows);
