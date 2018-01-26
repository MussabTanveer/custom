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
 $time=time();
$rec=$DB->get_records_sql('SELECT id, fullname, shortname, idnumber
    
    FROM mdl_course where fullname NOT LIKE "CIS" AND enddate > ? ',array($time));
if($rec){
 $serialno = 0;
        $table = new html_table();
        $table->head = array('S. No.','Present Courses', 'Short Name' , 'Course Code');
        foreach ($rec as $records) {
            $serialno++;
            $id = $records->id;
            $fname = $records->fullname;
            $sname = $records->shortname;
            $idnum = $records->idnumber;
            $table->data[] = array($serialno, "<a href='./add_mid_and_final.php?course=$id'>$fname</a>", "<a href='./add_mid_and_final.php?course=$id'>$sname</a>", "<a href='./add_mid_and_final.php?course=$id'>$idnum</a>");
        }
        //if($serialno == 1){
            //redirect("./add_mid_and_final.php?course=$id");
      //  }
        echo html_writer::table($table);


        }

$rec1=$DB->get_records_sql('SELECT id, fullname, shortname, idnumber
    
    FROM mdl_course where fullname NOT LIKE "CIS" AND enddate <= ? ',array($time));


if($rec1){
 $serialno = 0;
        $table = new html_table();
        $table->head = array('S. No.','Past Courses', 'Short Name' , 'Course Code');
        foreach ($rec1 as $records) {

 $serialno++;
            $id1 = $records->id;
            $fname1 = $records->fullname;
            $sname1 = $records->shortname;
            $idnum1= $records->idnumber;
            $table->data[] = array($serialno, "<a href='./add_mid_and_final.php?course=$id1'>$fname1</a>", "<a href='./add_mid_and_final.php?course=$id'>$sname1</a>", "<a href='./add_mid_and_final.php?course=$id'>$idnum1</a>");
        }

  echo html_writer::table($table);

}
?>
        
    
<?php

echo $OUTPUT->footer();


        ?>
