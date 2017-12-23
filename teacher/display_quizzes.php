<script src="../script/jquery/jquery-3.2.1.js"></script>

<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("My Quizzes");
    $PAGE->set_heading("Quizzes");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/display_quizzes.php');
    
    echo $OUTPUT->header();
    require_login();

    if((isset($_POST['submit']) && isset( $_POST['courseid'])) || (isset($SESSION->cid1) && $SESSION->cid1 != "xyz"))
    {
        if(isset($SESSION->cid1) && $SESSION->cid1 != "xyz")
        {
            $course_id=$SESSION->cid1;
            $SESSION->cid1 = "xyz";
        }
        else
            $course_id=$_POST['courseid'];
        //echo "Course ID : $course_id";
    
        // Dispaly all quizzes
        //$rec=$DB->get_records_sql('SELECT * FROM  `mdl_quiz` WHERE course = ? AND timeopen != ?', array($course_id, 0));
        $rec=$DB->get_records_sql('SELECT * FROM  `mdl_quiz` WHERE course = ? AND id IN (SELECT quiz FROM `mdl_quiz_attempts`)', array($course_id));
        if($rec){
            ?>
            <form method='post' action='display_quiz_grid.php' id="form_check">
            <?php
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Name', 'Intro', 'Select');
            foreach ($rec as $records) {
                $serialno++;
                $id = $records->id;
                $courseid = $records->course;
                $name = $records->name;
                $intro = $records->intro;
                $table->data[] = array($serialno, $name, $intro, '<input type="radio" value="'.$id.'" name="quizid">');
            }
            echo html_writer::table($table);
            ?>
            <input type="submit" value="NEXT" name="submit" class="btn btn-primary">
            </form>
            <br />
            <p id="msg"></p>

            <script>
            $('#form_check').on('submit', function (e) {
                if ($("input[type=radio]:checked").length === 0) {
                    e.preventDefault();
                    $("#msg").html("<font color='red'>Select any one quiz!</font>");
                    return false;
                }
            });
            </script>

            <?php
            echo $OUTPUT->footer();
        }
        else{
            echo "<h3>No quizzes found!</h3>";
            echo $OUTPUT->footer();
        }

    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./display_courses.php">Back</a>
    <?php
        echo $OUTPUT->footer();
    }?>
