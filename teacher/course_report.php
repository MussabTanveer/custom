<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Course Report");
    $PAGE->set_heading("Course Report");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/course_report.php');
    echo $OUTPUT->header();
    require_login();

    if(isset($_GET['course']))
    {
        $course_id=$_GET['course'];
        // Get Grading Items
        $rec=$DB->get_records_sql("SELECT * FROM mdl_grading_policy gp, mdl_grading_mapping mg WHERE gp.courseid = ? AND gp.id = mg.gradingitem ORDER BY mg.id", array($course_id));

        if($rec){
            $modules = array();
            $instances = array();
            $gnames = array();
            foreach($rec as $records){
                $module = $records->module;
                $instance = $records->instance ;
                $gname = $records->name;
                array_push($modules,$module);
                array_push($gnames,$gname);
                array_push($instances,$instance);

            }
            //var_dump($modules);
            //var_dump($gnames);
            //var_dump($instances);
            ?>
            <table class="generaltable" border="1">
                <tr>
                <?php
                if(in_array("final exam", $gnames)){
                    $pos = array_search('final exam', $gnames);
                    $quiz_ques=$DB->get_records_sql('SELECT * from mdl_quiz_slots WHERE quizid=?', array($instances[$pos]));
                    $tot_ques = count($quiz_ques); //echo $tot_ques;
                    ?>
                    <th></th>
                    <th colspan="<?php echo $tot_ques ?>">Final Exam</th>
                    <?php
                }
                ?>
                </tr>
                <tr></tr>
            </table>
            <?php
        }
    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./teacher_courses.php">Back</a>
    <?php
    }
    echo $OUTPUT->footer();
?>
