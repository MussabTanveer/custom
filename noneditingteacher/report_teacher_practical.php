<script src="../script/jquery/jquery-3.2.1.js"></script>
<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Non-Editing Teacher");
    $PAGE->set_heading("Assessments");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/noneditingteacher/report_teacher_practical.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
 
    $course_id=$_GET['course'];
    //echo $course_id; 
    $rec=$DB->get_records_sql('SELECT id,assessment FROM mdl_practical_assessment WHERE courseid = ?',array($course_id));
    ?>
    <link rel="stylesheet" type="text/css" href="../css/cool-link/style.css" />

    <div>
        <a <?php echo "href='./add_assessment.php?course=$course_id'" ?> class="btn btn-primary btn-lg"><i class="fa fa-plus"></i> Add New Assessment</a><br><br>
    </div>
    
    <?php
if(isset($_GET['delete'])){

$id_d=$_GET['delete'];

$sql_delete="DELETE from mdl_practical_assessment where id=$id_d";
            $DB->execute($sql_delete);
            $delmsg = "<font color='green'><b>Assessment has been deleted!</b></font><br />";
            redirect('../teacher/teacher_courses.php');
}

    elseif(empty($rec)){
        //echo "<h4>You have yet to add an Assessment, Click above to add one!</h4>";
    }
    elseif(!empty($rec)){
        //echo "<h4>View Already Made Assessments:</h4>";
        $assessmentarray = array();
        $a=1;
        foreach ($rec as $record) {
            //echo "outerloop";
            $id=$record->id;
            $assessment = $record->assessment;
            array_push($assessmentarray, $assessment);?>

            <h4><a href="javascript:void(0)" onclick="toggle_visibility('as<?php echo $a ?>')" class="cool-link"><?php echo "$assessment</a> &nbsp; &nbsp; <a href='report_teacher_practical.php?delete=$id&course=$course_id' onClick=\"return confirm('Delete Assessment?')\" title='Delete'> <img src='../img/icons/delete.png' /></a>"?></h4><br>
            <div id="as<?php echo $a ?>" style="display: none">
                <!--&nbsp;&nbsp;&nbsp;<a <?php echo "href='./print_grading_sheet.php?course=$course_id&assessmentid=$id'" ?>  class="cool-link">&#10070; Print Empty Grading Sheet </a><br>-->
                &nbsp;&nbsp;&nbsp;<a <?php echo "href='./assessment_marks.php?course=$course_id&assessmentid=$id'" ?>  class="cool-link">&#10070; Enter Assessment Marks</a><br>
                &nbsp;&nbsp;&nbsp;<a <?php echo "href='./view_result.php?course=$course_id&assessmentid=$id'" ?>  class="cool-link">&#10070; View Result</a><br>
            </div>
            <hr />

        <?php
            $a++;
        }
        ?>
    <?php
    }
    ?>
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