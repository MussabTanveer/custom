<script src="../script/jquery/jquery-3.2.1.js"></script>

<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Teacher Reports & Forms");
    $PAGE->set_heading("Teacher Reports & Forms");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/report_teacher.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    if(isset($_GET['course']))
    {
        $course_id=$_GET['course'];
    ?>
    <link rel="stylesheet" type="text/css" href="../css/cool-link/style.css" />

	<div>
        <h3>Click the links down below as per need </h3><br>

        <a <?php echo "href='./view_course_profile.php?course=$course_id'" ?> class="cool-link">View Course Profile</a><br><br>

        <a href="javascript:void(0)" onclick="toggle_visibility('gp');" class="cool-link">Grading Policy</a><br><br>
        <div id="gp" style="display: none">
            &nbsp;&nbsp;&nbsp;<a <?php echo "href='./grading_policy.php?course=$course_id'" ?>  class="cool-link">&#10070; Define Grading Policy</a><br>
            &nbsp;&nbsp;&nbsp;<a <?php echo "href='./display_grading_policy.php?course=$course_id'" ?>  class="cool-link">&#10070; Display Grading Policy</a><br><br>
        </div>

        <a href="javascript:void(0)" onclick="toggle_visibility('tools');" class="cool-link">Course Evaluation Tools &amp; Mappings</a><br><br>
        <div id="tools" style="display: none">
            &nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" onclick="toggle_visibility('manual');" class="cool-link">&#10070; Manual Evaluation</a><br><br>
            <div id="manual" style="display: none">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./define_quiz.php?type=quiz&course=$course_id'" ?> class="cool-link">&#10022; Define Quiz</a><br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./define_assign_pro.php?type=assign&course=$course_id'" ?> class="cool-link">&#10022; Define Assignment</a><br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./define_assign_pro.php?type=project&course=$course_id'" ?> class="cool-link">&#10022; Define Project</a><br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./define_quiz.php?type=midterm&course=$course_id'" ?> class="cool-link">&#10022; Define Mid Term</a><br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./define_quiz.php?type=finalexam&course=$course_id'" ?> class="cool-link">&#10022; Define Final Exam</a><br><br>
            </div>
            &nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" onclick="toggle_visibility('online');" class="cool-link">&#10070; Online Evaluation</a><br><br>
            <div id="online" style="display: none">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='../../../course/modedit.php?add=quiz&type=&course=$course_id&section=0&return=0&sr=0'" ?> class="cool-link">&#10022; Define Quiz/Mid Term</a><br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='../../../course/modedit.php?add=assign&type=&course=$course_id&section=0&return=0&sr=0'" ?> class="cool-link">&#10022; Define Assignment</a><br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./map_grading_item.php?course=$course_id'" ?> class="cool-link">&#10022; Map Activities to Grading items</a><br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./display_quizzes-2.php?course=$course_id'" ?> class="cool-link">&#10022; Map Questions to CLOs</a><br><br>
            </div>
        </div>
        
        <!--<a "href='../../../question/edit.php?cmid=13'" class="cool-link">Add Questions to Assessment</a><br><br>-->

        <!--<a href="./display_courses-4.php" class="cool-link">View Course CLOs Mapping to Levels &amp; PLOs</a><br><br>-->
        
        <a href="javascript:void(0)" onclick="toggle_visibility('clorep');" class="cool-link">CLO Reports</a><br><br>
        <div id="clorep" style="display: none">
            &nbsp;&nbsp;&nbsp;<a <?php echo "href='./display_quizzes.php?course=$course_id'" ?> class="cool-link">&#10070; View Activity Detailed Report</a><br>
            &nbsp;&nbsp;&nbsp;<a <?php echo "href='./display_activities.php?course=$course_id'" ?> class="cool-link">&#10070; Activity/Course CLO Report</a><br>
            &nbsp;&nbsp;&nbsp;<a <?php echo "href='./course_report.php?course=$course_id'" ?> class="cool-link">&#10070; Course Report</a><br><br>
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
