<?php
    require_once('../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Student Reports");
    $PAGE->set_heading("Student Reports");
    $PAGE->set_url($CFG->wwwroot.'/custom/report_student.php');
    echo $OUTPUT->header();
    require_login();
?>
	<div>
        <h3>Click the links down below as per need </h3><br>

        <a href="./display_courses_student.php" target="_blank">My Activity CLO Report</a><br><br>

        <a href="./display_course_progress.php" target="_blank">My Progress Report</a></br><br>
    </div>
<?php

echo $OUTPUT->footer();

?>
