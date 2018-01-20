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
        $rec=$DB->get_records_sql("SELECT * FROM mdl_grading_policy WHERE courseid = ? ORDER BY id", array($course_id));

        if($rec){
            $gids = array();
            $gnames = array();
            foreach($rec as $records){
                $gid = $records->id;
                $gname = $records->name;
                array_push($gids,$gid);
                array_push($gnames,$gname);
            }
            ?>
            <table class="generaltable">
                <tr>
                <?php
                if(in_array("final exam", $gnames)){

                    ?>
                    <th></th>
                    <th>Final Exam</th>
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
