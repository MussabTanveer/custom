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

<script src="../script/jquery/jquery-3.2.1.js"></script>
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

    // Save and Cont OR Save and Return
    if(isset($_POST['save']) || isset($_POST['return'])){
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
                while(!isset($_POST['scalescore'.$i])){
                    $i++;
                }
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
            if(isset($_POST['return'])){
                $redirect_page1='./report_chairman.php';
                redirect($redirect_page1);
            }
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
            <div id="crit1">
                <div class="row">
                    <div class="col-md-4"><h4 style="color: olive;">Criterion <!--1--></h4></div>
                    <div class="col-md-8">
                        <i id="crossC1" class="fa fa-times" style="font-size:28px;color:red;cursor:pointer" title="Remove"></i>
                    </div>
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

                    <div id="scale11">
                        <div class="row">
                            <div class="col-md-4"><b style="color: teal;">Scale <!--1--></b></div>
                            <div class="col-md-8">
                                <i id="crossS11" class="fa fa-times" style="font-size:20px;color:red;cursor:pointer" title="Remove"></i>
                            </div>
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
                                        step="0.001"
                                        min="0" max="100">
                                <div class="form-control-feedback" id="id_error_scalescore">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="scale12">
                        <div class="row">
                            <div class="col-md-4"><b style="color: teal;">Scale <!--2--></b></div>
                            <div class="col-md-8">
                                <i id="crossS12" class="fa fa-times" style="font-size:20px;color:red;cursor:pointer" title="Remove"></i>
                            </div>
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
                                        step="0.001"
                                        min="0" max="100">
                                <div class="form-control-feedback" id="id_error_scalescore">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="CS1">
                        <input class="btn btn-warning" type="button" value="&#10133; Scale" onClick="addScale('dynamicScale1',1,3);">
                    </div>
                </div> <!-- /dynamic scale end -->
            </div> <!-- / criterion div wrap -->
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
        // script to remove pre-entered criterion and scales fields from form
        $(document).ready(function(){
            $("#crossC1").click(function(){
                $("#crit1").remove();
            });
            $("#crossS11").click(function(){
                $("#scale11").remove();
            });
            $("#crossS12").click(function(){
                $("#scale12").remove();
            });
        });
    </script>

    <script>
        // script to add criteria to form
        var c = 2;
        
        function addCriterion(divName){
            var divCritWrap = document.createElement('div');
            var divid = "crit"+c;
            divCritWrap.setAttribute("id", divid);
            document.getElementById(divName).appendChild(divCritWrap);

            var newdiv = document.createElement('div');
            newdiv.innerHTML = '<br><div class="row"><div class="col-md-4"><h4 style="color: olive;">Criterion </h4></div><div class="col-md-8"><i id="crossC'+c+'" class="fa fa-times" style="font-size:28px;color:red;cursor:pointer" title="Remove"></i></div></div>';
            divCritWrap.appendChild(newdiv);
            
            var newdiv1 = document.createElement('div');
            newdiv1.innerHTML = '<div class="form-group row fitem"><div class="col-md-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_criteriondesc">Description</label></div><div class="col-md-9 form-inline felement" data-fieldtype="editor"><div><div><textarea required id="id_criteriondesc" name="criteriondesc[]" class="form-control" rows="4" cols="80" spellcheck="true" maxlength="500"></textarea></div></div><div class="form-control-feedback" id="id_error_criteriondesc" style="display: none;"></div></div></div>';
            divCritWrap.appendChild(newdiv1);
            
            var scaleDiv = document.createElement('div');
            dynScaleid = "dynamicScale"+c;
            scaleDiv.id = dynScaleid;
            scaleDiv.style.paddingLeft = "25px";
            
            var newdiv2 = document.createElement('div');
            newdiv2.innerHTML = '<div class="row"><div class="col-md-4"><h5 style="color: chocolate; display: inline;">Scales</h5></div><div class="col-md-8"></div></div>';
            scaleDiv.appendChild(newdiv2);

            var scaleDiv1 = document.createElement('div');
            scale1id = "scale"+c+"1";
            scaleDiv1.id = scale1id;
            scaleDiv.appendChild(scaleDiv1);

            var newdiv3 = document.createElement('div');
            newdiv3.innerHTML = '<div class="row"><div class="col-md-4"><b style="color: teal;">Scale</b></div><div class="col-md-8"><i id="crossS'+c+'1" class="fa fa-times" style="font-size:20px;color:red;cursor:pointer" title="Remove"></i></div></div>';
            scaleDiv1.appendChild(newdiv3);

            var newdiv4 = document.createElement('div');
            newdiv4.innerHTML = '<div class="form-group row fitem"><div class="col-md-2 col-sm-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_scaledesc">Description</label></div><div class="col-md-4 col-sm-3 form-inline felement" data-fieldtype="editor"><div><div><textarea required id="id_scaledesc" name="scaledesc'+c+'[]" class="form-control" rows="3" cols="40" spellcheck="true" maxlength="200"></textarea></div></div><div class="form-control-feedback" id="id_error_scaledesc"  style="display: none;"></div></div><div class="col-md-2 col-sm-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_scalescore">Score</label></div><div class="col-md-4 col-sm-3 form-inline felement" data-fieldtype="number"><input type="number" class="form-control" name="scalescore'+c+'[]" id="id_scalescore" size="" required placeholder="eg. 1" maxlength="7" step="0.001" min="0" max="100"><div class="form-control-feedback" id="id_error_scalescore"></div></div></div>';
            scaleDiv1.appendChild(newdiv4);

            var scaleDiv2 = document.createElement('div');
            scale2id = "scale"+c+"2";
            scaleDiv2.id = scale2id;
            scaleDiv.appendChild(scaleDiv2);

            var newdiv5 = document.createElement('div');
            newdiv5.innerHTML = '<div class="row"><div class="col-md-4"><b style="color: teal;">Scale</b></div><div class="col-md-8"><i id="crossS'+c+'2" class="fa fa-times" style="font-size:20px;color:red;cursor:pointer" title="Remove"></i></div></div>';
            scaleDiv2.appendChild(newdiv5);

            var newdiv6 = document.createElement('div');
            newdiv6.innerHTML = '<div class="form-group row fitem"><div class="col-md-2 col-sm-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_scaledesc">Description</label></div><div class="col-md-4 col-sm-3 form-inline felement" data-fieldtype="editor"><div><div><textarea required id="id_scaledesc" name="scaledesc'+c+'[]" class="form-control" rows="3" cols="40" spellcheck="true" maxlength="200"></textarea></div></div><div class="form-control-feedback" id="id_error_scaledesc"  style="display: none;"></div></div><div class="col-md-2 col-sm-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_scalescore">Score</label></div><div class="col-md-4 col-sm-3 form-inline felement" data-fieldtype="number"><input type="number" class="form-control" name="scalescore'+c+'[]" id="id_scalescore" size="" required placeholder="eg. 1" maxlength="7" step="0.001" min="0" max="100"><div class="form-control-feedback" id="id_error_scalescore"></div></div></div>';
            scaleDiv2.appendChild(newdiv6);

            var newdiv7 = document.createElement('div');
            newdiv7.innerHTML = '<div id="CS'+c+'"><input class="btn btn-warning" type="button" value="&#10133; Scale" onClick="addScale(\'dynamicScale'+c+'\','+c+',3);"></div>';
            scaleDiv.appendChild(newdiv7);
            
            divCritWrap.appendChild(scaleDiv);
            
            //remove criterion
            var idname = "#crossC" + c;
            var divname = "#crit" + c;
            $(idname).click(function(){
                $(divname).remove();
            });

            //remove scales
            var idname1 = "#crossS" + c + "1";
            var divname1 = "#scale" + c + "1";
            $(idname1).click(function(){
                $(divname1).remove();
            });
            var idname2 = "#crossS" + c + "2";
            var divname2 = "#scale" + c + "2";
            $(idname2).click(function(){
                $(divname2).remove();
            });
            
            c++;
        }

        // script to add scales to form
        //var i = 3;
        
        function addScale(divName, cno, sno){ // param1: criterion's scale div, param2: criterion num, param3: scale num
            var divScaleWrap = document.createElement('div');
            var divid = "scale"+cno+sno;
            divScaleWrap.setAttribute("id", divid);
            document.getElementById(divName).appendChild(divScaleWrap);

            var newdiv = document.createElement('div');
            newdiv.innerHTML = '<div class="row"><div class="col-md-4"><b style="color: teal;">Scale </b></div><div class="col-md-8"><i id="crossS'+cno+sno+'" class="fa fa-times" style="font-size:20px;color:red;cursor:pointer" title="Remove"></i></div></div>';
            divScaleWrap.appendChild(newdiv);
            
            var newdiv1 = document.createElement('div');
            newdiv1.innerHTML = '<div class="form-group row fitem"><div class="col-md-2 col-sm-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_scaledesc">Description</label></div><div class="col-md-4 col-sm-3 form-inline felement" data-fieldtype="editor"><div><div><textarea required id="id_scaledesc" name="scaledesc'+cno+'[]" class="form-control" rows="3" cols="40" spellcheck="true" ></textarea></div></div><div class="form-control-feedback" id="id_error_scaledesc"  style="display: none;"></div></div><div class="col-md-2 col-sm-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_scalescore">Score</label></div><div class="col-md-4 col-sm-3 form-inline felement" data-fieldtype="number"><input type="number" class="form-control" name="scalescore'+cno+'[]" id="id_scalescore" size="" required placeholder="eg. 1" maxlength="100" step="0.001" min="0" max="100"><div class="form-control-feedback" id="id_error_scalescore"></div></div></div>';
            divScaleWrap.appendChild(newdiv1);

            var divIdRmv = "CS"+cno;
            var element = document.getElementById(divIdRmv);
            element.parentNode.removeChild(element);

            var idname = "#crossS" + cno + sno;
            var divname = "#scale" + cno + sno;
            $(idname).click(function(){
                $(divname).remove();
            });

            sno++; // point to next add scale

            var newdiv2 = document.createElement('div');
            newdiv2.innerHTML = '<div id="CS'+cno+'"><input class="btn btn-warning" type="button" value="&#10133; Scale" onClick="addScale(\'dynamicScale'+cno+'\','+cno+','+sno+');"></div>';
            divScaleWrap.appendChild(newdiv2);
            
            //i++;
        }
    </script>


    <?php
        echo $OUTPUT->footer();
    ?>
