<script src="../script/jquery/jquery-3.2.1.js"></script>

<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("My Activities");
    $PAGE->set_heading("Activities");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/display_quizzes.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    /*if((isset($_POST['submit']) && isset( $_POST['courseid'])) || (isset($SESSION->cid1) && $SESSION->cid1 != "xyz"))
    {
        if(isset($SESSION->cid1) && $SESSION->cid1 != "xyz")
        {
            $course_id=$SESSION->cid1;
            $SESSION->cid1 = "xyz";
        }
        else
            $course_id=$_POST['courseid'];*/

    if(!empty($_GET['course']))
    {
        $course_id=$_GET['course'];
        $coursecontext = context_course::instance($course_id);
		is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
        //echo "Course ID : $course_id";
    
        // Dispaly all Online Quizzes/Midterm
        //$rec=$DB->get_records_sql('SELECT * FROM  `mdl_quiz` WHERE course = ? AND timeopen != ?', array($course_id, 0));
        $recOQ=$DB->get_records_sql('SELECT * FROM  `mdl_quiz` WHERE course = ? AND id IN (SELECT quiz FROM `mdl_quiz_attempts`)', array($course_id));
        if($recOQ){
            echo "<h3>Online Quizzes/Midterm</h3>";
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Name', 'Intro');
            foreach ($recOQ as $records) {
                $serialno++;
                $id = $records->id;
                //$courseid = $records->course;
                $name = $records->name;
                $intro = $records->intro;
                $table->data[] = array($serialno, "<a href='./display_quiz_grid.php?course=$course_id&quizid=$id'>$name</a>", "<a href='./display_quiz_grid.php?course=$course_id&quizid=$id'>$intro</a>");
            }
            echo html_writer::table($table);
            ?>
            <br />
            <?php
        }
        // Dispaly all Online Assignments
        $recOA=$DB->get_records_sql('SELECT * FROM `mdl_assign` WHERE course = ? AND id IN (SELECT assignment FROM `mdl_assign_grades`)', array($course_id));
        if($recOA){
            echo "<h3>Online Assignments</h3>";
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Name', 'Intro');
            foreach ($recOA as $records) {
                $serialno++;
                $id = $records->id;
                //$courseid = $records->course;
                $name = $records->name;
                $intro = $records->intro;
                $table->data[] = array($serialno, "<a href='./display_assign_grid.php?course=$course_id&assignid=$id'>$name</a>", "<a href='./display_assign_grid.php?course=$course_id&assignid=$id'>$intro</a>");
            }
            echo html_writer::table($table);
            ?>
            <br />
            <?php
        }
        // Dispaly all Manual Quizzes
        $recMQ = $DB->get_records_sql("SELECT * FROM mdl_manual_quiz WHERE courseid = ? AND module = ? AND id IN (SELECT quizid FROM `mdl_manual_quiz_attempt`)",array($course_id,-1));
        if($recMQ){
            echo "<h3>Manual Quizzes</h3>";
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Name', 'Intro');
            foreach ($recMQ as $records) {
                $serialno++;
                $id = $records->id;
                //$courseid = $records->course;
                $name = $records->name;
                $intro = $records->description;
                $table->data[] = array($serialno, "<a href='./view_activity_result1.php?quiz=$id&courseid=$course_id'>$name</a>", "<a href='./view_activity_result1.php?quiz=$id&courseid=$course_id'>$intro</a>");
            }
            echo html_writer::table($table);
            ?>
            <br />
            <?php
        }
        // Dispaly all Manual Assignments
        $recMA=$DB->get_records_sql('SELECT * FROM  `mdl_manual_assign_pro` WHERE courseid = ? AND module= ? AND id IN (SELECT assignproid FROM `mdl_manual_assign_pro_attempt`)', array($course_id,'-4'));
        if($recMA){
            echo "<h3>Manual Assignments</h3>";
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Name', 'Intro');
            foreach ($recMA as $records) {
                $serialno++;
                $id = $records->id;
                $name = $records->name;
                $intro = $records->description;
                $table->data[] = array($serialno, "<a href='./view_assignment.php?assignid=$id&courseid=$course_id'>$name</a>", "<a href='./view_assignment.php?assignid=$id&courseid=$course_id'>$intro</a>");
            }
            echo html_writer::table($table);
            ?>
            <br />
            <?php
        }
        // Dispaly all Manual Projects
        $recMP=$DB->get_records_sql('SELECT * FROM  `mdl_manual_assign_pro` WHERE courseid = ? AND module= ? AND id IN (SELECT assignproid FROM `mdl_manual_assign_pro_attempt`)', array($course_id,'-5'));
        if($recMP){
            echo "<h3>Manual Projects</h3>";
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Name', 'Intro');
            foreach ($recMP as $records) {
                $serialno++;
                $id = $records->id;
                $name = $records->name;
                $intro = $records->description;
                $table->data[] = array($serialno, "<a href='./view_project.php?projectid=$id&courseid=$course_id'>$name</a>", "<a href='./view_project.php?projectid=$id&courseid=$course_id'>$intro</a>");
            }
            echo html_writer::table($table);
            ?>
            <br />
            <?php
        }
        // Dispaly all Manual Midterm
        $recMM = $DB->get_records_sql("SELECT * FROM mdl_manual_quiz WHERE courseid = ? AND module = ?",array($course_id,-2));
        if($recMM){
            echo "<h3>Manual Midterm</h3>";
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Name', 'Intro');
            foreach ($recMM as $records) {
                $serialno++;
                $id = $records->id;
                //$courseid = $records->course;
                $name = $records->name;
                $intro = $records->description;
                $table->data[] = array($serialno, "<a href='./view_activity_result1.php?quiz=$id&courseid=$course_id'>$name</a>", "<a href='./view_activity_result1.php?quiz=$id&courseid=$course_id'>$intro</a>");
            }
            echo html_writer::table($table);
            ?>
            <br />
            <?php
        }
        // Dispaly all Manual Final
        $recMF = $DB->get_records_sql("SELECT * FROM mdl_manual_quiz WHERE courseid = ? AND module = ?",array($course_id,-3));
        if($recMF){
            echo "<h3>Manual Final Exam</h3>";
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Name', 'Intro');
            foreach ($recMF as $records) {
                $serialno++;
                $id = $records->id;
                //$courseid = $records->course;
                $name = $records->name;
                $intro = $records->description;
                $table->data[] = array($serialno, "<a href='./view_activity_result1.php?quiz=$id&courseid=$course_id'>$name</a>", "<a href='./view_activity_result1.php?quiz=$id&courseid=$course_id'>$intro</a>");
            }
            echo html_writer::table($table);
            ?>
            <br />
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
