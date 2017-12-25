<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Teacher Reports & Forms");
    $PAGE->set_heading("Teacher Reports & Forms");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/report_teacher.php');
    echo $OUTPUT->header();
    require_login();
?>
    <link rel="stylesheet" type="text/css" href="../css/cool-link/style.css" />

	<div>
        <h3>Click the links down below as per need </h3><br>

        <a href="./display_courses-4.php" class="cool-link">View Course CLOs Mapping to Levels &amp; PLOs</a><br><br>

        <a href="./display_courses-2.php" class="cool-link">Map Quiz Questions to CLOs</a><br><br>

        <a href="./display_courses.php" class="cool-link">View Quiz Detailed Report</a><br><br>

        <a href="./display_courses-3.php" class="cool-link">Activity/Course CLO Report</a><br><br>
    </div>
<?php

echo $OUTPUT->footer();

?>
