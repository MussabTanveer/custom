<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Admin Reports & Forms");
    $PAGE->set_heading("Admin Reports & Forms");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/admin/report_admin.php');
    echo $OUTPUT->header();
    require_login();
    is_siteadmin() || die($OUTPUT->header().'<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());
?>
	<div>
        <h3>Click the links down below as per need </h3><br>

        <a href="./guidelines.php" target="_blank">Admin Guidelines</a><br><br>

        <a href="./add_framework.php" >Create OBE Framework</a><br><br>

        <a href="./select_frameworktoPEO.php" >Define PEOs</a><br><br>

        <a href="./select_frameworktoPLO.php" >Define PLOs</a><br><br>

        <a href="./select_framework.php" >Map PLOs to PEOs</a><br><br>

        <a href="./select_frameworktoCLO.php" >Define CLOs</a><br><br>

        <a href="./select_framework-2.php" >Map CLOs to PLOs</a><br><br>

        <a href="./display_outcome_framework-2.php" >Map PLOs to Domains</a><br><br>

        <a href="./display_outcome_framework-3.php" >Map CLOs to Levels</a><br><br>

        <a href="./display_outcome_framework.php" >View OBE Framework Mapping</a><br><br>

        <a href="./display_outcome_framework-4.php" >View Bloom's Taxonomy Mapping</a><br><br>

        <a href="./select_frameworktoCourse.php" >Create Courses &amp; Map CLOs</a><br><br>

        <!--<a href="./select_course.php" >Add CLOs to Courses</a><br><br>-->

        <a href="../../../user/editadvanced.php?id=-1" >Add a new User</a><br><br>

        <a href="../../../admin/user.php" >View all Users</a><br><br>

        <a href="../../../cohort/index.php" >Add/Edit/View Cohorts</a><br><br>

        <a href="./select_course_enrol.php" >Enrol/Unenrol Users</a><br><br>
    </div>
<?php

echo $OUTPUT->footer();

?>
