<?php
    require_once('../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Teacher Reports");
    $PAGE->set_heading("Teacher Reports");
    $PAGE->set_url($CFG->wwwroot.'/custom/report_teacher.php');
    echo $OUTPUT->header();
    require_login();
?>
	<div>
        <h3>Click the links down below as per need </h3><br>

        <a href="./display_courses.php" target="_blank">View Quiz Grid</a><br><br>

        <a href="./display_courses-2.php" target="_blank">Map Questions to CLO</a><br><br>

        <a href="./display_courses-3.php" target="_blank">Activity/Course CLO Report</a><br><br>
    </div>
<?php

echo $OUTPUT->footer();

?>
