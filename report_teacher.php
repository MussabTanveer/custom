<?php
    require_once('../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('Teacher');
    $PAGE->set_title("Teacher Reports");
    $PAGE->set_heading("Teacher Reports");
    $PAGE->set_url($CFG->wwwroot.'/custom/report_teacher.php');
    echo $OUTPUT->header();
require_login();

?>
<!DOCTYPE html>
<html>
<head>
	<div class="cd-timeline-content">
			<h3>Click the Links down below as per need </h3></br>

             <a href="./display_courses.php" target="_blank" class="cd-read-more">View Grid</a></br></br>

             <a href="./display_courses-2.php" target="_blank" class="cd-read-more">Map Questions to CLO</a></br></br>

              <a href="./display_courses-3.php" target="_blank" class="cd-read-more">Display Quiz CLO Report</a></br></br>

              <a href="./display_outcome_framework.php" target="_blank" class="cd-read-more">View Mapped Outcomes Report</a></br></br>

              <a href="./display_outcome_framework-4.php" target="_blank" class="cd-read-more">View Domain and Level Mapping</a></br></br>




</div>


</head>
<body>

</body>
</html>

<?php

echo $OUTPUT->footer();

?>