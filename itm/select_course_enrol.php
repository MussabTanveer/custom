<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Choose Course");
    $PAGE->set_heading("Choose Course for User Enrolment");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/itm/select_course_enrol.php');
    
    require_login();
    if($SESSION->oberole != "itm"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
	
	$courses=$DB->get_records_sql('SELECT * FROM `mdl_course` WHERE id != ?', array(1));
    
    if($courses){
        $i = 1;
        foreach ($courses as $records){
            $fullname = $records->fullname;
            $shortname = $records->shortname;
            $idnumber = $records->idnumber;
            $id=$records->id;
            echo "<h4><a href='../../../enrol/users.php?id=$id' title='Enrol Users'>$i. $fullname ($shortname $idnumber)</a></h4><br />";
            $i++;
        }
    }
    else{
        echo "<h3>No courses found!</h3>";
    }
    echo $OUTPUT->footer();
?>
