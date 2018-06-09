<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Choose Activity");
    $PAGE->set_heading("Choose Activity");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/view_quiz.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/cool-link/style.css" />
    <?php
    if(!empty($_GET['type']) && !empty($_GET['course']))
    {
        $course_id=$_GET['course'];
		//echo "Course ID : $course_id";
		$course_id = (int)$course_id; // convert course id from string to int
        //echo gettype($course_id), "\n";
        $coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
        $type=$_GET['type'];
        //echo " Activity Type : $type";

        if($type=="quiz"){
            echo "<h3>Quiz</h3><br>";
            $mod=-1;
        }
        elseif($type=="midterm"){
            echo "<h3>Midterm</h3><br>";
            $mod=-2;
        }
        elseif($type=="finalexam"){
            echo "<h3>Final Exam</h3><br>";
            $mod=-3;
        }
        
        $rec=$DB->get_records_sql('SELECT * FROM mdl_manual_quiz WHERE courseid = ? AND module = ?', array($course_id,$mod));
        
        if($rec){
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Activity Name');
            foreach ($rec as $records) {
                $serialno++;
                $id = $records->id;
                $name = $records->name;
                
                $table->data[] = array($serialno,"<a href='./upload_marks.php?id=$id&course=$course_id'>$name</a>");
            }
            
            echo html_writer::table($table);
            echo "<br />";
        }
        else{
            echo "<h3>You do not have any manual $type in this course!</h3>";
        }
        ?>
        <a class="btn btn-default" href="./report_teacher.php?course=<?php echo $course_id ?>">Go Back</a>
        <?php
    }
	else
	{?>
		<h3 style="color:red;"> Invalid Selection </h3>
    	<a href="./teacher_courses.php">Back</a>
    	<?php
    }
    echo $OUTPUT->footer();
?>
