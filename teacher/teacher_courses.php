<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Teacher");
    $PAGE->set_heading("Courses");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/report_teacher.php');
    echo $OUTPUT->header();
    require_login();
?>
    <link rel="stylesheet" type="text/css" href="../css/cool-link/style.css" />
<?php
	$rec=$DB->get_records_sql('SELECT c.id, c.fullname, c.shortname, c.idnumber
    
    FROM mdl_course c

    INNER JOIN mdl_context cx ON c.id = cx.instanceid

    AND cx.contextlevel = ?

    INNER JOIN mdl_role_assignments ra ON cx.id = ra.contextid

    INNER JOIN mdl_role r ON ra.roleid = r.id

    INNER JOIN mdl_user usr ON ra.userid = usr.id

    WHERE r.shortname = ?

    AND usr.id = ?', array('50', 'editingteacher', $USER->id));
    if($rec){
        $serialno = 0;
        $table = new html_table();
        $table->head = array('S. No.','Full Name', 'Short Name' , 'Course Code');
        foreach ($rec as $records) {
            $serialno++;
            $id = $records->id;
            $fname = $records->fullname;
            $sname = $records->shortname;
            $idnum = $records->idnumber;
            $table->data[] = array($serialno, "<a href='./report_teacher.php?course=$id'>$fname</a>", "<a href='./report_teacher.php?course=$id'>$sname</a>", "<a href='./report_teacher.php?course=$id'>$idnum</a>");
        }
        if($serialno == 1){
            redirect("./report_teacher.php?course=$id");
        }
        echo html_writer::table($table);
        ?>
        
        <br />
        <p id="msg"></p>
        
        <?php
    }
    else{
        echo "<h3>You are not enrolled as teacher in any course!</h3>";
    }

    echo $OUTPUT->footer();

?>
