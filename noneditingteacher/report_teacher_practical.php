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
?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
<?php

    if(!empty($_GET['course']))
    {
        $course_id=$_GET['course'];
		$course_id = (int)$course_id; // convert course id from string to int
		$coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());

        $rec=$DB->get_records_sql('SELECT id,assessment FROM mdl_practical_assessment WHERE courseid = ?',array($course_id));
        ?>
        <link rel="stylesheet" type="text/css" href="../css/cool-link/style.css" />

        <div>
            <a <?php echo "href='./add_assessment.php?course=$course_id'" ?> class="btn btn-primary btn-lg"><i class="fa fa-plus"></i> Add New Assessment</a><br><br>
        </div>
        
        <?php
        if(!empty($_GET['delete'])){
            $id_d=$_GET['delete'];

            try {
                $transaction = $DB->start_delegated_transaction();

                $sql_delete1="DELETE from mdl_practical_assessment where id=?";
                $DB->execute($sql_delete1, array($id_d));

                $sql_delete2="DELETE from mdl_assessment_attempt where aid=?";
                $DB->execute($sql_delete2, array($id_d));

                $transaction->allow_commit();

                $delmsg = "<font color='green'><b>Assessment has been deleted!</b></font><br />";
                $redirectpage = "./report_teacher_practical.php?course=$course_id";
                redirect($redirectpage);

            } catch(Exception $e) {
                $transaction->rollback($e);
                $delmsg = "<font color='red'><b>Assessment failed to delete!</b></font><br />";
            }

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

                <h4><a href="javascript:void(0)" onclick="toggle_visibility('as<?php echo $a ?>')" class="cool-link"><?php echo "$assessment</a> &nbsp; &nbsp; <a href='report_teacher_practical.php?delete=$id&course=$course_id' onClick=\"return confirm('Are you sure you want to delete this assessment and its attempts?')\" title='Delete'><i class='icon fa fa-trash text-danger' aria-hidden='true' title='Delete' aria-label='Delete'></i></a>"?></h4><br>
                <div id="as<?php echo $a ?>" style="display: none">
                    <!--&nbsp;&nbsp;&nbsp;<a <?php echo "href='./print_grading_sheet.php?course=$course_id&assessmentid=$id'" ?>  class="cool-link">&#10070; Print Empty Grading Sheet </a><br>-->
                    &nbsp;&nbsp;&nbsp;<a <?php echo "href='./assessment_marks.php?course=$course_id&assessmentid=$id'" ?>  class="cool-link">&#10070; Enter Assessment Marks</a><br>
                    &nbsp;&nbsp;&nbsp;<a <?php echo "href='./view_result.php?course=$course_id&assessmentid=$id'" ?>  class="cool-link">&#10070; View Result</a>
                    <br>
                    &nbsp;&nbsp;&nbsp; <a <?php echo "href='./grading_sheet.php?course=$course_id&assessmentid=$id'" ?>  class="cool-link">&#10070; Print Empty Grading Sheet</a><br>
                     &nbsp;&nbsp;&nbsp;<a <?php echo "href='./upload_results.php?course=$course_id&assessmentid=$id'" ?>  class="cool-link">&#10070; Upload Result</a><br>
                </div>
                <hr />

            <?php
                $a++;
            }
            ?>
            <h4><a <?php echo "href='./assessment_clo_report.php?course=$course_id'" ?> class="cool-link">Assessment CLO Report</a></h4><br>
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
    }
    else{
        ?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="../teacher/teacher_courses.php">Back</a>
    <?php
    }
    echo $OUTPUT->footer();
    ?>
