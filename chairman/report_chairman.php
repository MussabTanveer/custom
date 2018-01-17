<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Chairman Reports");
    $PAGE->set_heading("Chairman Reports");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/report_chairman.php');
    echo $OUTPUT->header();
    require_login();
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
?>

 <link rel="stylesheet" type="text/css" href="../css/cool-link/style.css" />

	<div>
        <h3>Click the links down below as per need </h3><br>
        
        <a href="./define_vision_mission.php" class="cool-link">Define Vision &amp; Mission</a><br><br>

        <a href="./view_vision_mission.php" class="cool-link">View Vision &amp; Mission</a><br><br>

        <a href="./add_framework.php" class="cool-link">Create OBE Framework</a><br><br>

        <a href="./select_frameworktoPEO.php" class="cool-link">Define PEOs</a><br><br>

        <a href="./select_frameworktoPLO.php" class="cool-link">Define PLOs</a><br><br>

        <a href="./select_framework.php" class="cool-link">Map PLOs to PEOs</a><br><br>

        <a href="./select_frameworktoCLO.php" class="cool-link">Define CLOs</a><br><br>

        <a href="./select_framework-2.php" class="cool-link">Map CLOs to PLOs</a><br><br>

        <a href="./display_outcome_framework-2.php" class="cool-link">Map PLOs to Domains</a><br><br>

        <a href="./display_outcome_framework-3.php" class="cool-link">Map CLOs to Levels</a><br><br>

        <a href="./display_outcome_framework.php" class="cool-link">View OBE Framework Mapping</a><br><br>

        <a href="./display_outcome_framework-4.php" class="cool-link">View Bloom's Taxonomy Mapping</a><br><br>

        <a href="./display_teachers.php" class="cool-link">View Activity/Course CLO Report</a><br><br>

        <a href="./display_students.php" class="cool-link">View Student CLO Progress</a><br><br>
    </div>

<?php

echo $OUTPUT->footer();

?>
