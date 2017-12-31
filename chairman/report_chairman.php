<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Chairman Reports");
    $PAGE->set_heading("Chairman Reports");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/report_chairman.php');
    echo $OUTPUT->header();
    require_login();
?>

 <link rel="stylesheet" type="text/css" href="../css/cool-link/style.css" />

	<div>
        <h3>Click the links down below as per need </h3><br>
        
        <a href="./view_vision_mission.php" class="cool-link">View Vision &amp; Mission</a><br><br>

        <a href="./display_outcome_framework.php" class="cool-link">View OBE Framework Mapping</a><br><br>

        <a href="./display_outcome_framework-4.php" class="cool-link">View Bloom's Taxonomy Mapping</a><br><br>
    </div>

<?php

echo $OUTPUT->footer();

?>
