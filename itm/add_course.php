<link rel="stylesheet" href="../css/datepicker/wbn-datepicker.css">
<script src="../script/jquery/jquery-3.2.1.js"></script>
<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Add Courses");
    $PAGE->set_heading("Add Courses");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/itm/add_course.php');
    
    require_login();
    if($SESSION->oberole != "itm"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
  
    
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
                    $msg3="<font color='red'>-Please select course code</font>";
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

                $record1 = new stdClass();
                $record1->courseid = $courseid;
                $record1->format = 'topics';
                $record1->sectionid = 0;
                $record1->name = 'hiddensections';
                $record1->value = 0;
                $record2 = new stdClass();
                $record2->courseid = $courseid;
                $record2->format = 'topics';
                $record2->sectionid = 0;
                $record2->name = 'coursedisplay';
                $record2->value = 0;

                $records = array($record1, $record2);
                $DB->insert_records('course_format_options', $records);

                $record1 = new stdClass();
                $record1->course = $courseid;
                $record1->section = 0;
                $record1->summary = '';
                $record1->summaryformat = 1;
                $record1->sequence = "";
                $record1->visible = 1;
                $record2 = new stdClass();
                $record2->course = $courseid;
                $record2->section = 1;
                $record2->summary = '';
                $record2->summaryformat = 1;
                $record2->sequence = "";
                $record2->visible = 1;
                $record3 = new stdClass();
                $record3->course = $courseid;
                $record3->section = 2;
                $record3->summary = '';
                $record3->summaryformat = 1;
                $record3->sequence = "";
                $record3->visible = 1;
                $record4 = new stdClass();
                $record4->course = $courseid;
                $record4->section = 3;
                $record4->summary = '';
                $record4->summaryformat = 1;
                $record4->sequence = "";
                $record4->visible = 1;
                $record5 = new stdClass();
                $record5->course = $courseid;
                $record5->section = 4;
                $record5->summary = '';
                $record5->summaryformat = 1;
                $record5->sequence = "";
                $record5->visible = 1;
                
                $records = array($record1, $record2, $record3, $record4, $record5);
                $DB->insert_records('course_sections', $records);

                $record1 = new stdClass();
                $record1->enrol = "manual";
                $record1->status = 0;
                $record1->courseid = $courseid;
                $record1->sortorder = 0;
                $record1->expirythreshold = 86400;
                $record1->roleid = 5;
                $record1->customint1 = NULL;
                $record1->customint2 = NULL;
                $record1->customint3 = NULL;
                $record1->customint4 = NULL;
                $record1->customint5 = NULL;
                $record1->customint6 = NULL;
                $record1->timecreated = $time;
                $record1->timemodified = $time;
                $record2 = new stdClass();
                $record2->enrol = "guest";
                $record2->status = 1;
                $record2->courseid = $courseid;
                $record2->sortorder = 1;
                $record2->expirythreshold = 0;
                $record2->roleid = 0;
                $record2->customint1 = NULL;
                $record2->customint2 = NULL;
                $record2->customint3 = NULL;
                $record2->customint4 = NULL;
                $record2->customint5 = NULL;
                $record2->customint6 = NULL;
                $record2->timecreated = $time;
                $record2->timemodified = $time;
                $record3 = new stdClass();
                $record3->enrol = "self";
                $record3->status = 1;
                $record3->courseid = $courseid;
                $record3->sortorder = 2;
                $record3->expirythreshold = 86400;
                $record3->roleid = 5;
                $record3->customint1 = 0;
                $record3->customint2 = 0;
                $record3->customint3 = 0;
                $record3->customint4 = 1;
                $record3->customint5 = 0;
                $record3->customint6 = 1;
                $record3->timecreated = $time;
                $record3->timemodified = $time;
                
                $records = array($record1, $record2, $record3);
                $DB->insert_records('enrol', $records);
                
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


                 $competenciesRev=$DB->get_records_sql("SELECT * FROM `mdl_competency` 
                                WHERE idnumber = ? 
                                AND competencyframeworkid =? ",
                                array($idnumber,$fw_id));


                 foreach ($competenciesRev as $competencyRev) {
                   // echo "Working";
                        $id =  $competencyRev->id;
                        $idnumber =  $competencyRev->idnumber;
                        //echo "$idnumber";
                    }


                   
                        $check=$DB->get_records_sql("SELECT * FROM `mdl_competency_coursecomp`
                                    WHERE courseid = ?
                                    AND competencyid =? ",
                                    array($courseid,$id));
                        if ($check == NULL)
                        {   echo "$id<br>";
                            $flag=true;
                        
                            $sql="INSERT INTO mdl_competency_coursecomp (courseid, competencyid,ruleoutcome,timecreated,timemodified,usermodified,sortorder) VALUES ('$courseid', '$id','1','$time','$time', '$USER->id','0')";
                            $DB->execute($sql);
                        }




                    }
                    $msg4 = "<br><font color='green'><b>Course successfully created </b></font>";
                    $msg5="<p><b>Add another below.</b></p>";
                    if($flag == true)
                    {
                        // echo " <font color='green'>CLOs successfully mapped with the course </font>";
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
                    $msg3="<font color='red'>-Please select course code</font>";
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
                
                $record1 = new stdClass();
                $record1->courseid = $courseid;
                $record1->format = 'topics';
                $record1->sectionid = 0;
                $record1->name = 'hiddensections';
                $record1->value = 0;
                $record2 = new stdClass();
                $record2->courseid = $courseid;
                $record2->format = 'topics';
                $record2->sectionid = 0;
                $record2->name = 'coursedisplay';
                $record2->value = 0;

                $records = array($record1, $record2);
                $DB->insert_records('course_format_options', $records);

                $record1 = new stdClass();
                $record1->course = $courseid;
                $record1->section = 0;
                $record1->summary = '';
                $record1->summaryformat = 1;
                $record1->sequence = "";
                $record1->visible = 1;
                $record2 = new stdClass();
                $record2->course = $courseid;
                $record2->section = 1;
                $record2->summary = '';
                $record2->summaryformat = 1;
                $record2->sequence = "";
                $record2->visible = 1;
                $record3 = new stdClass();
                $record3->course = $courseid;
                $record3->section = 2;
                $record3->summary = '';
                $record3->summaryformat = 1;
                $record3->sequence = "";
                $record3->visible = 1;
                $record4 = new stdClass();
                $record4->course = $courseid;
                $record4->section = 3;
                $record4->summary = '';
                $record4->summaryformat = 1;
                $record4->sequence = "";
                $record4->visible = 1;
                $record5 = new stdClass();
                $record5->course = $courseid;
                $record5->section = 4;
                $record5->summary = '';
                $record5->summaryformat = 1;
                $record5->sequence = "";
                $record5->visible = 1;
                
                $records = array($record1, $record2, $record3, $record4, $record5);
                $DB->insert_records('course_sections', $records);

                $record1 = new stdClass();
                $record1->enrol = "manual";
                $record1->status = 0;
                $record1->courseid = $courseid;
                $record1->sortorder = 0;
                $record1->expirythreshold = 86400;
                $record1->roleid = 5;
                $record1->customint1 = NULL;
                $record1->customint2 = NULL;
                $record1->customint3 = NULL;
                $record1->customint4 = NULL;
                $record1->customint5 = NULL;
                $record1->customint6 = NULL;
                $record1->timecreated = $time;
                $record1->timemodified = $time;
                $record2 = new stdClass();
                $record2->enrol = "guest";
                $record2->status = 1;
                $record2->courseid = $courseid;
                $record2->sortorder = 1;
                $record2->expirythreshold = 0;
                $record2->roleid = 0;
                $record2->customint1 = NULL;
                $record2->customint2 = NULL;
                $record2->customint3 = NULL;
                $record2->customint4 = NULL;
                $record2->customint5 = NULL;
                $record2->customint6 = NULL;
                $record2->timecreated = $time;
                $record2->timemodified = $time;
                $record3 = new stdClass();
                $record3->enrol = "self";
                $record3->status = 1;
                $record3->courseid = $courseid;
                $record3->sortorder = 2;
                $record3->expirythreshold = 86400;
                $record3->roleid = 5;
                $record3->customint1 = 0;
                $record3->customint2 = 0;
                $record3->customint3 = 0;
                $record3->customint4 = 1;
                $record3->customint5 = 0;
                $record3->customint6 = 1;
                $record3->timecreated = $time;
                $record3->timemodified = $time;
                
                $records = array($record1, $record2, $record3);
                $DB->insert_records('enrol', $records);

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


                         $competenciesRev=$DB->get_records_sql("SELECT * FROM `mdl_competency` 
                                WHERE idnumber = ? 
                                AND competencyframeworkid =? ",
                                array($idnumber,$fw_id));


                 foreach ($competenciesRev as $competencyRev) {
                   // echo "Working";
                        $id =  $competencyRev->id;
                        $idnumber =  $competencyRev->idnumber;
                        //echo "$idnumber";
                    }





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

                    $redirect_page1='../index.php';
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

        $courseCodes=$DB->get_records_sql("SELECT DISTINCT idnumber FROM mdl_competency WHERE competencyframeworkid = ? AND idnumber NOT LIKE 'PLO%' AND parentid !=0 ORDER BY idnumber", array($fw_id));
        $ccs = array(); // course codes array
        foreach ($courseCodes as $cc) {
            $cCode = $cc->idnumber; $cCode = substr($cCode,0,6);
            array_push($ccs, $cCode);
        }
        $ccs = array_unique($ccs); // remove duplicate course codes

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
                    <!--
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
                    -->
                    <div class="form-control-feedback" id="id_error_idnumber">
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
                            placeholder="eg. Software Engineering - Spring - 18"
                            size="50"
                            maxlength="254" type="text" >
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
                            maxlength="100" type="text">
                    <div class="form-control-feedback" id="id_error_shortname">
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
            //document.getElementById("id_idnumber").value = <?php echo json_encode($idnumber); ?>;
            document.getElementById("id_summary_editor").value = <?php echo json_encode($summary); ?>;
            var dropDown = document.getElementById("id_idnumber");
            var dropDownVal = <?php echo json_encode($idnumber); ?>;
            for(var i=0; i < dropDown.options.length; i++)
            {
            if(dropDown.options[i].value == dropDownVal)
            dropDown.selectedIndex = i;
            }
        </script>
        <?php
        }
        ?>
        <br />
        <div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>
                        
        <?php 
            echo $OUTPUT->footer();
        ?>
        
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
    {?>
        <h3 style="color:red;"> Invalid Selection </h3>
        <a href="./select_frameworktoCourse.php">Back</a>
        <?php
        echo $OUTPUT->footer();
    }?>