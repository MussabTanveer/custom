<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Teacher Reports");
    $PAGE->set_heading("Teacher Reports");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/report_teacher_past.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    if(isset($_GET['course']))
    {
        $course_id=$_GET['course'];
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/cool-link/style.css" />

	<div>
    <h5 style="color: green"> Note: You can only view past course profile, grading policy and reports. Past courses are not Editable. </h5><br>
        <h3> Click the links down below as per need </h3><br>

        <!--<a <?php echo "href='./view_course_profile.php?course=$course_id'" ?> class="cool-link">View Course Profile</a><br><br>--> <!--For PDF Course Profile View-->

        <a <?php echo "href='./view_course_profileform.php?course=$course_id'" ?> class="cool-link">View Course Profile</a><br><br>

        <a <?php echo "href='./display_grading_policy.php?course=$course_id'" ?>  class="cool-link">Display Grading Policy</a><br><br>
        
        <a href="javascript:void(0)" onclick="toggle_visibility('clorep');" class="cool-link">CLO Reports</a><br><br>
        <div id="clorep" style="display: none">
            &nbsp;&nbsp;&nbsp;<a <?php echo "href='./display_activities-2.php?course=$course_id'" ?> class="cool-link">&#10070; Activity Detailed Report</a><br>
            &nbsp;&nbsp;&nbsp;<a <?php echo "href='./course_report.php?course=$course_id'" ?> class="cool-link">&#10070; Course Report</a><br>
            &nbsp;&nbsp;&nbsp;<a <?php echo "href='./display_activities.php?course=$course_id'" ?> class="cool-link">&#10070; Activity CLO Report</a><br>
            &nbsp;&nbsp;&nbsp;<a <?php echo "href='./clo_wise_report.php?course=$course_id'" ?> class="cool-link">&#10070; Student CLO-wise Report</a><br><br>
        </div>
        
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
    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./teacher_courses.php">Back</a>
    <?php
    }
    echo $OUTPUT->footer();
?>
