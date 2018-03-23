<?php 
   require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Print Project");
    $PAGE->set_heading("Print Project");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/print_project_paper.php');
    
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
            $table->head = array('S. No.', 'Project Details','Print Project Paper');
            foreach ($assigns as $assign) 
            {
                # code...
                 $serialno++;
                $aname = $assign->name;
                $adesc = $assign->description;
                $aid   = $assign->id;
                $mime = $assign->mime;
                 if ($mime)
                 $table->data[] = array($serialno,"<a href='./print_assign.php?assign=$aid&courseid=$course_id'>Print $aname</a>","<a href='./print_uploaded_paper2.php?assign=$aid&courseid=$course_id'>Print $aname uploaded paper</a>");
            ?>
           
            
            <?php
            }
              echo html_writer::table($table);
            echo "<br />";
        }
        else
            echo "<h3><font color = red> No Project Found!</font></h3>";
    }
    else
	{?>
		<h3 style="color:red;"> Invalid Selection </h3>
    	<a href="./teacher_courses.php">Back</a>
    	<?php
    }

    echo $OUTPUT->footer();
?>
