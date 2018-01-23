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
    <link rel="stylesheet" type="text/css" href="../css/cool-link/style.css" />

	<div>
        <h3>Click the links down below as per need </h3><br>

        <a href="./guidelines.php" target="_blank" class="cool-link">Admin Guidelines</a><br><br>

        <a href="./define_vision_mission.php" class="cool-link">Define Vision &amp; Mission</a><br><br>

        <a href="./view_vision_mission.php" class="cool-link">View Vision &amp; Mission</a><br><br>

        <a href="./add_framework.php" class="cool-link">Create OBE Framework</a><br><br>

        <a href="./select_frameworktoPEO.php" class="cool-link">Define PEOs</a><br><br>

        <a href="./select_frameworktoPLO.php" class="cool-link">Define PLOs</a><br><br>

        <!--<a href="./select_framework.php" class="cool-link">Map PLOs to PEOs</a><br><br>-->

        <a href="./select_frameworktoCLO.php" class="cool-link">Define CLOs</a><br><br>

        <!--<a href="./select_framework-2.php" class="cool-link">Map CLOs to PLOs</a><br><br>-->

        <!--<a href="./display_outcome_framework-2.php" class="cool-link">Map PLOs to Domains</a><br><br>-->

        <!--<a href="./display_outcome_framework-3.php" class="cool-link">Map CLOs to Levels</a><br><br>-->

        <a href="./display_outcome_framework.php" class="cool-link">View OBE Framework Mapping</a><br><br>

        <a href="./display_outcome_framework-4.php" class="cool-link">View Bloom's Taxonomy Mapping</a><br><br>

        <a href="./select_frameworktoCourse.php" class="cool-link">Create Courses &amp; Map CLOs</a><br><br>

        <!--<a href="./select_course.php" class="cool-link">Add CLOs to Courses</a><br><br>-->

        <a href="../../../user/editadvanced.php?id=-1" class="cool-link">Add a new User</a><br><br>

        <a href="../../../admin/user.php" class="cool-link">View all Users</a><br><br>

        <a href="../../../cohort/index.php" class="cool-link">Add/Edit/View Cohorts</a><br><br>

        <a href="./select_course_enrol.php" class="cool-link">Enrol/Unenrol Users</a><br><br>
    </div>
<?php

echo $OUTPUT->footer();

?>
