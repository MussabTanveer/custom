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
        $type = $_GET['type'];
        $coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
        //echo "$course_id";
        $assigns= $DB->get_records_sql("SELECT * FROM mdl_manual_assign_pro WHERE courseid = ? AND module = ?",array($course_id,-5));

        if($assigns)
        {
             $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Project Details','Print Project Paper','Delete','Edit');
            foreach ($assigns as $assign) 
            {
                # code...
                $serialno++;
                $aname = $assign->name;
                $adesc = $assign->description;
                $aid   = $assign->id;
                $mime = $assign->mime;
                if ($mime)
                    $table->data[] = array($serialno,"<a href='./print_assign.php?assign=$aid&courseid=$course_id'>Print $aname</a>","<a href='./print_uploaded_paper2.php?assign=$aid&courseid=$course_id'>Print $aname uploaded paper</a>","<a href='./delete_manual_activity.php?id=$aid&course=$course_id&type=$type' title='Delete' onClick=\"return confirm('Are you sure you want to delete this activity and its attempt?')\" ><i class='icon fa fa-trash text-danger' aria-hidden='true' title='Delete' aria-label='Delete'></i></a>","<a href='./edit_manual_activity2.php?id=$aid&course=$course_id&type=$type' title='Edit') ><i class='icon fa fa-pencil text-info' aria-hidden='true' title='Edit' aria-label='Edit'></i></a>");
                else
                    $table->data[] = array($serialno,"<a href='./print_assign.php?assign=$aid&courseid=$course_id'>Print $aname</a>","-","<a href='./delete_manual_activity.php?id=$aid&course=$course_id&type=$type' title='Delete' onClick=\"return confirm('Are you sure you want to delete this activity and its attempt?')\" ><i class='icon fa fa-trash text-danger' aria-hidden='true' title='Delete' aria-label='Delete'></i></a>","<a href='./edit_manual_activity2.php?id=$aid&course=$course_id&type=$type' title='Edit') ><i class='icon fa fa-pencil text-info' aria-hidden='true' title='Edit' aria-label='Edit'></i></a>");
            ?>
           
            
            <?php
            }
              echo html_writer::table($table);
            echo "<br />";
        }
        else
            echo "<h3 style='color:red;'>No Project Found!</h3>";
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
