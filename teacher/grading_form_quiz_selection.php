<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Select Quiz");
    $PAGE->set_heading("Select Quiz");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/grading_form_quiz_selection.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    if(!empty($_GET['type']) && !empty($_GET['course']))
    {
        $course_id=$_GET['course'];
        $coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
        // echo "$course_id";
        $type=$_GET['type'];
        //echo " Activity Type : $type";
        
        $quizzes= $DB->get_records_sql("SELECT * FROM mdl_manual_quiz WHERE courseid = ? AND module = ?",array($course_id,-1));

        if($quizzes)
        {
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Quiz Name');
            foreach ($quizzes as $records) {
                $serialno++;
                $qid = $records->id;
                $qname = $records->name;
                
                $table->data[] = array($serialno,"<a href='./grading_form.php?quiz=$qid&courseid=$course_id'>$qname</a>");
            }
            echo html_writer::table($table);
            echo "<br />";
        }
        else
            echo "<h3>You do not have any manual $type in this course!</h3>";
    }
    else
	{?>
		<h3 style="color:red;"> Invalid Selection </h3>
    	<a href="./teacher_courses.php">Back</a>
    	<?php
    }

    echo $OUTPUT->footer();
    
?>