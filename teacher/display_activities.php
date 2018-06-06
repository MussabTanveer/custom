<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("My Activities");
    $PAGE->set_heading("Activities");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/display_activities.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <script>
    if(performance.navigation.type == 2){
    location.reload(true);
    }
    </script>
    <?php

    /*if((isset($_POST['submit']) && isset( $_POST['courseid'])) || (isset($SESSION->cid3) && $SESSION->cid3 != "xyz"))
    {
        if(isset($SESSION->cid3) && $SESSION->cid3 != "xyz")
        {
            $course_id=$SESSION->cid3;
            $SESSION->cid3 = "xyz";
        }
        else
            $course_id=$_POST['courseid'];*/

    if(!empty($_GET['course']))
    {
        $course_id=$_GET['course'];
        $coursecontext = context_course::instance($course_id);
		is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
        
        //echo "Course ID : $course_id";

        // Dispaly all quizzes
        $recQ=$DB->get_records_sql('SELECT * FROM  `mdl_quiz` WHERE course = ? AND id IN (SELECT quiz FROM `mdl_quiz_attempts`)', array($course_id));
        $recA=$DB->get_records_sql('SELECT * FROM `mdl_assign` WHERE course = ? AND id IN (SELECT assignment FROM `mdl_assign_grades`)', array($course_id));
        $statusQuery=$DB->get_records_sql('SELECT id, instance, module FROM `mdl_consolidated_report` WHERE course = ? AND form = ?', array($course_id,"online"));

        $statusArray = array();
        $modArray = array();

        foreach ($statusQuery as $state) {
           
            $sta = $state->instance;
            $mod = $state ->module;
           
            array_push($statusArray, $sta);
            array_push($modArray, $mod);
        }
                
        if($recQ || $recA){
            ?>
            <!--<form method='post' action='activity_comp_report.php' id="form_check">-->
            <?php
            $serialno = 0;
            $table = new html_table();
            echo "<h3>Online Activities</h3>";
            $table->head = array('S. No.', 'Name', 'Intro', 'Status');
            foreach ($recQ as $records) {
                $serialno++;
                $Status='<span style="color: red;">PENDING</span>';
                $id = $records->id;

                for ($i=0; $i< sizeof($statusArray); $i++ )
                {

                      if($id == $statusArray[$i] && $modArray[$i] == 16)
                        {
                            $Status='<span style="color: #006400;">VIEWED</span>';
                            break;
                        }

                }
                
                $id = 'Q'.$records->id;
                $courseid = $records->course;
                $name = $records->name;
                $intro = $records->intro;
                
                $table->data[] = array($serialno, "<a href='./activity_comp_report.php?course=$course_id&activityid=$id'>$name</a>", "<a href='./activity_comp_report.php?course=$course_id&activityid=$id'>$intro</a>", $Status);
            }
            foreach ($recA as $records) {
                $serialno++;
                $Status='<span style="color: red;">PENDING</span>';
                $id = $records->id;

                for ($i=0; $i< sizeof($statusArray); $i++ )
                {

                      if($id == $statusArray[$i] && $modArray[$i] == 1)
                        {
                            $Status='<span style="color: #006400;">VIEWED</span>';
                            break;
                        }

                }
                $id = 'A'.$records->id;
                $courseid = $records->course;
                $name = $records->name;
                $intro = $records->intro;
                $table->data[] = array($serialno, "<a href='./activity_comp_report.php?course=$course_id&activityid=$id'>$name</a>", "<a href='./activity_comp_report.php?course=$course_id&activityid=$id'>$intro</a>", $Status);
            }
			
            echo html_writer::table($table);
            ?>
            <!--<input type="hidden" value='<?php echo $course_id; ?>' name="courseid">
			<input type="submit" value="NEXT" name="submit" class="btn btn-primary">
            </form>-->
            <br />
            <p id="msg"></p>
            <br />
            

            <script>
            $('#form_check').on('submit', function (e) {
                if ($("input[type=radio]:checked").length === 0) {
                    e.preventDefault();
                    $("#msg").html("<font color='red'>Select any one activity!</font>");
                    return false;
                }
            });
            </script>

            <?php
        }
        else{
            echo "<h3>No Online activity found!</h3>";
        }



        $recQ=$DB->get_records_sql('SELECT * FROM  `mdl_manual_quiz` WHERE courseid = ? AND id IN (SELECT quizid FROM `mdl_manual_quiz_attempt`)', array($course_id));
        $recA=$DB->get_records_sql('SELECT * FROM `mdl_manual_assign_pro` WHERE courseid = ? AND id IN (SELECT assignproid FROM `mdl_manual_assign_pro_attempt`)', array($course_id));
        $statusQuery=$DB->get_records_sql('SELECT id, instance, module FROM `mdl_consolidated_report` WHERE course = ? AND form = ?', array($course_id,"manual"));

        $mstatusarray = array();
        $mmodArray = array();

        foreach ($statusQuery as $state) {
           
            $sta = $state->instance;
            $mod = $state ->module;
           
            array_push($mstatusarray, $sta);
            array_push($mmodArray, $mod);
        }
                
        if($recQ || $recA){
            ?>
            <!--<form method='post' action='manual_activity_comp_report.php' id="form_check2">-->
            <?php
            $serialno = 0;
            $table = new html_table();
            echo "<h3>Manual Activities</h3>";
            $table->head = array('S. No.', 'Name', 'Intro', 'Status');
            foreach ($recQ as $records) {
                $serialno++;
                $Status='<span style="color: red;">PENDING</span>';
                $id = $records->id;

                for ($i=0; $i< sizeof($mstatusarray); $i++ )
                {

                      if($id == $mstatusarray[$i] && $mmodArray[$i] == 16)
                        {
                            $Status='<span style="color: #006400;">VIEWED</span>';
                            break;
                        }

                }
                
                $id = 'Q'.$records->id;
                $courseid = $records->courseid;
                $name = $records->name;
                $intro = $records->description;
                
                $table->data[] = array($serialno, "<a href='./manual_activity_comp_report.php?course=$course_id&activityid=$id'>$name</a>", "<a href='./manual_activity_comp_report.php?course=$course_id&activityid=$id'>$intro</a>", $Status);
            }
            foreach ($recA as $records) {
                $serialno++;
                $Status='<span style="color: red;">PENDING</span>';
                $id = $records->id;

                for ($i=0; $i< sizeof($mstatusarray); $i++ )
                {

                      if($id == $mstatusarray[$i] && $mmodArray[$i] == 1)
                        {
                            $Status='<span style="color: #006400;">VIEWED</span>';
                            break;
                        }

                }
                $id = 'A'.$records->id;
                $courseid = $records->courseid;
                $name = $records->name;
                $intro = $records->description;
                $table->data[] = array($serialno, "<a href='./manual_activity_comp_report.php?course=$course_id&activityid=$id'>$name</a>", "<a href='./manual_activity_comp_report.php?course=$course_id&activityid=$id'>$intro</a>", $Status);
            }
            
            echo html_writer::table($table);
            ?>
            <!--<input type="hidden" value='<?php echo $course_id; ?>' name="courseid">
            <input type="submit" value="NEXT" name="submit" class="btn btn-primary">
            </form>-->
            <br />
            <p id="msg2"></p>
            <br />
            <!--<form method='post' action='consolidated_report_selection.php'>
                <input type="hidden" value='<?php echo $course_id; ?>' name="courseid">
                <input type="submit" value="View Consolidated Report" name="view_consolidated" class="btn btn-secondary">
            </form>-->
            

            <script>
            $('#form_check2').on('submit', function (e) {
                if ($("input[type=radio]:checked").length === 0) {
                    e.preventDefault();
                    $("#msg2").html("<font color='red'>Select any one activity!</font>");
                    return false;
                }
            });
            </script>

            <?php
        }
        else{
            echo "<h3>No Manual activity found!</h3>";
        }

        ?>

        <a style="margin-top: 20px" href="consolidated_report_selection.php?course=<?php echo $course_id; ?>" class="btn btn-success">View Consolidated Report</a>

        <?php

        echo "<br><br><a href='./report_teacher.php?course=$course_id'>Back</a>";
        
    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./teacher_courses.php">Back</a>
    <?php
    }
    echo $OUTPUT->footer();
    ?>
