<?php
    require_once('../../../config.php');
    //require_once($CFG->dirroot.'/lib/form/editor.php');
    //require_once($CFG->dirroot.'/lib/editorlib.php');

    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Vision & Mission");
    $PAGE->set_heading("Define Vision & Mission");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/admin/define_vision_mission.php');


    echo $OUTPUT->header();
    require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());

    //var_dump($_POST);
    $temp = array();
    $editor = \editors_get_preferred_editor();
    $editor->use_editor("id_uv",$temp);
    $editor->use_editor("id_um",$temp);
    $editor->use_editor("id_dv",$temp);
    $editor->use_editor("id_dm",$temp);

    ?>
    <form method="post" action="" class="mform">
        <div class="container">
            
            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                    </span>
                    <label class="col-form-label d-inline" for="id_uv">
                        University Vision
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="editor">
                    <div>
                        <div>
                            <textarea id="id_uv" name="uv" class="form-control" rows="4" cols="80" spellcheck="true" ></textarea>
                        </div>
                    </div>
                    <div class="form-control-feedback" id="id_error_uv"  style="display: none;">
                    </div>
                </div>
            </div>

            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                    </span>
                    <label class="col-form-label d-inline" for="id_um">
                        University Mission
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="editor">
                    <div>
                        <div>
                            <textarea id="id_um" name="um" class="form-control" rows="4" cols="80" spellcheck="true" ></textarea>
                        </div>
                    </div>
                    <div class="form-control-feedback" id="id_error_um"  style="display: none;">
                    </div>
                </div>
            </div>

            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                    </span>
                    <label class="col-form-label d-inline" for="id_dv">
                        Department Vision
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="editor">
                    <div>
                        <div>
                            <textarea id="id_dv" name="dv" class="form-control" rows="4" cols="80" spellcheck="true" ></textarea>
                        </div>
                    </div>
                    <div class="form-control-feedback" id="id_error_dv"  style="display: none;">
                    </div>
                </div>
            </div>

            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                    </span>
                    <label class="col-form-label d-inline" for="id_dm">
                        Department Mission
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="editor">
                    <div>
                        <div>
                            <textarea id="id_dm" name="dm" class="form-control" rows="4" cols="80" spellcheck="true" ></textarea>
                        </div>
                    </div>
                    <div class="form-control-feedback" id="id_error_dm"  style="display: none;">
                    </div>
                </div>
            </div>
        </div>
        <input class="btn btn-info" type="submit" name="save" value="Save and continue"/>
        <input class="btn btn-info" type="submit" name="return" value="Save and return"/>
		<a class="btn btn-default" type="submit" href="./report_admin.php">Cancel</a>
    </form>
    <?php
    /*
    echo "<form method='post'>";
    echo \html_writer::tag('textarea', 'default',
        array('id' => "someid", 'name' => 'somename', 'rows' => 5, 'cols' => 10));

    echo "<input type='submit' name='submit' />";
    echo "</form>";
    */
    echo $OUTPUT->footer();
?>