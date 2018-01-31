<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Delete Weightage");
    $PAGE->set_heading("Delete Weightage");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/delete_grading_policy.php');
    echo $OUTPUT->header();
	require_login();
if($SESSION->oberole != "chairman"){
        header('Location: ../index.php');
	}


$sql_delete="DELETE FROM mdl_grading_policy WHERE name='mid term' OR name='final exam'";

 $DB->execute($sql_delete);

 echo "<font color='green'><b>Weightage across Midterms and Finals Acrosss all Courses have been deleted!<br />";
?>




<?php
echo $OUTPUT->footer();

?>