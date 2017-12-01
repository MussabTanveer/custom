<?php
    require_once('../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Admin Reports");
    $PAGE->set_heading("Admin Reports");
    $PAGE->set_url($CFG->wwwroot.'/custom/report_admin.php');
    echo $OUTPUT->header();
require_login();
    is_siteadmin() || die($OUTPUT->header().'<h2>This page is for site admins only!</h2>'.
    	$OUTPUT->footer());

?>
<!DOCTYPE html>
<html>
<head>
	<div class="cd-timeline-content">
			<h3>Click the Links down below as per need </h3></br>

             <a href="./display_outcome_framework.php" target="_blank" class="cd-read-more">View Outcome Framework</a></br></br>

             <a href="./display_outcome_framework-4.php" target="_blank" class="cd-read-more">View Domain and level Mapping</a></br></br>

              <a href="./display_outcome_framework-2.php" target="_blank" class="cd-read-more">Map PLO to Domian</a></br></br>

              <a href="./display_outcome_framework-3.php" target="_blank" class="cd-read-more">Map CLO to Level</a></br></br>


</div>


</head>
<body>

</body>
</html>

<?php

echo $OUTPUT->footer();

?>