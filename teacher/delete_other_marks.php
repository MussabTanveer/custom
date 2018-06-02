<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Delete Result");
    $PAGE->set_heading("Delete Result");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/delete_other_marks.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

?>

<?php

    if(!empty($_GET['delete']) && !empty($_GET['courseid']) && !empty($_GET['otherid']))
    {
        $marksId = $_GET['delete'];
        $courseid = $_GET['courseid'];
        $otherid = $_GET['otherid'];

      $sql = "DELETE FROM mdl_manual_other_attempt WHERE id = $marksId AND otherid =$otherid";
        $DB->execute($sql);
         $redirect = "view_other2.php?courseid=$courseid&otherid=$otherid";
                    redirect($redirect);



      }

      else
        echo "<font color=red > Error! </font>";
       ?>