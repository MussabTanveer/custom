<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("My Courses");
    $PAGE->set_heading("Courses");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/student/display_courses_student2.php');
    
    require_login();
    if($SESSION->oberole != "student"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <?php
    if(isset($_POST['submit']) && isset( $_POST['semesterid']))
    {
        $semester_id=$_POST['semesterid'];
        // Dispaly all courses
        $action="display_activities_student2.php";
        require '../templates/display_courses_student_template.php';
    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./report_student.php">Back</a>
    <?php
    }
    echo $OUTPUT->footer();
?>
