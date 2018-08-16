<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Select Final Exam");
    $PAGE->set_heading("Select Final Exam");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/view_final_result.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    if(!empty($_GET['type']) && !empty($_GET['course']))
    {
        $course_id=$_GET['course'];
        // echo "$course_id";
        $coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die($OUTPUT->header().'<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
        $type=$_GET['type'];
        //echo " Activity Type : $type";
        
        $finals= $DB->get_records_sql("SELECT * FROM mdl_manual_quiz WHERE courseid = ? AND module = ?",array($course_id,-3));

        if($finals)
        {
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Final Exam Name','Delete');
            foreach ($finals as $records) {
                $serialno++;
                $qid = $records->id;
                $qname = $records->name;
                
                $table->data[] = array($serialno,"<a href='./view_activity_result1.php?type=finalexam&quiz=$qid&courseid=$course_id'>$qname</a>","<a href='./delete_manual_activity_marks.php?id=$qid&course=$course_id&type=finalexam' title='Delete' onClick=\"return confirm('Are you sure you want to delete marks of this activity?')\" ><i class='icon fa fa-trash text-danger' aria-hidden='true' title='Delete' aria-label='Delete'></i></a>");
            
            }

            echo html_writer::table($table);
            echo "<br />";

        }

        else
            echo "<h3>You do not have any Final Exam in this course!</h3>";
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