<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Delete Result");
    $PAGE->set_heading("Delete Result");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/noneditingteacher/delete_assessment_marks.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    if(!empty($_GET['aid']) && !empty($_GET['userId']) && !empty($_GET['courseid']))
    {
        $aid = $_GET['aid'];
        $userId = $_GET['userId'];
        $courseid = $_GET['courseid'];
        $courseid = (int)$courseid; // convert course id from string to int
        $coursecontext = context_course::instance($courseid);
        is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());

        $sql = "DELETE FROM mdl_assessment_attempt WHERE aid = $aid AND userid =$userId";
        $DB->execute($sql);
        $redirect = "view_result.php?course=$courseid&assessmentid=$aid";
        redirect($redirect);
    }

    else
        echo "<font color=red > Error! </font>";
?>