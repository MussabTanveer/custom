<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Select Batch");
    $PAGE->set_heading("Select Batch");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/select_batch.php');
    
    echo $OUTPUT->header();
	require_login();
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());

    $rec=$DB->get_records_sql('SELECT * FROM mdl_batch');
    if($rec)
    {   
        $serialNo=0;
        $table = new html_table();
        $table->head = array('S. No.', 'Batch');
        foreach ($rec as $records) 
        {
            $serialNo++;
            $id=$records->id;
            $name=$records->name;
            $table->data[] = array($serialNo,"<a href='fw_selection.php?id=$id'>$name</a>");
        }
         echo html_writer::table($table);
    }
    echo $OUTPUT->footer();
?>