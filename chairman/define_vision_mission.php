<?php
    require_once('../../../config.php');
    //require_once($CFG->dirroot.'/lib/form/editor.php');
    //require_once($CFG->dirroot.'/lib/editorlib.php');

    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Vision & Mission");
    $PAGE->set_heading("Define Vision & Mission");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/define_vision_mission.php');

    echo $OUTPUT->header();
    require_login();
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
    
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <script src="../script/jquery/jquery-2.1.3.js"></script>

    <script type="text/javascript" >

        $(document).ready(function(){
            $("button").click(function(){
                var formdata = $("form").serialize();
                $.ajax({
                    type: "POST",
                    url: "save_vision_mission.php",
                    data: formdata,
                    success:function(){
                        document.getElementById("msg").innerHTML ="<font color='green'>Vision and Mission successfully defined!</font>"
                    }
                });
                return false;
            });
        });
    </script>

    

<?php
    global $CFG;
    $dbp= $CFG->dbpass;
    $dbh = $CFG->dbhost;
    $dbn = $CFG->dbname;
    $dbu = $CFG->dbuser;

    if(isset($_POST['return']))
    {
        $universityVision = trim($_POST["uv"]);
        $universityMission = trim($_POST["um"]);
        $departmentVision = trim($_POST["dv"]);
        $departmentMission = trim($_POST["dm"]);
        $departmentName = trim($_POST["departName"]);
        $UniversityName = trim($_POST["uniName"]);

        
        if($departmentName != "")
        {

           $revisions=$DB->get_records_sql('SELECT revision FROM `mdl_vision_mission` where idnumber = ?', array('dn'));

            $rev=0;
            if($revisions){
                foreach ($revisions as $revision){
                    $rev = $revision->revision; 
                }
            }
            $rev++;

            $record = new stdclass();
            $record->name='department name';
            $record->idnumber = 'dn';
            $record->description=$departmentName;
            $record->revision=$rev;
            $insert = $DB->insert_record('vision_mission', $record);
        }


        if($UniversityName != "")
        {

           $revisions=$DB->get_records_sql('SELECT revision FROM `mdl_vision_mission` where idnumber = ?', array('un'));

            $rev=0;
            if($revisions){
                foreach ($revisions as $revision){
                    $rev = $revision->revision; 
                }
            }
            $rev++;

            $record = new stdclass();
            $record->name='university name';
            $record->idnumber = 'un';
            $record->description=$UniversityName;
            $record->revision=$rev;
            $insert = $DB->insert_record('vision_mission', $record);
        }



        if($universityVision != "")
        {

           $revisions=$DB->get_records_sql('SELECT revision FROM `mdl_vision_mission` where idnumber = ?', array('uv'));

            $rev=0;
            if($revisions){
                foreach ($revisions as $revision){
                    $rev = $revision->revision; 
                }
            }
            $rev++;

            $record = new stdclass();
            $record->name='university vision';
            $record->idnumber = 'uv';
            $record->description=$universityVision;
            $record->revision=$rev;
            $insert = $DB->insert_record('vision_mission', $record);
        }


        if($universityMission != "")
        {

         $revisions=$DB->get_records_sql('SELECT revision FROM `mdl_vision_mission` where idnumber = ?', array('um'));

            $rev=0;
            if($revisions){
                foreach ($revisions as $revision){
                    $rev = $revision->revision; 
                }
            }
            $rev++;

            $record = new stdclass();
            $record->name='university mission';
            $record->idnumber = 'um';
            $record->description=$universityMission;
            $record->revision=$rev;
            $insert = $DB->insert_record('vision_mission', $record);

        }


        if($departmentVision != "")
        {

            $revisions=$DB->get_records_sql('SELECT revision FROM `mdl_vision_mission` where idnumber = ?', array('dv'));

            $rev=0;
            if($revisions){
                foreach ($revisions as $revision){
                    $rev = $revision->revision; 
                }
            }
            $rev++;

            $record = new stdclass();
            $record->name='department vision';
            $record->idnumber = 'dv';
            $record->description=$departmentVision;
            $record->revision=$rev;
            $insert = $DB->insert_record('vision_mission', $record);
        }


        if($departmentMission != "")
        {
          $revisions=$DB->get_records_sql('SELECT revision FROM `mdl_vision_mission` where idnumber = ?', array('dm'));

            $rev=0;
            if($revisions){
                foreach ($revisions as $revision){
                    $rev = $revision->revision; 
                }
            }
            $rev++;

            $record = new stdclass();
            $record->name='department mission';
            $record->idnumber = 'dm';
            $record->description=$departmentMission;
            $record->revision=$rev;
            $insert = $DB->insert_record('vision_mission', $record);

        }

        
        $redirect_page1='../index.php';
        redirect($redirect_page1); 

    }


    $temp = array();
    $editor = \editors_get_preferred_editor();
    $editor->use_editor("id_uv",$temp);
    $editor->use_editor("id_um",$temp);
    $editor->use_editor("id_dv",$temp);
    $editor->use_editor("id_dm",$temp);
    $editor->use_editor("uniName",$temp);
    $editor->use_editor("departName",$temp);

    ?>
    <form method="post" action="" class="mform" id="vismisForm">
        <div class="container">

            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                    </span>
                    <label class="col-form-label d-inline" for="id_uv">
                        University Name
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="editor">
                    <div>
                        <div>
                            <textarea id="uniName" name="uniName" class="form-control" rows="4" cols="80" spellcheck="true" ></textarea>
                        </div>
                    </div>
                    <div class="form-control-feedback" id="id_error_uniName"  style="display: none;">
                    </div>
                </div>
            </div>


            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                    </span>
                    <label class="col-form-label d-inline" for="id_uv">
                        Department Name
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="editor">
                    <div>
                        <div>
                            <textarea id="departName" name="departName" class="form-control" rows="4" cols="80" spellcheck="true" ></textarea>
                        </div>
                    </div>
                    <div class="form-control-feedback" id="id_error_departName"  style="display: none;">
                    </div>
                </div>
            </div>

            
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
        
        <button class="btn btn-info" type="submit"  name="save" /> Save and continue </button>
        <input class="btn btn-info" type="submit" name="return" value="Save and return"/>
		<a class="btn btn-default" href="./report_chairman.php">Cancel</a>
    </form>
    <p id=msg> </p>
    <?php

    echo $OUTPUT->footer();
?>
