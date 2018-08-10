<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Edit Course");
    $PAGE->set_heading("Edit Course");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/itm/edit_course.php');
    
    require_login();
    if($SESSION->oberole != "itm"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    ?>
    <link rel="stylesheet" href="../css/datepicker/wbn-datepicker.css">
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <?php

    if(!empty($_GET['course']))
    {
        $course_id = $_GET['course'];
        if(isset($_POST['save'])){
            $semester=trim($_POST['semester']);
            $fullname=trim($_POST['fullname']);
            $shortname=trim($_POST['shortname']);
            //$idnumber=trim($_POST['idnumber']); $idnumber=strtoupper($idnumber);
            //$startdate=strtotime($_POST['startdate']);
            //$enddate=strtotime($_POST['enddate']);
            $summary=trim($_POST['summary_editor']);
            $time = time();

            if(empty($fullname) || empty($shortname) || /*empty($idnumber) ||*/ empty($semester))
            {
                if(empty($fullname))
                {
                    $msg1="<font color='red'>-Please enter full name</font>";
                }
                if(empty($shortname))
                {
                    $msg2="<font color='red'>-Please enter short name</font>";
                }
                /*if(empty($idnumber))
                {
                    $msg3="<font color='red'>-Please select course code</font>";
                }*/
                if(empty($semester))
                {
                    $msg6="<font color='red'>-Please select semester</font>";
                }
            }
            else{
                $sem=$DB->get_records_sql('SELECT * FROM `mdl_semester` WHERE id = ?', array($semester));
                if ($sem != NULL){
                    foreach ($sem as $s) {
                        $startdate = $s->startdate;
                        $enddate =  $s->enddate;
                    }
                }
                try {
                    $transaction = $DB->start_delegated_transaction();

                    $sql_update="UPDATE mdl_course SET fullname=?, shortname=?, summary=?, startdate=?, enddate=?, timemodified=?, semesterid=? WHERE id=?";
                    $DB->execute($sql_update, array($fullname, $shortname, $summary, $startdate, $enddate, $time, $semester, $course_id));

                    $transaction->allow_commit();
                    $msg4 = "<br><h4 style='color: green'>Course information updated!</h4>";
                } catch(Exception $e) {
                    $msg4 = "<br><h4 style='color: red'>Course information update failed!</h4>";
                    $transaction->rollback($e);
                    $msg4 = "<br><h4 style='color: red'>Course information update failed!</h4>";
                }
            }
        }
        if(isset($msg4)){
			echo $msg4;
			goto label;
		}
        
        $semesters=$DB->get_records_sql("SELECT * FROM mdl_semester ORDER BY id DESC LIMIT 12");

        if(isset($msg4)){
            echo $msg4;
            echo $msg5;
        }
        ?>
        <br />
        <form method='post' action="" class="mform">
        
            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                    </span>
                    <label class="col-form-label d-inline" for="id_semester">
                        Select Semester
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <select required name="semester" class="select custom-select" id="id_semester">
                        <option value=''>Select..</option>
                        <?php
                        foreach ($semesters as $s) {
                            ?>
                            <option value='<?php echo $s->id; ?>'><?php echo "$s->name - $s->year"; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <div class="form-control-feedback" id="id_error_semester">
                    <?php
                    if(isset($msg6)){
                        echo $msg6;
                    }
                    ?>
                    </div>
                </div>
            </div>
            
            <!--
            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                        <a class="btn btn-link p-a-0" role="button" data-container="body" data-toggle="popover" data-placement="right"
                        data-content="&lt;div class=&quot;no-overflow&quot;&gt;&lt;p&gt;The course official course-code must be entered.&lt;/p&gt;&lt;/div&gt; "
                        data-html="true" tabindex="0" data-trigger="focus">
                        <i class="icon fa fa-question-circle text-info fa-fw " aria-hidden="true" title="Help with Course ID number" aria-label="Help with Course ID number"></i>
                        </a>
                    </span>
                    <label class="col-form-label d-inline" for="id_idnumber">
                        Course code
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <select required name="idnumber" class="select custom-select" id="id_idnumber">
                        <option value=''>Select..</option>
                        <?php
                        foreach ($ccs as $cc) {
                            //$cc = substr($cc,0,6);
                            ?>
                            <option value='<?php echo $cc; ?>'><?php echo $cc; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <div class="form-control-feedback" id="id_error_idnumber">
                    <?php
                    if(isset($msg3)){
                        echo $msg3;
                    }
                    ?>
                    </div>
                </div>
            </div>
            -->
            
            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                        <a class="btn btn-link p-a-0" role="button" data-container="body" data-toggle="popover" data-placement="right"
                        data-content="&lt;div class=&quot;no-overflow&quot;&gt;&lt;p&gt;The full name of the course is displayed at the top of each page in the course and in the list of courses.&lt;/p&gt;&lt;/div&gt; "
                        data-html="true" tabindex="0" data-trigger="focus">
                        <i class="icon fa fa-question-circle text-info fa-fw " aria-hidden="true" title="Help with Course full name" aria-label="Help with Course full name"></i>
                        </a>
                    </span>
                    <label class="col-form-label d-inline" for="id_fullname">
                        Course full name
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
                            class="form-control "
                            name="fullname"
                            id="id_fullname"
                            value=""
                            required
                            placeholder="eg. Software Engineering"
                            size="50"
                            maxlength="254">
                    <div class="form-control-feedback" id="id_error_fullname">
                    <?php
                    if(isset($msg1)){
                        echo $msg1;
                    }
                    ?>
                    </div>
                </div>
            </div>

            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                        <a class="btn btn-link p-a-0" role="button" data-container="body" data-toggle="popover" data-placement="right"
                        data-content="&lt;div class=&quot;no-overflow&quot;&gt;&lt;p&gt;The short name of the course is displayed in the navigation and is used in the subject line of course email messages.&lt;/p&gt;&lt;/div&gt;"
                        data-html="true" tabindex="0" data-trigger="focus">
                        <i class="icon fa fa-question-circle text-info fa-fw " aria-hidden="true" title="Help with Course short name" aria-label="Help with Course short name"></i>
                        </a>
                    </span>
                    <label class="col-form-label d-inline" for="id_shortname">
                        Course short name
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
                            class="form-control "
                            name="shortname"
                            id="id_shortname"
                            value=""
                            required
                            placeholder="eg. SE"
                            size="20"
                            maxlength="100">
                    <div class="form-control-feedback" id="id_error_shortname">
                    <?php
                    if(isset($msg2)){
                        echo $msg2;
                    }
                    ?>
                    </div>
                </div>
            </div>

            <!--
            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                        <a class="btn btn-link p-a-0" role="button" data-container="body" data-toggle="popover" data-placement="right"
                        data-content="&lt;div class=&quot;no-overflow&quot;&gt;&lt;p&gt;This setting determines the start of the first week for a course in weekly format. It also determines the earliest date that logs of course activities are available for. If the course is reset and the course start date changed, all dates in the course will be moved in relation to the new start date.&lt;/p&gt;&lt;/div&gt; "
                        data-html="true" tabindex="0" data-trigger="focus">
                        <i class="icon fa fa-question-circle text-info fa-fw " aria-hidden="true" title="Help with Course start date" aria-label="Help with Course start date"></i>
                        </a>
                    </span>
                    <label for="id_startdate">
                        Course start date
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
                        required
                        class="form-control wbn-datepicker"
                        name="startdate"
                        id="id_startdate"
                        size="27"
                        maxlength="100" >
                    <div class="form-control-feedback" id="id_error_idnumber">
                    </div>
                </div>
            </div>

            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                        <a class="btn btn-link p-a-0" role="button"
                        data-container="body" data-toggle="popover"
                        data-placement="right" data-content="&lt;div class=&quot;no-overflow&quot;&gt;&lt;p&gt;The course end date is only used for reports. Users can still enter the course after the end date.&lt;/p&gt;
                        &lt;/div&gt; "
                        data-html="true" tabindex="0" data-trigger="focus">
                        <i class="icon fa fa-question-circle text-info fa-fw " aria-hidden="true" title="Help with Course end date" aria-label="Help with Course end date"></i>
                        </a>
                    </span>
                    <label for="id_enddate">
                        Course end date
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
                        required
                        class="form-control wbn-datepicker"
                        name="enddate"
                        id="id_enddate"
                        data-start-src="id_startdate"
                        size="27"
                        maxlength="100" >
                    <div class="form-control-feedback" id="id_error_idnumber">
                    </div>
                </div>
            </div>
            -->

            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <a class="btn btn-link p-a-0" role="button"
                        data-container="body" data-toggle="popover"
                        data-placement="right" data-content="&lt;div class=&quot;no-overflow&quot;&gt;&lt;p&gt;The course summary is displayed in the list of courses. A course search searches course summary text in addition to course names.&lt;/p&gt;&lt;/div&gt; "
                        data-html="true" tabindex="0" data-trigger="focus">
                        <i class="icon fa fa-question-circle text-info fa-fw " aria-hidden="true" title="Help with Course summary" aria-label="Help with Course summary"></i>
                        </a>
                    </span>
                    <label class="col-form-label d-inline " for="id_summary_editor">
                        Course summary
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="editor">
                    <div>
                        <textarea id="id_summary_editor" name="summary_editor" class="form-control" rows="5" cols="80" spellcheck="true" ></textarea>
                    </div>
                    <div class="form-control-feedback" id="id_error_summary_editor">
                    </div>
                </div>
            </div>
            
            <input class="btn btn-info" type="submit" name="save" value="Save"/>
            <a class="btn btn-default" href="./select_course_edit.php">Cancel</a>

        </form>
        <?php
        if(!empty($_GET['course']) && !isset($_POST['save'])){
            $course = $DB->get_records_sql('SELECT * FROM  `mdl_course` WHERE id = ?', array($course_id));
            if($course){
                foreach($course as $c) {
                    $fullname = $c->fullname;
                    $shortname = $c->shortname;
                    $idnumber = $c->idnumber;
                    $summary = $c->summary;
                    $semesterid = $c->semesterid;
                }
            }
        }
        ?>
        <script>
            document.getElementById("id_fullname").value = <?php echo json_encode($fullname); ?>;
            document.getElementById("id_shortname").value = <?php echo json_encode($shortname); ?>;
            //document.getElementById("id_idnumber").value = <?php echo json_encode($idnumber); ?>;
            document.getElementById("id_summary_editor").value = <?php echo json_encode($summary); ?>;
            var dropDown = document.getElementById("id_semester");
            var dropDownVal = <?php echo json_encode($semesterid); ?>;
            for(var i=0; i < dropDown.options.length; i++)
            {
            if(dropDown.options[i].value == dropDownVal)
                dropDown.selectedIndex = i;
            }
        </script>
        <br />
        <div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>
        
        <script src="../script/datepicker/wbn-datepicker.min.js"></script>
        <script type="text/javascript">
            $(function () {
            $('.wbn-datepicker').datepicker()
        
            var $jsDatepicker = $('#value-specified-js').datepicker()
            $jsDatepicker.val('2017-05-30')
            })
        </script>
        <?php
    }
    else
    {
        echo "<h4 style='color: red'> Invalid Selection </h4><br>";
    }
    label:
    ?>
    <br>
    <a class="btn btn-default" href="./select_course_edit.php">Go Back</a>
    <?php
    echo $OUTPUT->footer();
    ?>
