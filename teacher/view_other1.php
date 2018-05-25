<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Edit/Delete Other");
    $PAGE->set_heading("Edit/Delete Other");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/view_other1.php');
    
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
        $others= $DB->get_records_sql("SELECT * FROM mdl_manual_other WHERE courseid = ? AND module = ?",array($course_id,-6));

        if($others)
        { 
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Other Name','Delete','Edit');

            foreach ($others as $other) 
            {
                # code...
                $serialno++;
                $oname = $other->name;
                $odesc = $other->description;
                $oid   = $other->id;
               
             
                    $table->data[] = array($serialno," $oname","<a href='./delete_manual_activity.php?id=$oid&course=$course_id&type=$type' title='Delete' onClick=\"return confirm('Are you sure you want to delete this activity and its attempt?')\" ><i class='icon fa fa-trash text-danger' aria-hidden='true' title='Delete' aria-label='Delete'></i></a>","<a href='./edit_manual_activity3.php?id=$oid&course=$course_id&type=$type' title='Edit') ><i class='icon fa fa-pencil text-info' aria-hidden='true' title='Edit' aria-label='Edit'></i></a>");

            ?>
            
            
            <?php
            }
            echo html_writer::table($table);
            echo "<br />";
        }
        else
            echo "<font color = red> No Assignment Found!</font>";
    }
    else
	{?>
		<h3 style="color:red;"> Invalid Selection </h3>
    	<a href="./teacher_courses.php">Back</a>
    	<?php
    }
    echo $OUTPUT->footer();
?>
