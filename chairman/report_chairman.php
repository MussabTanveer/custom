<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Chairman Reports");
    $PAGE->set_heading("Chairman Reports & Forms");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/report_chairman.php');
    echo $OUTPUT->header();
    require_login();
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
?>

<link rel="stylesheet" type="text/css" href="../css/cool-link/style.css" />
<script src="../script/jquery/jquery-3.2.1.js"></script>

	<div>
        <h3>Click the links down below as per need </h3><br>
        
        <a href="javascript:void(0)" onclick="toggle_visibility('vnm');" class="cool-link">Vision & Mission</a><br><br>
        <div id="vnm" style="display: none">
            &nbsp;&nbsp;&nbsp;<a href="./define_vision_mission.php" class="cool-link">&#10070; Define Vision &amp; Mission</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./view_vision_mission.php" class="cool-link">&#10070; View Vision &amp; Mission</a><br><br>
        </div>

        <a href="javascript:void(0)" onclick="toggle_visibility('vl');" class="cool-link">Verb List</a><br><br>
        <div id="vl" style="display: none">
            &nbsp;&nbsp;&nbsp;<a href="./upload_verb_list.php" class="cool-link">&#10070; Upload Verb List</a><br>
            &nbsp;&nbsp;&nbsp;<a href="../view_verb_list.php" class="cool-link">&#10070; View Verb List</a><br><br>
        </div>

        <a href="javascript:void(0)" onclick="toggle_visibility('obef');" class="cool-link">OBE Framework</a><br><br>
        <div id="obef" style="display: none">
            &nbsp;&nbsp;&nbsp;<a href="./add_framework.php" class="cool-link">&#10070; Create OBE Framework</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./select_frameworktoPEO.php" class="cool-link">&#10070; Define PEOs</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./select_frameworktoPLO.php" class="cool-link">&#10070; Define PLOs</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./select_frameworktoCLO.php" class="cool-link">&#10070; Define CLOs</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./display_outcome_framework.php" class="cool-link">&#10070; View OBE Framework Mapping</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./display_outcome_framework-4.php" class="cool-link">&#10070; View Bloom's Taxonomy Mapping</a><br><br>
        </div>

        <a href="javascript:void(0)" onclick="toggle_visibility('cr');" class="cool-link">CLO Reports</a><br><br>
        <div id="cr" style="display: none">
            &nbsp;&nbsp;&nbsp;<a href="./display_teachers.php" class="cool-link">&#10070; View Teacher's Course CLO Report</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./display_students.php" class="cool-link">&#10070; View Student's CLO Progress</a><br><br>
        </div>

        <!--
        <a href="./define_vision_mission.php" class="cool-link">Define Vision &amp; Mission</a><br><br>

        <a href="./view_vision_mission.php" class="cool-link">View Vision &amp; Mission</a><br><br>

        <a href="./upload_verb_list.php" class="cool-link">Upload Verb List</a><br><br>
        
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

        <a href="./display_teachers.php" class="cool-link">View Teacher's Course CLO Report</a><br><br>

        <a href="./display_students.php" class="cool-link">View Student's CLO Progress</a><br><br>
        -->
    </div>

    <script type="text/javascript">
        function toggle_visibility(id) {
        var e = document.getElementById(id);
        if(e.style.display == 'block')
            e.style.display = 'none';
        else
            e.style.display = 'block';
        }
    </script>

<?php

echo $OUTPUT->footer();

?>
