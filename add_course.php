<link rel="stylesheet" href="./css/datepicker/wbn-datepicker.css">
<script src="./script/jquery/jquery-3.2.1.js"></script>
<?php
    require_once('../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Add Courses");
    $PAGE->set_heading("Add Courses");
    $PAGE->set_url($CFG->wwwroot.'/custom/add_course.php');
    
    echo $OUTPUT->header();
    require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());
  
    
    if((isset($_POST['submit']) && isset( $_POST['fwid'])) || (isset($SESSION->fid11) && $SESSION->fid11 != "xyz") || isset($_POST['save']) || isset($_POST['return']))
    {
        if(isset($_POST['submit']) || (isset($SESSION->fid11) && $SESSION->fid11 != "xyz")){
            if(isset($SESSION->fid11) && $SESSION->fid11 != "xyz")
            {
                $fw_id=$SESSION->fid11;
                $SESSION->fid11 = "xyz";
            }
            else
                $fw_id=$_POST['fwid'];
            $rec=$DB->get_records_sql('SELECT shortname from mdl_competency_framework WHERE id=?', array($fw_id));
            if($rec){
                foreach ($rec as $records){
                    $fw_shortname = $records->shortname;
                }
            }
        }
    
        if(isset($_POST['save'])){
            $fullname=trim($_POST['fullname']);
            $shortname=trim($_POST['shortname']);
            $idnumber=trim($_POST['idnumber']); $idnumber=strtoupper($idnumber);
            $startdate=strtotime($_POST['startdate']);
            $enddate=strtotime($_POST['enddate']);
            $summary=trim($_POST['summary_editor']);
            $fw_id=$_POST['fid'];
            $fw_shortname=$_POST['fname'];
            $time = time();
            
            if(empty($fullname) || empty($shortname) || empty($idnumber))
            {
                if(empty($fullname))
                {
                    $msg1="<font color='red'>-Please enter full name</font>";
                }
                if(empty($shortname))
                {
                    $msg2="<font color='red'>-Please enter short name</font>";
                }
                if(empty($idnumber))
                {
                    $msg3="<font color='red'>-Please enter course code</font>";
                }
            }/*
            elseif(substr($idnumber,0,4) != 'PEO-')
            {
                $msg3="<font color='red'>-The ID number must start with PEO-</font>";
            }*/
            else{
                /*$sql="INSERT INTO mdl_course (category, fullname, shortname, idnumber, summary, summaryformat, newsitems, startdate, enddate, timecreated, timemodified, enablecompletion)
                VALUES (1, '$fullname', '$shortname', '$idnumber', '$summary', 1, 5, '$startdate', '$enddate', '$time', '$time', 1)";
                $DB->execute($sql);*/
                
                $record = new stdClass();
                $record->category = 1;
                $record->fullname = $fullname;
                $record->shortname = $shortname;
                $record->idnumber = $idnumber;
                $record->summary = $summary;
                $record->summaryformat = 1;
                $record->newsitems = 5;
                $record->startdate = $startdate;
                $record->enddate = $enddate;
                $record->timecreated = $time;
                $record->timemodified = $time;
                $record->enablecompletion = 1;
                
                $courseid = $DB->insert_record('course', $record);
                $course=$DB->get_records_sql('SELECT * FROM `mdl_course` 
                WHERE id = ? ',
                array($courseid));
                if ($course != NULL){
                    foreach ($course as $rec) {
                        $id =  $rec->id;
                        $idnumber =  $rec->idnumber;
                    }
                }   
                $count=0;
                $competencies=$DB->get_records_sql("SELECT * FROM `mdl_competency` 
                WHERE idnumber like '{$idnumber}%' 
                AND competencyframeworkid =? ",
                array($fw_id));
                $flag=false;
                if ($competencies != NULL){
                    foreach ($competencies as $rec) {
                        $id =  $rec->id;
                        $idnumber =  $rec->idnumber;
                        //echo "$idnumber";
                        $check=$DB->get_records_sql("SELECT * FROM `mdl_competency_coursecomp`
                                    WHERE courseid = ?
                                    AND competencyid =? ",
                                    array($courseid,$id));
                        if ($check == NULL)
                        {   
                            $flag=true;
                        
                            $sql="INSERT INTO mdl_competency_coursecomp (courseid, competencyid,ruleoutcome,timecreated,timemodified,usermodified,sortorder) VALUES ('$courseid', '$id','1','$time','$time', '$USER->id','0')";
                            $DB->execute($sql);
                            
                        }
                    }
                    $msg4 = "<br><font color='green'><b>Course successfully created </b></font>";
                    $msg5="<p><b>Add another below.</b></p>";
                    if($flag == true)
                    {
                //     echo " <font color='green'>CLOs successfully mapped with the course </font>";
                        $msg4 .= "<font color='green'><b>& mapped with respective CLOs!</b></font><br />";
                    }
                
                }
                else 
                {   echo " <font color='red'>No CLOs of this course have been added to the framework</font>";
                    $msg4 = "<br><font color='green'><b>Course successfully created </b></font>";
                    $msg5="<p><b>Add another below.</b></p>";
                    goto end;
                }
            // if ($flag == false)
                //{
                //    echo " <font color='green'>CLOs are already mapped with the course </font>";
            // }
            end:
            }
        }
        elseif(isset($_POST['return'])){
            $fullname=trim($_POST['fullname']);
            $shortname=trim($_POST['shortname']);
            $idnumber=trim($_POST['idnumber']); $idnumber=strtoupper($idnumber);
            $startdate=strtotime($_POST['startdate']);
            $enddate=strtotime($_POST['enddate']);
            $summary=trim($_POST['summary_editor']);
            $fw_id=$_POST['fid'];
            $fw_shortname=$_POST['fname'];
            $time = time();
            
            if(empty($fullname) || empty($shortname) || empty($idnumber))
            {
                if(empty($fullname))
                {
                    $msg1="<font color='red'>-Please enter full name</font>";
                }
                if(empty($shortname))
                {
                    $msg2="<font color='red'>-Please enter short name</font>";
                }
                if(empty($idnumber))
                {
                    $msg3="<font color='red'>-Please enter course code</font>";
                }
            }/*
            elseif(substr($idnumber,0,4) != 'PEO-')
            {
                $msg3="<font color='red'>-The ID number must start with PEO-</font>";
            }*/
            else{
                /*$sql="INSERT INTO mdl_course (category, fullname, shortname, idnumber, summary, summaryformat, newsitems, startdate, enddate, timecreated, timemodified, enablecompletion)
                VALUES (1, '$fullname', '$shortname', '$idnumber', '$summary', 1, 5, '$startdate', '$enddate', '$time', '$time', 1)";
                $DB->execute($sql);*/
                
                $record = new stdClass();
                $record->category = 1;
                $record->fullname = $fullname;
                $record->shortname = $shortname;
                $record->idnumber = $idnumber;
                $record->summary = $summary;
                $record->summaryformat = 1;
                $record->newsitems = 5;
                $record->startdate = $startdate;
                $record->enddate = $enddate;
                $record->timecreated = $time;
                $record->timemodified = $time;
                $record->enablecompletion = 1;
                
                $courseid = $DB->insert_record('course', $record);
                $course=$DB->get_records_sql('SELECT * FROM `mdl_course` 
                    WHERE id = ? ',
                     array($courseid));
                if ($course != NULL){
                    foreach ($course as $rec) {
                        $id =  $rec->id;
                        $idnumber =  $rec->idnumber;
                    }
                }   
                $count=0;
                $competencies=$DB->get_records_sql("SELECT * FROM `mdl_competency` 
                WHERE idnumber like '{$idnumber}%' 
                AND competencyframeworkid =? ",
                array($fw_id));
                $flag=false;
                if ($competencies != NULL){
                    foreach ($competencies as $rec) {
                        $id =  $rec->id;
                        $idnumber =  $rec->idnumber;
                        //echo "$idnumber";
                        $check=$DB->get_records_sql("SELECT * FROM `mdl_competency_coursecomp`
                                    WHERE courseid = ?
                                    AND competencyid =? ",
                                    array($courseid,$id));
                        if ($check == NULL)
                        {   
                            $flag=true;
                        
                            $sql="INSERT INTO mdl_competency_coursecomp (courseid, competencyid,ruleoutcome,timecreated,timemodified,usermodified,sortorder) VALUES ('$courseid', '$id','1','$time','$time', '$USER->id','0')";
                            $DB->execute($sql);
                            
                        }
                    }
                    $msg4 = "<br><font color='green'><b>Course successfully created </b></font>";
                    $msg5="<p><b>Add another below.</b></p>";
                    if($flag == true)
                    {
                        //echo " <font color='green'>CLOs successfully mapped with the course </font>";
                        $msg4 .= "<font color='green'><b>& mapped with respective CLOs!</b></font><br />";
                    }

                    $redirect_page1='./report_main.php';
                    redirect($redirect_page1);
            
                }
                else 
                {   
                    echo " <font color='red'>No CLOs of this course have been added to the framework</font>";
                    $msg4 = "<br><font color='green'><b>Course successfully created </b></font>";
                    $msg5="<p><b>Add another below.</b></p>";
                    goto end2;
                }
                // if ($flag == false)
                //{
                //    echo " <font color='green'>CLOs are already mapped with the course </font>";
                // }
        
            end2:
            }
        }

        if(isset($msg4)){
            echo $msg4;
            echo $msg5;
        }
        
        ?>
        <br />
        <h3>Add New Course</h3>
        <form method='post' action="" class="mform">

            <div class="form-group row fitem">
                <div class="col-md-3">
                    <label class="col-form-label d-inline" for="id_framework">
                        OBE framework
                    </label>
                </div>
                <div class="col-md-9 form-inline felement">
                    <?php echo $fw_shortname; ?>
                </div>
            </div>
            
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
                            maxlength="254" type="text" >
                    <div class="form-control-feedback" id="id_error_fullname"  style="display: none;">
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
                            maxlength="100" type="text">
                    <div class="form-control-feedback" id="id_error_shortname" style="display: none;">
                    <?php
                    if(isset($msg2)){
                        echo $msg2;
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
                    <input type="text"
                            class="form-control "
                            name="idnumber"
                            id="id_idnumber"
                            pattern="[a-zA-Z]{2}-[0-9]{3}"
                            title="eg. CS-304"
                            value=""
                            required
                            placeholder="eg. CS-304"
                            size="10"
                            maxlength="20" type="text" > (eg. CS-304)
                    <div class="form-control-feedback" id="id_error_idnumber"  style="display: none;">
                    <?php
                    if(isset($msg3)){
                        echo $msg3;
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
                        class="form-control wbn-datepicker"
                        name="startdate"
                        id="id_startdate"
                        size="27"
                        maxlength="100" >
                    <div class="form-control-feedback" id="id_error_idnumber"  style="display: none;">
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
                        class="form-control wbn-datepicker"
                        name="enddate"
                        id="id_enddate"
                        data-start-src="id_startdate"
                        size="27"
                        maxlength="100" >
                    <div class="form-control-feedback" id="id_error_idnumber"  style="display: none;">
                    </div>
                </div>
            </div>
            
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
                    <div class="form-control-feedback" id="id_error_summary_editor"  style="display: none;">
                    </div>
                </div>
            </div>
            
            <input type="hidden" name="fname" value="<?php echo $fw_shortname; ?>"/>
            <input type="hidden" name="fid" value="<?php echo $fw_id; ?>"/>
            <input class="btn btn-info" type="submit" name="save" value="Save and continue"/>
            <input class="btn btn-info" type="submit" name="return" value="Save and return"/>
            <a class="btn btn-default" type="submit" href="./select_frameworktoCourse.php">Cancel</a>

        </form>
        <?php
        if(isset($_POST['save']) && !isset($msg4)){
        ?>
        <script>
            document.getElementById("id_fullname").value = <?php echo json_encode($fullname); ?>;
            document.getElementById("id_shortname").value = <?php echo json_encode($shortname); ?>;
            document.getElementById("id_idnumber").value = <?php echo json_encode($idnumber); ?>;
            document.getElementById("id_summary_editor").value = <?php echo json_encode($summary); ?>;
        </script>
        <?php
        }
        ?>
        <br />
        <div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>
                        
        <?php 
            echo $OUTPUT->footer();
        ?>
        
            <script src="./script/datepicker/wbn-datepicker.min.js"></script>
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
    {?>
        <h3 style="color:red;"> Invalid Selection </h3>
        <a href="./select_frameworktoCourse.php">Back</a>
        <?php
        echo $OUTPUT->footer();
    }?>