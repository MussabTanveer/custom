<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("My Semesters");
    $PAGE->set_heading("Semesters");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/student/select_semester_marks.php');
    
    require_login();
    if($SESSION->oberole != "student"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <?php

    // Dispaly all semesters
    $action="display_courses_student2.php";
    require '../templates/select_semester_template.php';

    echo $OUTPUT->footer();
?>
