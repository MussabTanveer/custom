<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Add Rubric");
    $PAGE->set_heading("Add New Rubric");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/add_rubric.php');
    
    echo $OUTPUT->header();
	require_login();
	$rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
?>

<style>
	input[type='number'] {
		-moz-appearance:textfield;
	}
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
	}
</style>

<?php

    // Save and Cont
    if(isset($_POST['save'])){
        try {
            $transaction = $DB->start_delegated_transaction();
            // Rubric Info
            $rname=trim($_POST['rubricname']);
            $rdescription=trim($_POST['rubricdesc']);
            // Insert Rubric Info
            $record = new stdClass();
            $record->name = $rname;
            $record->description = $rdescription;
            $rubricid = $DB->insert_record('rubric', $record);
            //var_dump($rname); echo "<br>";
            //var_dump($rdescription); echo "<br>";

            // Criterion + Scales Info
            //$criterionDesc=array();
            $scaleDesc=array();
            $scaleScore=array();
            $i = 1;
            foreach ($_POST['criteriondesc'] as $cd)
            {
                // Insert Criterion Info
                $record = new stdClass();
                $record->rubric = $rubricid;
                $record->description = $cd;
                $criterionid = $DB->insert_record('rubric_criterion', $record);
                //var_dump($cd); echo "<br>";
                //array_push($criterionDesc,$cd);
                foreach ($_POST['scaledesc'.$i] as $sd)
                {
                    array_push($scaleDesc,$sd);	
                }
                foreach ($_POST['scalescore'.$i] as $ss)
                {
                    array_push($scaleScore,$ss);	
                }

                // Insert Scales Info
                for ($j=0; $j < count($scaleScore); $j++) { 
                    $record = new stdClass();
                    $record->rubric = $rubricid;
                    $record->criterion = $criterionid;
                    $record->description = $scaleDesc[$j];
                    $record->score = $scaleScore[$j];
                    $DB->insert_record('rubric_scale', $record);
                }
                //var_dump($scaleDesc); echo "<br>";
                //var_dump($scaleScore); echo "<br>";
                unset($scaleDesc); unset($scaleScore); // remove arrays
                $scaleDesc=array(); $scaleScore=array(); // reinitialize arrays
                $i++;
            }

            $transaction->allow_commit();
            $msg = "<font color='green'>Rubric successfully defined!</font>";
        } catch(Exception $e) {
            $transaction->rollback($e);
            $msg = "<font color='red'>Rubric failed to save!</font>";
        }
    }
    // Save and Return
    elseif(isset($_POST['return'])){
        try {
            $transaction = $DB->start_delegated_transaction();
            // Rubric Info
            $rname=trim($_POST['rubricname']);
            $rdescription=trim($_POST['rubricdesc']);
            // Insert Rubric Info
            $record = new stdClass();
            $record->name = $rname;
            $record->description = $rdescription;
            $rubricid = $DB->insert_record('rubric', $record);
            //var_dump($rname); echo "<br>";
            //var_dump($rdescription); echo "<br>";

            // Criterion + Scales Info
            //$criterionDesc=array();
            $scaleDesc=array();
            $scaleScore=array();
            $i = 1;
            foreach ($_POST['criteriondesc'] as $cd)
            {
                // Insert Criterion Info
                $record = new stdClass();
                $record->rubric = $rubricid;
                $record->description = $cd;
                $criterionid = $DB->insert_record('rubric_criterion', $record);
                //var_dump($cd); echo "<br>";
                //array_push($criterionDesc,$cd);
                foreach ($_POST['scaledesc'.$i] as $sd)
                {
                    array_push($scaleDesc,$sd);	
                }
                foreach ($_POST['scalescore'.$i] as $ss)
                {
                    array_push($scaleScore,$ss);	
                }

                // Insert Scales Info
                for ($j=0; $j < count($scaleScore); $j++) { 
                    $record = new stdClass();
                    $record->rubric = $rubricid;
                    $record->criterion = $criterionid;
                    $record->description = $scaleDesc[$j];
                    $record->score = $scaleScore[$j];
                    $DB->insert_record('rubric_scale', $record);
                }
                //var_dump($scaleDesc); echo "<br>";
                //var_dump($scaleScore); echo "<br>";
                unset($scaleDesc); unset($scaleScore); // remove arrays
                $scaleDesc=array(); $scaleScore=array(); // reinitialize arrays
                $i++;
            }

            $transaction->allow_commit();
            $msg = "<font color='green'>Rubric successfully defined!</font>";
            $redirect_page1='./report_chairman.php';
            redirect($redirect_page1);
        } catch(Exception $e) {
            $transaction->rollback($e);
            $msg = "<font color='red'>Rubric failed to save!</font>";
        }
    }
    
    if(isset($msg)){
        echo $msg;
    }
    
    ?>
    <br />
    <form method='post' action="" class="mform" class="form-inline">
        <div class="row">
            <div class="col-md-4"><h3 style="color: firebrick;"><u>Rubric</u></h3></div>
            <div class="col-md-8"></div>
        </div>
        
        <div class="form-group row fitem ">
            <div class="col-md-3">
                <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                </span>
                <label class="col-form-label d-inline" for="id_rubricname">
                    Name
                </label>
            </div>
            <div class="col-md-9 form-inline felement" data-fieldtype="text">
                <input type="text"
                        class="form-control "
                        name="rubricname"
                        id="id_rubricname"
                        size=""
                        required
                        maxlength="100" >
                <div class="form-control-feedback" id="id_error_rubricname">
                </div>
            </div>
        </div>
        
        <div class="form-group row fitem">
            <div class="col-md-3">
                <span class="pull-xs-right text-nowrap">
                </span>
                <label class="col-form-label d-inline" for="id_rubricdesc">
                    Description
                </label>
            </div>
            <div class="col-md-9 form-inline felement" data-fieldtype="editor">
                <div>
                    <div>
                        <textarea id="id_rubricdesc" name="rubricdesc" class="form-control" rows="4" cols="80" spellcheck="true" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="form-control-feedback" id="id_error_rubricdesc"  style="display: none;">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4"><h3 style="color: navy;"><u>Criteria</u></h3></div>
            <div class="col-md-8"></div>
        </div>
        
        <div id="dynamicCriterion">
        <div class="row">
            <div class="col-md-4"><h4 style="color: olive;">Criterion 1</h4></div>
            <div class="col-md-8"></div>
        </div>

        <div class="form-group row fitem">
            <div class="col-md-3">
                <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                </span>
                <label class="col-form-label d-inline" for="id_criteriondesc">
                    Description
                </label>
            </div>
            <div class="col-md-9 form-inline felement" data-fieldtype="editor">
                <div>
                    <div>
                        <textarea required id="id_criteriondesc" name="criteriondesc[]" class="form-control" rows="4" cols="80" spellcheck="true" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="form-control-feedback" id="id_error_criteriondesc" style="display: none;">
                </div>
            </div>
        </div>

        <div id="dynamicScale1" style="padding-left: 25px;">
        <div class="row">
            <div class="col-md-4">
                <h5 style="color: chocolate; display: inline;">Scales</h5>
            </div>
            <div class="col-md-8"></div>
        </div>

        <div class="row">
            <div class="col-md-4"><b style="color: teal;">Scale 1</b></div>
            <div class="col-md-8"></div>
        </div>

        <div class="form-group row fitem">
            <div class="col-md-2 col-sm-3">
                <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                </span>
                <label class="col-form-label d-inline" for="id_scaledesc">
                    Description
                </label>
            </div>
            <div class="col-md-4 col-sm-3 form-inline felement" data-fieldtype="editor">
                <div>
                    <div>
                        <textarea required id="id_scaledesc" name="scaledesc1[]" class="form-control" rows="3" cols="40" spellcheck="true" maxlength="200"></textarea>
                    </div>
                </div>
                <div class="form-control-feedback" id="id_error_scaledesc"  style="display: none;">
                </div>
            </div>
            <div class="col-md-2 col-sm-3">
                <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                </span>
                <label class="col-form-label d-inline" for="id_scalescore">
                    Score
                </label>
            </div>
            <div class="col-md-4 col-sm-3 form-inline felement" data-fieldtype="number">
                <input type="number"
                        class="form-control"
                        name="scalescore1[]"
                        id="id_scalescore"
                        size=""
                        required
                        placeholder="eg. 1"
                        maxlength="7"
                        step="1"
                        min="0" max="100">
                <div class="form-control-feedback" id="id_error_scalescore">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3"><b style="color: teal;">Scale 2</b></div>
            <div class="col-md-9"></div>
        </div>

        <div class="form-group row fitem">
            <div class="col-md-2 col-sm-3">
                <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                </span>
                <label class="col-form-label d-inline" for="id_scaledesc">
                    Description
                </label>
            </div>
            <div class="col-md-4 col-sm-3 form-inline felement" data-fieldtype="editor">
                <div>
                    <div>
                        <textarea required id="id_scaledesc" name="scaledesc1[]" class="form-control" rows="3" cols="40" spellcheck="true" maxlength="200"></textarea>
                    </div>
                </div>
                <div class="form-control-feedback" id="id_error_scaledesc"  style="display: none;">
                </div>
            </div>
            <div class="col-md-2 col-sm-3">
                <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                </span>
                <label class="col-form-label d-inline" for="id_scalescore">
                    Score
                </label>
            </div>
            <div class="col-md-4 col-sm-3 form-inline felement" data-fieldtype="number">
                <input type="number"
                        class="form-control"
                        name="scalescore1[]"
                        id="id_scalescore"
                        size=""
                        required
                        placeholder="eg. 1"
                        maxlength="7"
                        step="1"
                        min="0" max="100">
                <div class="form-control-feedback" id="id_error_scalescore">
                </div>
            </div>
        </div>
        <div id="CS1">
            <input class="btn btn-warning" type="button" value="&#10133; Scale" onClick="addScale('dynamicScale1',1,3);">
        </div>
        </div> <!-- /dynamic scale end -->
        </div> <!-- /dynamic criterion end -->

        </br>
        <div class="row">
            <div class="col-md-4">
                <input class="btn btn-success" type="button" value="&#10133; Criterion" onClick="addCriterion('dynamicCriterion');">
            </div>
            <div class="col-md-8">
            </div>
        </div>
        <br />
        
        <input class="btn btn-info" type="submit" name="save" value="Save and continue"/>
        <input class="btn btn-info" type="submit" name="return" value="Save and return"/>
        <a class="btn btn-default" type="submit" href="./report_chairman.php">Cancel</a>
        
    </form>
    <br />
    <div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>
    
    <script>
        // script to add criteria to form
        var c = 2;
        
        function addCriterion(divName){
            var newdiv = document.createElement('div');
            newdiv.innerHTML = '<br><div class="row"><div class="col-md-4"><h4 style="color: olive;">Criterion '+c+'</h4></div><div class="col-md-8"></div></div>';
            document.getElementById(divName).appendChild(newdiv);
            
            var newdiv1 = document.createElement('div');
            newdiv1.innerHTML = '<div class="form-group row fitem"><div class="col-md-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_criteriondesc">Description</label></div><div class="col-md-9 form-inline felement" data-fieldtype="editor"><div><div><textarea required id="id_criteriondesc" name="criteriondesc[]" class="form-control" rows="4" cols="80" spellcheck="true" maxlength="500"></textarea></div></div><div class="form-control-feedback" id="id_error_criteriondesc" style="display: none;"></div></div></div>';
            document.getElementById(divName).appendChild(newdiv1);
            
            var scaleDiv = document.createElement('div');
            dynScaleid = "dynamicScale"+c;
            scaleDiv.id = dynScaleid;
            scaleDiv.style.paddingLeft = "25px";

            var newdiv2 = document.createElement('div');
            newdiv2.innerHTML = '<div class="row"><div class="col-md-4"><h5 style="color: chocolate; display: inline;">Scales</h5></div><div class="col-md-8"></div></div>';
            scaleDiv.appendChild(newdiv2);

            var newdiv3 = document.createElement('div');
            newdiv3.innerHTML = '<div class="row"><div class="col-md-4"><b style="color: teal;">Scale 1</b></div><div class="col-md-8"></div></div>';
            scaleDiv.appendChild(newdiv3);

            var newdiv4 = document.createElement('div');
            newdiv4.innerHTML = '<div class="form-group row fitem"><div class="col-md-2 col-sm-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_scaledesc">Description</label></div><div class="col-md-4 col-sm-3 form-inline felement" data-fieldtype="editor"><div><div><textarea required id="id_scaledesc" name="scaledesc'+c+'[]" class="form-control" rows="3" cols="40" spellcheck="true" maxlength="200"></textarea></div></div><div class="form-control-feedback" id="id_error_scaledesc"  style="display: none;"></div></div><div class="col-md-2 col-sm-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_scalescore">Score</label></div><div class="col-md-4 col-sm-3 form-inline felement" data-fieldtype="number"><input type="number" class="form-control" name="scalescore'+c+'[]" id="id_scalescore" size="" required placeholder="eg. 1" maxlength="7" step="1" min="0" max="100"><div class="form-control-feedback" id="id_error_scalescore"></div></div></div>';
            scaleDiv.appendChild(newdiv4);

            var newdiv5 = document.createElement('div');
            newdiv5.innerHTML = '<div class="row"><div class="col-md-4"><b style="color: teal;">Scale 2</b></div><div class="col-md-8"></div></div>';
            scaleDiv.appendChild(newdiv5);

            var newdiv6 = document.createElement('div');
            newdiv6.innerHTML = '<div class="form-group row fitem"><div class="col-md-2 col-sm-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_scaledesc">Description</label></div><div class="col-md-4 col-sm-3 form-inline felement" data-fieldtype="editor"><div><div><textarea required id="id_scaledesc" name="scaledesc'+c+'[]" class="form-control" rows="3" cols="40" spellcheck="true" maxlength="200"></textarea></div></div><div class="form-control-feedback" id="id_error_scaledesc"  style="display: none;"></div></div><div class="col-md-2 col-sm-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_scalescore">Score</label></div><div class="col-md-4 col-sm-3 form-inline felement" data-fieldtype="number"><input type="number" class="form-control" name="scalescore'+c+'[]" id="id_scalescore" size="" required placeholder="eg. 1" maxlength="7" step="1" min="0" max="100"><div class="form-control-feedback" id="id_error_scalescore"></div></div></div>';
            scaleDiv.appendChild(newdiv6);

            var newdiv7 = document.createElement('div');
            newdiv7.innerHTML = '<div id="CS'+c+'"><input class="btn btn-warning" type="button" value="&#10133; Scale" onClick="addScale(\'dynamicScale'+c+'\','+c+',3);"></div>';
            scaleDiv.appendChild(newdiv7);
            
            document.getElementById(divName).appendChild(scaleDiv);
            
            c++;
        }

        // script to add scales to form
        //var i = 3;
        
        function addScale(divName, cno, sno){ // param1: criterion's scale div, param2: criterion num, param3: scale num
            var newdiv = document.createElement('div');
            newdiv.innerHTML = '<div class="row"><div class="col-md-4"><b style="color: teal;">Scale '+sno+'</b></div><div class="col-md-8"></div></div>';
            document.getElementById(divName).appendChild(newdiv);
            
            var newdiv1 = document.createElement('div');
            newdiv1.innerHTML = '<div class="form-group row fitem"><div class="col-md-2 col-sm-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_scaledesc">Description</label></div><div class="col-md-4 col-sm-3 form-inline felement" data-fieldtype="editor"><div><div><textarea required id="id_scaledesc" name="scaledesc'+cno+'[]" class="form-control" rows="3" cols="40" spellcheck="true" ></textarea></div></div><div class="form-control-feedback" id="id_error_scaledesc"  style="display: none;"></div></div><div class="col-md-2 col-sm-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_scalescore">Score</label></div><div class="col-md-4 col-sm-3 form-inline felement" data-fieldtype="number"><input type="number" class="form-control" name="scalescore'+cno+'[]" id="id_scalescore" size="" required placeholder="eg. 1" maxlength="100" step="1" min="0" max="100"><div class="form-control-feedback" id="id_error_scalescore"></div></div></div>';
            document.getElementById(divName).appendChild(newdiv1);

            var divIdRmv = "CS"+cno;
            var element = document.getElementById(divIdRmv);
            element.parentNode.removeChild(element);
            sno++;

            var newdiv2 = document.createElement('div');
            newdiv2.innerHTML = '<div id="CS'+cno+'"><input class="btn btn-warning" type="button" value="&#10133; Scale" onClick="addScale(\'dynamicScale'+cno+'\','+cno+','+sno+');"></div>';
            document.getElementById(divName).appendChild(newdiv2);
            
            //i++;
        }
    </script>


    <?php
        echo $OUTPUT->footer();
    ?>
