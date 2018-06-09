<?php 
   require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Select Other Activity");
    $PAGE->set_heading("Select Other Activity");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/grading_form_other_selection.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    
    if(!empty($_GET['course']))
    {
        $course_id=$_GET['course'];
        $coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
        //echo "$course_id";
        $others= $DB->get_records_sql("SELECT * FROM mdl_manual_other WHERE courseid = ? AND module = ?",array($course_id,-6));

        if($others)
        {
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Assignment Name');
            foreach ($others as $other) {
                $serialno++;
                $aname = $other->name;
                $aid   = $other->id;
                
                $table->data[] = array($serialno,"<a href='./grading_form_other.php?other=$aid&courseid=$course_id'>$aname</a>");
            }
            echo html_writer::table($table);
            echo "<br />";
        }
        else
            echo "<font color = red> No Other Activity Found!</font>";
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
