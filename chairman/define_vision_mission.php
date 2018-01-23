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

    <p id=msg> </p>

<?php

    if(isset($_POST['return']))
    {
        $universityVision = trim($_POST["uv"]);
        $universityMission = trim($_POST["um"]);
        $departmentVision = trim($_POST["dv"]);
        $departmentMission = trim($_POST["dm"]);
        
        $universityVision = mysql_real_escape_string($universityVision);
        $universityMission = mysql_real_escape_string($universityMission);
        $departmentVision = mysql_real_escape_string($departmentVision);
        $departmentMission = mysql_real_escape_string($departmentMission);


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


              $sql="INSERT INTO  mdl_vision_mission (name,idnumber,description,revision) VALUES ('university vision','uv','$universityVision','$rev')";

                $DB->execute($sql);
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

        $sql="INSERT INTO  mdl_vision_mission (name,idnumber,description,revision) VALUES ('university mission','um','$universityMission','$rev')";
        $DB->execute($sql);

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


        $sql="INSERT INTO  mdl_vision_mission (name,idnumber,description,revision) VALUES ('department vision','dv','$departmentVision','$rev')";
        $DB->execute($sql);
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

        $sql="INSERT INTO  mdl_vision_mission (name,idnumber,description,revision) VALUES ('department mission','dm','$departmentMission','$rev')";
            
        $DB->execute($sql);

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
        <button class="btn btn-info" type="submit"  name="save" /> Save and continue </button>
        <input class="btn btn-info" type="submit" name="return" value="Save and return"/>
		<a class="btn btn-default" type="submit" href="./report_admin.php">Cancel</a>
    </form>
    <?php
    echo $OUTPUT->footer();
?>
