<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("CLO Wise Report");
    $PAGE->set_heading("CLO Wise Report");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/clo_wise_report_chairman.php');
    
	require_login();
    echo $OUTPUT->header();
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
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
    if(isset($_POST['course']) && isset($_POST['tid']))
    {
        $course_id=$_POST['course'];
        $tid=$_POST['tid'];

        require '../templates/clo_wise_report_template.php';

        echo "<a class='btn btn-default' href='./display_courses-3.php?tid=$tid'>Go Back</a>";
    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./teacher_courses.php">Back</a>
    <?php
    }
    echo $OUTPUT->footer();
?>
