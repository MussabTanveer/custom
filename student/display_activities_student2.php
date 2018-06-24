<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("My Activities");
    $PAGE->set_heading("Activities");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/display_quizzes.php');
    
    require_login();
    if($SESSION->oberole != "student"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <?php
    if((isset($_POST['submit']) && isset( $_POST['courseid'])) || (isset($SESSION->cid4) && $SESSION->cid4 != "xyz"))
    {
       $course_id=$_POST['courseid'];

        // echo $course_id;
        $coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());

        $recmQ=$DB->get_records_sql('SELECT q.id, q.course, q.name, q.intro 
        FROM  mdl_quiz q, mdl_quiz_attempts qa
        WHERE q.course = ? AND q.id=qa.quiz AND qa.userid = ?',
        array($course_id,$USER->id));
        if($recmQ){

        echo "<h3>Online Quizzes/Midterm</h3>";
?>
        <form method="post" action="display_online_quizreport.php" id="form_check">
        <?php
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Name', 'Intro','Select');
            foreach ($recmQ as $records) {
                $serialno++;
                $id = $records->id;
                $qname=$records->name;
                $qintro=$records->intro;
                
                $table->data[] = array($serialno,$qname,$qintro,'<input type="radio" value="'.$id.'" name="quizid">');
            }

            echo html_writer::table($table);
 
        ?>
        <input type='submit' value='NEXT' name='submit' class="btn btn-primary">
        <?php
        }
        ?>

    </form>
    <br />
    <p id="msg"></p>
    <script>
            $('#form_check').on('submit', function (e) {
                if ($("input[type=radio]:checked").length === 0) {
                    e.preventDefault();
                    $("#msg").html(" ");
                    return false;
                }
            });
            </script>

    <?php

    $reca=$DB->get_records_sql('SELECT a.id, a.course, a.name, a.intro FROM mdl_assign a, mdl_assign_grades ag WHERE a.course = ? AND a.id=ag.assignment AND ag.userid = ?',array($course_id,$USER->id));

    if($reca){

    echo "<h3>Online Assignments</h3>";

    ?>

    <form method="post" action="display_online_assignmentreport.php" id="form_check2">

    <?php
                $serialno = 0;
                $table = new html_table();
                $table->head = array('S. No.', 'Name', 'Intro','Select');
            foreach ($reca as $records) {
                $serialno++;
                $id = $records->id;
                $aname=$records->name;
                $aintro=$records->intro;
    $table->data[] = array($serialno,$aname,$aintro,'<input type="radio" value="'.$id.'" name="assignid">');

            }

    echo html_writer::table($table);
    

    ?>
    
    <input type='submit' value='NEXT' name='submit1' class="btn btn-primary">
    <?php
    }
    ?>
    </form>


    <p id="msg"></p>
    <script>
            $('#form_check2').on('submit', function (e) {
                if ($("input[type=radio]:checked").length === 0) {
                    e.preventDefault();
                    $("#msg").html(" ");
                    return false;
                }
            });
            </script>

    <?php
    $recmq=$DB->get_records_sql('SELECT DISTINCT q.id, q.courseid, q.name, q.description FROM mdl_manual_quiz q, mdl_manual_quiz_attempt qa WHERE q.courseid = ? AND q.id=qa.quizid AND qa.userid = ?',array($course_id,$USER->id));



    if($recmq){

    echo "<h3>Manual Quiz</h3>";

    ?>

    <form method="post" action="display_manual_quizreport.php" id="form_check3">

    <?php
                $serialno = 0;
                $table = new html_table();
                $table->head = array('S. No.', 'Name', 'Intro','Select');
            foreach ($recmq as $records) {
                $serialno++;
                $id = $records->id;
                $mqname=$records->name;
                $mqintro=$records->description;
    $table->data[] = array($serialno,$mqname,$mqintro,'<input type="radio" value="'.$id.'" name="mqid">');

            }

    echo html_writer::table($table);
    

    ?>
    

        <input type='submit' value='NEXT' name='submit' class="btn btn-primary">

    <?php
    }
    ?>
    </form>


    <p id="msg"></p>
    <script>
            $('#form_check3').on('submit', function (e) {
                if ($("input[type=radio]:checked").length === 0) {
                    e.preventDefault();
                    $("#msg").html(" ");
                    return false;
                }
            });
            </script>

    <?php  
    $recma=$DB->get_records_sql('SELECT DISTINCT a.id, a.courseid, a.name, a.description FROM mdl_manual_assign_pro a, mdl_manual_assign_pro_attempt aa WHERE a.courseid = ? AND a.id=aa.assignproid AND a.module=-4 AND aa.userid = ?',array($course_id,$USER->id));

    if($recma){

    echo "<h3>Manual Assignment</h3>";

    ?>

    <form method="post" action="display_manual_assignreport.php" id="form_check4">

    <?php
                $serialno = 0;
                $table = new html_table();
                $table->head = array('S. No.', 'Name', 'Intro','Select');
            foreach ($recma as $records) {
                $serialno++;
                $id = $records->id;
                $maname=$records->name;
                $maintro=$records->description;
    $table->data[] = array($serialno,$maname,$maintro,'<input type="radio" value="'.$id.'" name="maid">');

            }

    echo html_writer::table($table);
    

    ?>
    

        <input type='submit' value='NEXT' name='submit2' class="btn btn-primary">
    <?php
    }
    ?>
    </form>


    <p id="msg"></p>
    <script>
            $('#form_check4').on('submit', function (e) {
                if ($("input[type=radio]:checked").length === 0) {
                    e.preventDefault();
                    $("#msg").html(" ");
                    return false;
                }
            });
            </script>

            <?php
    $recmp=$DB->get_records_sql('SELECT DISTINCT p.id, p.courseid, p.name, p.description FROM mdl_manual_assign_pro p, mdl_manual_assign_pro_attempt pa WHERE p.courseid = ? AND p.id=pa.assignproid AND p.module=-5 AND pa.userid = ?',array($course_id,$USER->id));

    if($recmp){

    echo "<h3>Manual Projects</h3>";

    ?>

    <form method="post" action="display_manual_projectreport.php" id="form_check5">

    <?php
                $serialno = 0;
                $table = new html_table();
                $table->head = array('S. No.', 'Name', 'Intro','Select');
            foreach ($recpa as $records) {
                $serialno++;
                $id = $records->id;
                $mpname=$records->name;
                $mpintro=$records->description;
    $table->data[] = array($serialno,$mpname,$mpintro,'<input type="radio" value="'.$id.'" name="mpid">');

            }

    echo html_writer::table($table);
    

    ?>
    

        <input type='submit' value='NEXT' name='submit' class="btn btn-primary">
    <?php
    }
    ?>


    </form>


    <p id="msg"></p>
    <script>
            $('#form_check5').on('submit', function (e) {
                if ($("input[type=radio]:checked").length === 0) {
                    e.preventDefault();
                    $("#msg").html(" ");
                    return false;
                }
            });
            </script>


        <?php
            
        }
        echo $OUTPUT->footer();
        ?>
