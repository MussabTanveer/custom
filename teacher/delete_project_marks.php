<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Delete Result");
    $PAGE->set_heading("Delete Result");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/delete_project_marks.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

?>

<?php

    if(!empty($_GET['delete']) && !empty($_GET['courseid']) && !empty($_GET['proid']))
    {
        $marksId = $_GET['delete'];
        $courseid = $_GET['courseid'];
        $proid = $_GET['proid'];

      $sql = "DELETE FROM mdl_manual_assign_pro_attempt WHERE id = $marksId AND assignproid =$proid";
        $DB->execute($sql);
         $redirect = "view_project.php?courseid=$courseid&projectid=$proid";
                    redirect($redirect);



      }

      else
        echo "<font color=red > Error! </font>";
       ?>