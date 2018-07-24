<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Delete Sample");
    $PAGE->set_heading("Delete Sample");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/delete_sample_paper.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();



if(!empty($_GET['id']) && !empty($_GET['courseid']) && !empty($_GET['type']))
{
    $course_id=$_GET['courseid'];
    $coursecontext = context_course::instance($course_id);
    is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());

    $id = $_GET['id'];
    $type = $_GET['type'];

    if ($type == "quiz")
    {
        $mod=-1;
    }
    elseif ($type == "assign")
    {
        $mod=-4;
    }
    elseif ($type == "project")
    {
        $mod=-5;
    }
     elseif ($type == "midterm")
    {
        $mod=-2;
    }
     elseif ($type == "finalexam")
    {
        $mod=-3;
    }
     elseif ($type == "other")
    {
        $mod=-6;
    }


 $instances= $DB->get_records_sql("SELECT * FROM mdl_sample_solution WHERE id = ? AND module = ?",array($id,$mod));

    if($instances)
    {

        foreach ($instances as $instance) {
            $instance = $instance->instance;

        }
    }



    $sql = "DELETE FROM mdl_sample_solution WHERE id = ? AND module =? ";
    $DB->execute($sql,array($id,$mod));
    echo "<font color = green> Sample Paper Has Been Deleted Successfully </font><br>"; 

    $redirect = "./view_samples.php?type=$type&instance=$instance&courseid=$course_id";
    redirect($redirect);
?>


<?php
}
else 
{
    echo "<font color=red size = 20px> Error </font>";
}
  echo $OUTPUT->footer();
?>
