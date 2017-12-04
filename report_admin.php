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
    is_siteadmin() || die($OUTPUT->header().'<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());
?>
	<div>
        <h3>Click the links down below as per need </h3><br>

        <a href="./guidelines.php" target="_blank">Admin Guidelines</a><br><br>

        <a href="./add_framework.php" target="_blank">Create OBE Framework</a><br><br>

        <a href="./select_frameworktoPEO.php" target="_blank">Define PEOs</a><br><br>

        <a href="./select_frameworktoPLO.php" target="_blank">Define PLOs</a><br><br>

        <a href="./select_framework.php" target="_blank">Map PLOs to PEOs</a><br><br>

        <a href="./select_frameworktoCLO.php" target="_blank">Define CLOs</a><br><br>

        <a href="./select_framework-2.php" target="_blank">Map CLOs to PLOs</a><br><br>

        <a href="./display_outcome_framework-2.php" target="_blank">Map PLOs to Domains</a><br><br>

        <a href="./display_outcome_framework-3.php" target="_blank">Map CLOs to Levels</a><br><br>

        <a href="./display_outcome_framework.php" target="_blank">View OBE Framework Mapping</a><br><br>

        <a href="./display_outcome_framework-4.php" target="_blank">View Bloom's Taxonomy Mapping</a><br><br>

        <a href="../course/edit.php?category=1&returnto=guidelines" target="_blank">Create Courses</a><br><br>

        <a href="./select_course.php" target="_blank">Add CLOs to Courses</a><br><br>

        <a href="../user/editadvanced.php?id=-1" target="_blank">Add a new User</a><br><br>

        <a href="../admin/user.php" target="_blank">View all Users</a><br><br>

        <a href="../cohort/index.php" target="_blank">Add/Edit/View Cohorts</a><br><br>

        <a href="./select_course_enrol.php" target="_blank">Enrol/Unenrol Users</a><br><br>
    </div>
<?php

echo $OUTPUT->footer();

?>
