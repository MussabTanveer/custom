<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("My Activities");
    $PAGE->set_heading("Activities");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/display_activities.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <script>
    if(performance.navigation.type == 2){
    location.reload(true);
    }
    </script>
    <?php

    /*if((isset($_POST['submit']) && isset( $_POST['courseid'])) || (isset($SESSION->cid3) && $SESSION->cid3 != "xyz"))
    {
        if(isset($SESSION->cid3) && $SESSION->cid3 != "xyz")
        {
            $course_id=$SESSION->cid3;
            $SESSION->cid3 = "xyz";
        }
        else
            $course_id=$_POST['courseid'];*/

    if(!empty($_GET['course']))
    {
        $course_id=$_GET['course'];
        $coursecontext = context_course::instance($course_id);
		is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
        
        //echo "Course ID : $course_id";
        require '../templates/activity_clo_report_template.php';
        
        echo "<a class='btn btn-default' href='./report_teacher_past.php?course=$course_id'>Go Back</a>";

        
    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./teacher_courses.php">Back</a>
    <?php
    }
    echo $OUTPUT->footer();
    ?>
