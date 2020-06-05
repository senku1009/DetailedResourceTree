<?php
class DetailedResourceTree {
    private $msg = [];
    private $post_data = [];
    private $modx;
    private $db = [];
    public $template_list = [];
    public $tmplvars = [];

    function __construct(&$modx)
    {
        $this->msg['ErrorMessage'] = '';

        $this->modx = &$modx;
        $this->db['prefix'] = $this->modx->db->config['prefix'];
        $this->db['name'] = $this->modx->db->config['database'];
    }

    public function getTmplvarContent($r_id, $t_id) :string
    {
        $sql = "SELECT value
            FROM `{$this->db['name']}`.`{$this->db['prefix']}site_tmplvar_contentvalues`
            WHERE contentid = {$r_id}
            AND `tmplvarid` = {$t_id}
            ORDER BY `{$this->db['prefix']}site_tmplvar_contentvalues`.`tmplvarid` ASC;";
        $rs = $this->modx->db->query($sql);
        $rs = $this->modx->db->makeArray($rs);
        if ($rs[0]['value'] === null) {
            return '';
        }
        return $rs[0]['value'];
    }

    public function fetchTmplVars() :void
    {
        $sql = "SELECT *
            FROM `{$this->db['name']}`.`{$this->db['prefix']}site_tmplvars`;";
        $rs = $this->modx->db->query($sql);
        $this->tmplvars = $this->modx->db->makeArray($rs);
    }

    public function searchChild($parent_id, $output_array) :array
    {
        $childs = [];
        foreach ($output_array as $value) {
            if($value['parent_id'] === $parent_id) {
                $childs[] = $value;
                foreach ($this->searchChild($value['id'], $output_array) as $sub_value) {
                    $childs[] = $sub_value;
                }
            }
        }
        return $childs;
    }

    public function makeURL($i, $site_url) :string
    {
        $url = $this->modx->makeUrl($i, '', '', 'full');
        if ( $url === $site_url ) {
            return '/';
        }
        return str_replace($site_url, '', $url);
    }

    public function selectResource(int $rid) :array
    {
        $sql = "SELECT id, type, pagetitle, published, parent, template, alias
            FROM `{$this->db['name']}`.`{$this->db['prefix']}site_content`
            WHERE id = {$rid}
            AND deleted = 0;";
        $rs = $this->modx->db->query($sql);
        $row = $this->modx->db->makeArray($rs);
        if (empty($row)) {
            return array();
        }
        return $row[0];
    }

    public function getSiteContentAI() :int
    {
        $sql = "SELECT auto_increment
        FROM information_schema.tables
        WHERE table_schema = '{$this->db['name']}'
        AND table_name = '{$this->db['prefix']}site_content';";

        $rs = $this->modx->db->query($sql);
        return $this->modx->db->getValue($rs);
    }

    public function tvList() :void
    {
        $sql = "SELECT id, templatename
        FROM `{$this->db['name']}`.`{$this->db['prefix']}site_templates`;";
        $rs = $this->modx->db->query($sql);
        $rows = $this->modx->db->makeArray($rs);
        foreach($rows as $row) {
            $this->template_list[$row['id']] = sprintf('%s (%s)', $row['templatename'], $row['id']);
        }
    }

    public function tamplateNameList() :void
    {
        $sql = "SELECT id, templatename
        FROM `{$this->db['name']}`.`{$this->db['prefix']}site_templates`;";
        $rs = $this->modx->db->query($sql);
        $rows = $this->modx->db->makeArray($rs);
        foreach($rows as $row) {
            $this->template_list[$row['id']] = sprintf('%s (%s)', $row['templatename'], $row['id']);
        }
    }

    public function getTemplate( string $filename = '' ) :string
    {
        if ( !file_exists( dirname(__FILE__) . '/templates/' . $filename) ) {
            $this->msg['ErrorMessage'] = "Error: getTemplateFile()\n";
            $this->Error();
        }
        return file_get_contents( dirname(__FILE__) . '/templates/' . $filename );
    }
}