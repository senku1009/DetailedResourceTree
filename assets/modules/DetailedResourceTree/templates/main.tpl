<h1>
    <i class="fa fa-file-text"></i> [+lang.module_name+]
</h1>

<div id="actions">
    <div class="btn-group">
        <a id="Button1" class="btn btn-success" href="javascript:;"
            onclick="window.location.href='index.php?a=106';">
        <i class="fa fa-times-circle"></i><span>[+lang.close+]</span>
        </a>
    </div>
</div>

<div class="tab-pane" id="DetailedResourceTreePane">
    <script type="text/javascript">
        tpResources = new WebFXTabPane(document.getElementById('DetailedResourceTreePane'));
    </script>

    <div class="tab-page" id="tabTemplates">
        <h2 class="tab"><i class="fa fa-newspaper-o"></i> [+lang.resource_list+]</h2>
        <script type="text/javascript">tpResources.addTabPage(document.getElementById('tabTemplates'));</script>
        <div class="tab-body">
            <h4><i class="fa fa-calendar"></i> [+lang.resource_list+]</h4>
            <p>[+lang.resource_list_info+]</p>
            <br />
            <form name="template" action="" method="POST">
                <p class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg" name="dl_pagelist_csv" value="1">[+lang.download_as_csv+]</button>
                </p>
                <br />
                <table class='grid' cellpadding='1' cellspacing='1'>
                    <thead>
                        <tr>
                            <th class='gridHeader'>[+lang.id+]</th>
                            <th class='gridHeader'>[+lang.type+]</th>
                            <th class='gridHeader'>[+lang.pagetitle+]</th>
                            <th class='gridHeader'>[+lang.published+]</th>
                            <th class='gridHeader'>[+lang.parent_id+]</th>
                            <th class='gridHeader'>[+lang.parent_or_child+]</th>
                            <th class='gridHeader'>[+lang.template+]</th>
                            <th class='gridHeader'>[+lang.alias+]</th>
                            <th class='gridHeader'>[+lang.url+]</th>
                        </tr>
                    </thead>
                    <tbody>
                        [+tbody+]
                    </tbody>
                </table>
                <br />
            </form>
        </div>
    </div>

    <div class="tab-page" id="tabTemplateVariables">
    <h2 class="tab"><i class="fa fa-list-alt"></i> [+lang.template_variable_list+]</h2>
        <script
            type="text/javascript">tpResources.addTabPage(document.getElementById('tabTemplateVariables'));</script>
        <div class="tab-body">
            <h4><i class="fa fa-calendar"></i> [+lang.template_variable_list+]</h4>
            <p>[+lang.select_the_template_variable_you_want_to_display+]</p>
            <form action="" name="tmplvars" method="POST">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">[+lang.tv_name+]</th>
                            <th scope="col">[+lang.caption+]</th>
                            <th scope="col">[+lang.description+]</th>
                        </tr>
                    </thead>
                    <tbody>
                        [+tmplvar_checkbox+]
                    </tbody>
                </table>
                <br />
                <p class="text-center">
                    <input class="custom-control-input" type="checkbox" name="remove_null" value="1" id="remove_null"[+checked_remove_null+]>
                    <label class="custom-control-label" for="remove_null">
                        [+lang.show_only_resources_that_have_a_value+]
                    </label>
                </p>
                <br />
                <p class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">[+lang.filter+]</button>
                    <button type="submit" class="btn btn-primary btn-lg" name="dl_tv_csv" value="1">[+lang.download_as_csv+]</button>
                </p>
            </form>
            <br>
            <h4><i class="fa fa-calendar"></i> [+lang.results+]</h4>
            <table class='grid' cellpadding='1' cellspacing='1'>
                <thead>
                    [+tv_head+]
                </thead>
                <tbody>
                    [+tv_body+]
                </tbody>
            </table>
        </div>
    </div>
