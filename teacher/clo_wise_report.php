<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("CLO Wise Report");
    $PAGE->set_heading("CLO Wise Report");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/clo_wise_report.php');
    
	require_login();
	if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
	}
	echo $OUTPUT->header();


    



     echo $OUTPUT->footer();

