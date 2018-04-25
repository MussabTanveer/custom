
<?php 
   require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Print Assignment");
    $PAGE->set_heading("Print Assignment");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/print_assign_paper.php');
    
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
        $assigns= $DB->get_records_sql("SELECT * FROM mdl_manual_assign_pro WHERE courseid = ? AND module = ?",array($course_id,-4));

        if($assigns)
        { 
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Assignment Details','Print Assignment Paper','Delete');

            foreach ($assigns as $assign) 
            {
                # code...
                $serialno++;
                $aname = $assign->name;
                $adesc = $assign->description;
                $aid   = $assign->id;
                 $mime = $assign->mime;
                if ($mime)
                 $table->data[] = array($serialno,"<a href='./print_assign.php?assign=$aid&courseid=$course_id'>Print $aname</a>","<a href='./print_uploaded_paper2.php?assign=$aid&courseid=$course_id'>Print $aname uploaded paper</a>","<a href='./delete_manual_activity.php?id=$aid&course=$course_id&type=$type' title='Delete' onClick=\"return confirm('Are you sure you want to delete this activity and its attempt?')\" > <img src='../img/icons/Delete1.png' /></a>");
             else
                $table->data[] = array($serialno,"<a href='./print_assign.php?assign=$aid&courseid=$course_id'>Print $aname</a>","-");

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
