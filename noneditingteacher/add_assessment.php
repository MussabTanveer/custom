<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Adding Assessments");
    $PAGE->set_heading("Adding Assessments");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/noneditingteacher/add_assessment.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    $course_id=$_GET['course'];
    
    //echo $course_id; 

    $redirect_page1="./report_teacher_practical.php?course=$course_id";
    $rec=$DB->get_records_sql('SELECT assessment FROM mdl_practical_assessment WHERE courseid = ?',array($course_id));

    foreach ($rec as $records) {
        $assessment = $records->assessment;
    }

    if(empty($assessment)){
        try {
            $transaction = $DB->start_delegated_transaction();
            $record = new stdClass();
            $record->courseid=$course_id;
            $record->assessment='Assessment 1';
            
            $assessmentid = $DB->insert_record('practical_assessment', $record);
            $transaction->allow_commit();
        }
        catch(Exception $e) {
            $transaction->rollback($e);
        }
    }
    else{
        $number=substr($assessment, 11);
        //echo $number;
        $number++;
        try {
            $transaction = $DB->start_delegated_transaction();
            $record = new stdClass();
            $record->courseid=$course_id;
            $record->assessment='Assessment'." ".$number;
            
            $assessmentid = $DB->insert_record('practical_assessment', $record);
            $transaction->allow_commit();
        }
        catch(Exception $e) {
            $transaction->rollback($e);
        }
    }
    redirect($redirect_page1);
    echo $OUTPUT->footer();
?>
