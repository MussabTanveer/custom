<script src="../script/jquery/jquery-3.2.1.js"></script>

<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Empty Grading Sheet");
    $PAGE->set_heading("Print Empty Grading Sheet");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/print_grading_sheet_assign_pro.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    ?>
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

        if($type=="assign"){
            echo "<h3>Choose Assignment</h3><br>";
            $mod=-4;
        }
        elseif($type=="project"){
            echo "<h3>Choose Project</h3><br>";
            $mod=-5;
        }
        
        $rec=$DB->get_records_sql('SELECT * FROM mdl_manual_assign_pro WHERE courseid = ? AND module = ?', array($course_id,$mod));
        
        if($rec){
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Activity Name');
            foreach ($rec as $records) {
                $serialno++;
                $id = $records->id;
                $name = $records->name;
                
                $table->data[] = array($serialno,"<a href='./export2.php?id=$id&course=$course_id'>$name</a>");
            }
            
            echo html_writer::table($table);
            echo "<br />";
        }
        else{
            echo "<h3>You do not have any manual $type in this course!</h3>";
        }
    }
	else
	{?>
		<h3 style="color:red;"> Invalid Selection </h3>
    	<a href="./teacher_courses.php">Back</a>
    	<?php
    }
    echo $OUTPUT->footer();
?>
