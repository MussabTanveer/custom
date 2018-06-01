<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Delete Result");
    $PAGE->set_heading("Delete Result");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/delete_quiz_marks.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

?>

<?php

    if(!empty($_GET['quizid']) && !empty($_GET['userId']) && !empty($_GET['qId'])  && !empty($_GET['courseid']))
    {
        $quizId = $_GET['quizid'];
        $userId = $_GET['userId'];
        $qId = $_GET['qId'];
        $courseid = $_GET['courseid'];

      $sql = "DELETE FROM mdl_manual_quiz_attempt WHERE quizid = $quizId AND userid =$userId ";
        $DB->execute($sql);
         $redirect = "view_activity_result1.php?courseid=$courseid&quiz=$quizId";
                    redirect($redirect);



      }

      else
        echo "<font color=red > Error! </font>";
       ?>