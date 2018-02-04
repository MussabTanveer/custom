<?php 
   require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Empty Grading Sheet");
    $PAGE->set_heading("Empty Grading Sheet");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/print_grading_sheet.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    if(!empty($_GET['quiz']) && !empty($_GET['course']))
    {
        $course_id=$_GET['course'];
        $quiz_id=$_GET['quiz'];

        // Get all students of course
        $recStudents=$DB->get_records_sql("SELECT u.id AS sid, u.username AS seatnum, u.firstname, u.lastname
        FROM mdl_role_assignments ra, mdl_user u, mdl_course c, mdl_context cxt
        WHERE ra.userid = u.id
        AND ra.contextid = cxt.id
        AND cxt.contextlevel = ?
        AND cxt.instanceid = c.id
        AND c.id = ?
        AND (roleid=5)", array(50, $course_id));

        // push student ids and seat nums to array
        $stdids = array();
        $seatnos = array();
        foreach($recStudents as $records){
            $id = $records->sid;
            $seatno = $records->seatnum ;
            array_push($stdids,$id);
            array_push($seatnos,$seatno);
        }

        // print all students enrolled in this course
        echo "STUDENTS<br>";
        for($i=0; $i<count($seatnos); $i++)
            echo $seatnos[$i]."<br>";
        
        // Get all questions of quiz
        $recQues=$DB->get_records_sql('SELECT * FROM mdl_manual_quiz_question WHERE mquizid = ?', array($quiz_id));

        // push question ids and names to array
        $quesids = array();
        $quesnames = array();
        foreach($recQues as $records){
            $id = $records->id;
            $name = $records->quesname ;
            array_push($quesids,$id);
            array_push($quesnames,$name);
        }

        // print all questions of this quiz
        echo "<br>QUESTIONS<br>";
        for($i=0; $i<count($quesnames); $i++)
            echo $quesnames[$i]."<br>";

        // Now export this data to excel file

    }
    else
	{?>
		<h3 style="color:red;"> Invalid Selection </h3>
    	<a href="../index.php">Back</a>
    	<?php
    }

    echo $OUTPUT->footer();
    
?>