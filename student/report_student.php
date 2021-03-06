<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Student Reports");
    $PAGE->set_heading("Student Reports");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/student/report_student.php');
    
    require_login();
    if($SESSION->oberole != "student"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
?>
    <link rel="stylesheet" type="text/css" href="../css/cool-link/style.css" />
    
	<div>
        <a href="./select_semester_marks.php" class="cool-link">My Activity Marks Report</a><br><br>

        <a href="./select_semester_clo.php" class="cool-link">My Activity CLO Report</a><br><br>

        <a href="./select_semester.php" class="cool-link">My Semester Progress Report</a></br><br>

        <a href="./display_course_progress.php" class="cool-link">My Overall Progress Report</a></br><br>
    </div>
<?php

echo $OUTPUT->footer();

?>
