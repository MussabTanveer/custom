<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("My Activities");
    $PAGE->set_heading("Activities");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/student/display_activities_student.php');
    
    require_login();
    if($SESSION->oberole != "student"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <?php
    if((isset($_POST['submit']) && isset( $_POST['courseid'])) || (isset($SESSION->cid4) && $SESSION->cid4 != "xyz"))
    {
        if(isset($SESSION->cid4) && $SESSION->cid4 != "xyz")
        {
            $course_id=$SESSION->cid4;
            $SESSION->cid4 = "xyz";
        }
        else
            $course_id=$_POST['courseid'];
        //echo "Course ID : $course_id";

        echo "<h4 style='color:navy'>Note: Only <span style='color:green'>VIEWED</span> activities can be added to the progress report.</h4><br>";

        // Dispaly all quizzes
        //$rec=$DB->get_records_sql('SELECT * FROM  `mdl_quiz` WHERE course = ? AND timeopen != ?', array($course_id, 0));
        $recQ=$DB->get_records_sql('SELECT q.id, q.course, q.name, q.intro 
		FROM  mdl_quiz q, mdl_quiz_attempts qa
		WHERE q.course = ? AND q.id=qa.quiz AND qa.userid = ?',
        array($course_id,$USER->id));
        
        $recA=$DB->get_records_sql('SELECT a.id, a.course, a.name, a.intro 
		FROM  mdl_assign a, mdl_assign_grades ag
		WHERE a.course = ? AND a.id=ag.assignment AND ag.userid = ? AND ag.grade != ?',
		array($course_id,$USER->id,-1));



        $statusQuery=$DB->get_records_sql('SELECT DISTINCT instance, module FROM `mdl_consolidated_report_student` WHERE course = ? AND userid = ? AND form =?', array($course_id,$USER->id,'online'));

        $statusArray = array();
        $modArray = array();

        foreach ($statusQuery as $state) {
           
            $sta = $state->instance;
            $mod = $state ->module;
          
            array_push($statusArray, $sta);
            array_push($modArray, $mod);
        }


      //  var_dump($statusArray);
      //  var_dump($modArray);


        
	    if($recQ || $recA){
            ?>
            <form method='post' action='activity_comp_report_student.php' id="form_check">
            <?php
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Name', 'Intro', 'Select','Status');
            foreach ($recQ as $records) {
                $serialno++;

                $Status='<span style="color: red;">NOT VIEWED</span>';
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
                $table->data[] = array($serialno, $name, $intro, '<input type="radio" value="'.$id.'" name="activityid">',$Status);
            }
            foreach ($recA as $records) {
                $serialno++;

                $Status='<span style="color: red;">NOT VIEWED</span>';
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
                $table->data[] = array($serialno, $name, $intro, '<input type="radio" value="'.$id.'" name="activityid">',$Status);
            }
            echo html_writer::table($table);
            ?>
            <input type="hidden" value='<?php echo $course_id; ?>' name="courseid">
            <input type="submit" value="NEXT" name="submit" class="btn btn-primary">
            </form>
            <br />
            <p id="msg"></p>
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
           // echo $OUTPUT->footer();
        }
        else{
            echo "<h3>No Online Activity found!</h3>";
            echo $OUTPUT->footer();
        }




        //Manual Activites

        $recQ=$DB->get_records_sql('SELECT q.id, q.courseid, q.name, q.description 
        FROM  mdl_manual_quiz q, mdl_manual_quiz_attempt qa
        WHERE q.courseid = ? AND q.id=qa.quizid AND qa.userid = ?',
        array($course_id,$USER->id));
        
        $recA=$DB->get_records_sql('SELECT a.id, a.courseid, a.name, a.description 
        FROM  mdl_manual_assign_pro a, mdl_manual_assign_pro_attempt ag
        WHERE a.courseid = ? AND a.id=ag.assignproid AND ag.userid = ?',
        array($course_id,$USER->id));



        $statusQuery=$DB->get_records_sql('SELECT DISTINCT instance, module FROM `mdl_consolidated_report_student` WHERE course = ? AND userid = ? AND form = ?', array($course_id,$USER->id,'manual'));

        $statusArray = array();
        $modArray = array();

        foreach ($statusQuery as $state) {
           
            $sta = $state->instance;
            $mod = $state ->module;
          
            array_push($statusArray, $sta);
            array_push($modArray, $mod);
        }


      //  var_dump($statusArray);
      //  var_dump($modArray);


        
        if($recQ || $recA){
            ?>
            <form method='post' action='manual_activity_comp_report_student.php' id="form_check">
            <?php
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Name', 'Intro', 'Select','Status');
            foreach ($recQ as $records) {
                $serialno++;

                $Status='<span style="color: red;">NOT VIEWED</span>';
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
                $courseid = $records->courseid;
                $name = $records->name;
                $intro = $records->description;
                $table->data[] = array($serialno, $name, $intro, '<input type="radio" value="'.$id.'" name="activityid">',$Status);
            }
            foreach ($recA as $records) {
                $serialno++;

                $Status='<span style="color: red;">NOT VIEWED</span>';
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
                $courseid = $records->courseid;
                $name = $records->name;
                $intro = $records->description;
                $table->data[] = array($serialno, $name, $intro, '<input type="radio" value="'.$id.'" name="activityid">',$Status);
            }
            echo html_writer::table($table);
            ?>
            <input type="hidden" value='<?php echo $course_id; ?>' name="courseid">
            <input type="submit" value="NEXT" name="submit" class="btn btn-primary">
            </form>
            <br />
            <p id="msg"></p>
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
            echo $OUTPUT->footer();
        }
        else{
            echo "<h3>No Manual Activity found!</h3>";
            echo $OUTPUT->footer();
        }


    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./display_courses_student.php">Back</a>
    <?php
        echo $OUTPUT->footer();
    }?>
