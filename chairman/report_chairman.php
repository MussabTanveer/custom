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
        <a href="javascript:void(0)" id="vnm_click" onclick="toggle_visibility('vnm_click', 'vnm');" class="cool-link"><span class="fa fa-chevron-down"></span> Vision & Mission</a><br><br>
        <div id="vnm" style="display: none">
            &nbsp;&nbsp;&nbsp;<a href="./define_vision_mission.php" class="cool-link">&#10070; Define Vision &amp; Mission</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./view_vision_mission.php" class="cool-link">&#10070; View Vision &amp; Mission</a><br><br>
        </div>
        
        <a href="javascript:void(0)" id="obef_click" onclick="toggle_visibility('obef_click', 'obef');" class="cool-link"><span class="fa fa-chevron-down"></span> OBE Framework</a><br><br>
        <div id="obef" style="display: none">
            &nbsp;&nbsp;&nbsp;<a href="./add_framework.php" class="cool-link">&#10070; Create OBE Framework</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./select_frameworktoPEO.php" class="cool-link">&#10070; Define PEOs</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./select_frameworktoPLO.php" class="cool-link">&#10070; Define PLOs</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./add_rubric.php" class="cool-link">&#10070; Define Rubrics</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./select_rubric.php" class="cool-link">&#10070; View/Edit/Delete Rubrics</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./select_frameworktoCLO.php" class="cool-link">&#10070; Define CLOs</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./display_outcome_framework.php" class="cool-link">&#10070; View OBE Framework Mapping</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./display_outcome_framework-4.php" class="cool-link">&#10070; View Bloom's Taxonomy Mapping</a><br><br>
        </div>

            
            <a href="javascript:void(0)" id="pr_click" onclick="toggle_visibility('pr_click', 'pr');" class="cool-link"><span class="fa fa-chevron-down"></span> PLO Report</a><br><br>
        <div id="pr" style="display: none">
            &nbsp;&nbsp;&nbsp;<a href="./select_batch.php" class="cool-link">&#10070; View
            PLO Report</a><br><br>
        </div>


        <a href="javascript:void(0)" id="cr_click" onclick="toggle_visibility('cr_click', 'cr');" class="cool-link"><span class="fa fa-chevron-down"></span> CLO Reports</a><br><br>
        <div id="cr" style="display: none">
            &nbsp;&nbsp;&nbsp;<a href="./display_teachers.php" class="cool-link">&#10070; View Teacher's Course CLO Report</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./display_students.php" class="cool-link">&#10070; View Student's CLO Progress</a><br><br>
        </div>

        
            
        </div>

        <a href="javascript:void(0)" id="vl_click" onclick="toggle_visibility('vl_click', 'vl');" class="cool-link"><span class="fa fa-chevron-down"></span> Verb List</a><br><br>
        <div id="vl" style="display: none">
            &nbsp;&nbsp;&nbsp;<a href="./upload_verb_list.php" class="cool-link">&#10070; Upload Verb List</a><br>
            &nbsp;&nbsp;&nbsp;<a href="../view_verb_list.php" class="cool-link">&#10070; View Verb List</a><br><br>
        </div>
        
        <a href="javascript:void(0)" id="wt_click" onclick="toggle_visibility('wt_click', 'wt');" class="cool-link"><span class="fa fa-chevron-down"></span> Weightage</a><br><br>
        <div id="wt" style="display: none">
            &nbsp;&nbsp;&nbsp;<a href="./assign_weightage.php" class="cool-link">&#10070; Assign Weightage</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./view_weightage.php" class="cool-link">&#10070; View Weightage</a><br><br>
        </div>
    </div>

    <script type="text/javascript">
        function toggle_visibility(id_click, id) {
            var e = document.getElementById(id);
            var e_click = document.getElementById(id_click).getElementsByTagName("span")[0];
            if(e.style.display == 'block') {
                e.style.display = 'none';
                e_click.className = "fa fa-chevron-down";
            }
            else {
                e.style.display = 'block';
                e_click.className = "fa fa-chevron-up";
            }
        }
    </script>

<?php

echo $OUTPUT->footer();

?>
