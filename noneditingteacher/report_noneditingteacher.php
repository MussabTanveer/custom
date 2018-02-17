<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Non-Editing Teacher");
    $PAGE->set_heading("Non-Editing Teacher Courses");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/report_noneditingteacher.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();


    $rec=$DB->get_records_sql('SELECT c.id, c.fullname , c.shortname, c.idnumber
    
    FROM mdl_course c

    INNER JOIN mdl_context cx ON c.id = cx.instanceid

    AND cx.contextlevel = ? 

    INNER JOIN mdl_role_assignments ra ON cx.id = ra.contextid

    INNER JOIN mdl_role r ON ra.roleid = r.id

    INNER JOIN mdl_user usr ON ra.userid = usr.id

    WHERE r.shortname = ?

    AND usr.id = ?', array('50','teacher', $USER->id));

    if($rec){

        $serialno = 0;
        $table = new html_table();
        $table->head = array('S. No.','Non-Editing Courses', 'Short Name' , 'Course Code');
        foreach ($rec as $records) {
            $serialno++;
            $id2 = $records->id;
            $fname2 = $records->fullname;
            $sname2 = $records->shortname;
            $idnum2= $records->idnumber;
            $table->data[] = array($serialno, "<a href='../../../course/view.php?id=$id2'>$fname2</a>", "<a href='../../../course/view.php?id=$id2'>$sname2</a>", "<a href='../../../course/view.php?id=$id2'>$idnum2</a>");
        }
        echo html_writer::table($table);
        echo "<br />";
    }

echo $OUTPUT->footer();

?>
