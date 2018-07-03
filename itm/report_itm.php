<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("IT Manager Forms");
    $PAGE->set_heading("IT Manager Forms");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/itm/report_itm.php');
    
    require_login();
    if($SESSION->oberole != "itm"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
?>
    <link rel="stylesheet" type="text/css" href="../css/cool-link/style.css" />
    <script src="../script/jquery/jquery-3.2.1.js"></script>

	<div>
        <a href="javascript:void(0)" id="sem_click" onclick="toggle_visibility('sem_click', 'sem');" class="cool-link"><span class="fa fa-chevron-down"></span> Semesters</a><br><br>
        <div id="sem" style="display: none">
            &nbsp;&nbsp;&nbsp;<a href="./add_semester.php" class="cool-link">&#10070; Create Semester</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./view_semester.php" class="cool-link">&#10070; Edit/Delete/View Semester</a><br><br>
        </div>

        <a href="javascript:void(0)" id="crs_click" onclick="toggle_visibility('crs_click', 'crs');" class="cool-link"><span class="fa fa-chevron-down"></span> Courses</a><br><br>
        <div id="crs" style="display: none">
            &nbsp;&nbsp;&nbsp;<a href="./select_frameworktoCourse.php" class="cool-link">&#10070; Create Courses &amp; Map CLOs</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./select_course_edit.php" class="cool-link">&#10070; Edit Courses</a><br>
            &nbsp;&nbsp;&nbsp;<a href="../../../course/management.php" class="cool-link">&#10070; Delete Courses</a><br><br>
        </div>

        <!--<a href="./select_frameworktopracCourse.php" class="cool-link">Create Practical Courses &amp; Map CLOs</a><br><br>-->

        <!--<a href="./select_course.php" class="cool-link">Add CLOs to Courses</a><br><br>-->

        <a href="javascript:void(0)" id="usr_click" onclick="toggle_visibility('usr_click', 'usr');" class="cool-link"><span class="fa fa-chevron-down"></span> Users</a><br><br>
        <div id="usr" style="display: none">
            &nbsp;&nbsp;&nbsp;<a href="../../../cohort/index.php" class="cool-link">&#10070; Add/Edit/Delete/View Cohorts</a><br>
            &nbsp;&nbsp;&nbsp;<a href="../../../user/editadvanced.php?id=-1" class="cool-link">&#10070; Add a New User</a><br>
            &nbsp;&nbsp;&nbsp;<a href="../../../admin/tool/uploaduser/index.php" class="cool-link">&#10070; Upload Users</a><br>
            &nbsp;&nbsp;&nbsp;<a href="../../../admin/user/user_bulk.php" class="cool-link">&#10070; Bulk User Actions</a><br>
            &nbsp;&nbsp;&nbsp;<a href="../../../admin/user.php" class="cool-link">&#10070; View/Edit/Delete Users</a><br><br>
        </div>

        <a href="javascript:void(0)" id="enrol_click" onclick="toggle_visibility('enrol_click', 'enrol');" class="cool-link"><span class="fa fa-chevron-down"></span> Enrollment</a><br><br>
        <div id="enrol" style="display: none">
            &nbsp;&nbsp;&nbsp;<a href="./select_course_enrol.php" class="cool-link">&#10070; Enrol/Unenrol Users from Courses</a><br><br>
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
