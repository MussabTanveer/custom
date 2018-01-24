<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Chairman");
    $PAGE->set_heading("Courses to Grade");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/course_select_to_grading.php');
    echo $OUTPUT->header();
    require_login();
?>
 <link rel="stylesheet" type="text/css" href="../css/cool-link/style.css" />

<?php

$rec=$DB->get_records_sql('SELECT id, fullname, shortname, idnumber
    
    FROM mdl_course where fullname NOT LIKE "CIS" ');

 $serialno = 0;
        $table = new html_table();
        $table->head = array('S. No.','Full Name', 'Short Name' , 'Course Code');
        foreach ($rec as $records) {
            $serialno++;
            $id = $records->id;
            $fname = $records->fullname;
            $sname = $records->shortname;
            $idnum = $records->idnumber;
            $table->data[] = array($serialno, "<a href='./add_mid_and_final.php?course=$id'>$fname</a>", "<a href='./add_mid_and_final.php?course=$id'>$sname</a>", "<a href='./add_mid_and_final.php?course=$id'>$idnum</a>");
        }
        if($serialno == 1){
            redirect("./add_mid_and_final.php?course=$id");
        }
        echo html_writer::table($table);

        echo $OUTPUT->footer();
        ?>