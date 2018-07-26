<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("CLO Wise Report");
    $PAGE->set_heading("CLO Wise Report");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/clo_wise_report.php');
    
	require_login();
	if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
	}
    echo $OUTPUT->header();
?>
<link href="../css/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet">
<script src="../script/perfect-scrollbar/perfect-scrollbar.js"></script>

<script src="../script/chart/Chart.bundle.js"></script>
<script src="../script/chart/utils.js"></script>


<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/table2excel/jquery.table2excel.js"></script>
<style>
#container {
    position: relative;
    margin: 0px auto;
    padding: 0px;
    width: 100%;
    overflow: auto;
}

/* Change the alignment of scrollbars */
/* Recommendation: modify CSS directly */
.ps__rail-x {
    top: 0px;
    bottom: auto; /* If using `top`, there shouldn't be a `bottom`. */
}
.ps__rail-y {
    left: 0px;
    right: auto; /* If using `left`, there shouldn't be a `right`. */
}
.ps__thumb-x {
    top: 2px;
    bottom: auto; /* If using `top`, there shouldn't be a `bottom`. */
}
.ps__thumb-y {
    left: 2px;
    right: auto; /* If using `left`, there shouldn't be a `right`. */
}

td{
    text-align:center;
}
th{
    text-align:center;
}
</style>
<?php
    if(!empty($_GET['course']))
    {
        $course_id=$_GET['course'];
        $coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
        
        require '../templates/clo_wise_report_template.php';

        echo "<a class='btn btn-default' href='./report_teacher.php?course=$course_id'>Go Back</a>";
    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./teacher_courses.php">Back</a>
    <?php
    }
    echo $OUTPUT->footer();
?>
