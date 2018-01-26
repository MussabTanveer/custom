<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Grading Policy");
    $PAGE->set_heading("Add Grading Policy Items");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/to_editing_deleting.php');
    echo $OUTPUT->header();
	require_login();


	if(isset($_GET['courseid'])){

$course_id=$_GET['courseid'];

//echo $course_id;

	}


echo "<font color = green>Grading Policy is already 80% or greater<br />Cannot add another evaluation method.<br />Either <a href=display_grading_policy.php?course=$course_id>delete</a> or <a href=display_grading_policy.php?course=$course_id>edit</a> grading policy.</font>";



?>




	<?php

	echo $OUTPUT->footer();

?>
