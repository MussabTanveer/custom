<?php
    require_once('../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('Teacher');
    $PAGE->set_title("Student Reports");
    $PAGE->set_heading("Student Reports");
    $PAGE->set_url($CFG->wwwroot.'/custom/report_student.php');
    echo $OUTPUT->header();
require_login();

?>
<!DOCTYPE html>
<html>
<head>
	<div class="cd-timeline-content">
			<h3>Click the Links down below as per need </h3></br>

             <a href="./display_courses_student.php" target="_blank" class="cd-read-more">My CLO Report</a></br></br>

             <a href="./display_course_progress.php" target="_blank" class="cd-read-more">My Progress Report</a></br></br>

             




</div>


</head>
<body>

</body>
</html>

<?php

echo $OUTPUT->footer();

?>