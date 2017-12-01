<script src="./script/jquery/jquery-3.2.1.js"></script>

<?php 
    require_once('../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("My Quizzes");
    $PAGE->set_heading("Quizzes");
    $PAGE->set_url($CFG->wwwroot.'/custom/display_quizzes-2.php');
    
    echo $OUTPUT->header();
    require_login();

    if((isset($_POST['submit']) && isset( $_POST['courseid'])) || isset($SESSION->cid2))
    {	
        if(isset($SESSION->cid2))
            $course_id=$SESSION->cid2;
        else
            $course_id=$_POST['courseid'];
       
        //echo "Course ID : $course_id";
    
        // Dispaly all quizzes
        $recQ=$DB->get_records_sql('SELECT * FROM  `mdl_quiz` WHERE course = ? ', array($course_id));
        $recA=$DB->get_records_sql('SELECT * FROM  `mdl_assign` WHERE course = ? ', array($course_id));
        
        if($recQ || $recA){
            ?>
            <form method='post' action='add_comp_ques.php' id="form_check">
            <?php
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Name', 'Intro', 'Select');
            foreach ($recQ as $records) {
                $serialno++;
                $id = 'Q'.$records->id;
                $courseid = $records->course;
                $name = $records->name;
                $intro = $records->intro;
                $table->data[] = array($serialno, $name, $intro, '<input type="radio" value="'.$id.'" name="activityid">');
            }
            foreach ($recA as $records) {
                $serialno++;
                $id = 'A'.$records->id;
                $courseid = $records->course;
                $name = $records->name;
                $intro = $records->intro;
                $table->data[] = array($serialno, $name, $intro, '<input type="radio" value="'.$id.'" name="activityid">');
            }
			
            echo html_writer::table($table);
            ?>
            <input type="hidden" value='<?php echo $course_id; ?>' name="courseid">
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
        <a href="./display_courses-2.php">Back</a>
    <?php
        echo $OUTPUT->footer();
    }?>
