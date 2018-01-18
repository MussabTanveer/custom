<script src="../script/jquery/jquery-3.2.1.js"></script>

<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Teacher Reports & Forms");
    $PAGE->set_heading("Teacher Reports & Forms");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/report_teacher.php');
    echo $OUTPUT->header();
    require_login();

    if((isset($_POST['submit']) && isset( $_POST['courseid'])) || (isset($SESSION->cid) && $SESSION->cid != "xyz"))
    {
        if(isset($SESSION->cid) && $SESSION->cid != "xyz")
        {
            $course_id=$SESSION->cid;
            $SESSION->cid = "xyz";
        }
        else
            $course_id=$_POST['courseid'];
?>
    <link rel="stylesheet" type="text/css" href="../css/cool-link/style.css" />

	<div>
        <h3>Click the links down below as per need </h3><br>

        <a href="./abcd.php" class="cool-link">View Course Profile</a><br><br>

        <a href="./abcd.php" class="cool-link">Define Grading Policy</a><br><br>

        <a href="javascript:void(0)" onclick="toggle_visibility('tools');" class="cool-link">Course Evaluation Tools</a><br><br>
        <div id="tools" style="display: none">
            &nbsp;&nbsp;&nbsp;<a href="./abcd.php" class="cool-link">- Define Quiz</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./abcd.php" class="cool-link">- Define Assignment</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./abcd.php" class="cool-link">- Define Project</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./abcd.php" class="cool-link">- Define Mid Term</a><br>
            &nbsp;&nbsp;&nbsp;<a href="./abcd.php" class="cool-link">- Define Final Exam</a><br><br>
        </div>

        <a href="./display_courses-4.php" class="cool-link">View Course CLOs Mapping to Levels &amp; PLOs</a><br><br>

        <a href="./display_courses-2.php" class="cool-link">Map Quiz Questions to CLOs</a><br><br>

        <a href="./display_courses.php" class="cool-link">View Quiz Detailed Report</a><br><br>

        <a href="./display_courses-3.php" class="cool-link">Activity/Course CLO Report</a><br><br>
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
        <a href="../index.php">Back</a>
    <?php
    }
    echo $OUTPUT->footer();
    
?>
