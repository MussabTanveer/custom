
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
        $coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
        //echo "$course_id";
        $assigns= $DB->get_records_sql("SELECT * FROM mdl_manual_assign_pro WHERE courseid = ? AND module = ?",array($course_id,-4));

        if($assigns)
        {
            foreach ($assigns as $assign) 
            {
                # code...
                $aname = $assign->name;
                $adesc = $assign->description;
                $aid   = $assign->id;
            ?>
            <a <?php echo "href='./print_assign.php?assign=$aid&courseid=$course_id'" ?> > Print <?php echo $aname; ?> </a><br>
            
            <?php
            }
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
