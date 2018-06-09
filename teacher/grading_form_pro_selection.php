<?php 
   require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Select Project");
    $PAGE->set_heading("Select Project");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/grading_form_pro_selection.php');
    
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
        $assigns= $DB->get_records_sql("SELECT * FROM mdl_manual_assign_pro WHERE courseid = ? AND module = ?",array($course_id,-5));

        if($assigns)
        {
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Project Name');
            foreach ($assigns as $assign) {
                $serialno++;
                $aname = $assign->name;
                $aid   = $assign->id;
                
                $table->data[] = array($serialno,"<a href='./grading_form_assignpro.php?type=project&as_pro=$aid&courseid=$course_id'>$aname</a>");
            }
            echo html_writer::table($table);
            echo "<br />";
        }
        else
            echo "<p style='color:red;'> No Project Found!</p>";
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
