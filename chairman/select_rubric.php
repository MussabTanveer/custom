<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Choose Rubric");
    $PAGE->set_heading("Choose a Rubric");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/select_rubric.php');
    
    echo $OUTPUT->header();
	require_login();
	$rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());

    // Dispaly all rubrics
    $rec=$DB->get_records_sql('SELECT * FROM mdl_rubric');
	if($rec){
        $serialno = 0;
        $table = new html_table();
        $table->head = array('S. No.', 'Rubric', 'Description','Delete');
		foreach ($rec as $records) {
            $serialno++;
			$id = $records->id;
            $name = $records->name;
            $description = $records->description;
            $table->data[] = array($serialno, "<a href='./view_rubric.php?rubric=$id'>$name</a>", "<a href='./view_rubric.php?rubric=$id'>$description</a>","<a href='./delete_rubric.php?id=$id' title='Delete' onClick=\"return confirm('Delete Rubric?')\" > <img src='../img/icons/Delete1.png' /></a>");
        }
        echo html_writer::table($table);
        
	}
	else{
        echo "<h3>No Rubric found!</h3>";
    }

    echo $OUTPUT->footer();

?>
