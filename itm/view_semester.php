<?php 
   require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Semesters");
    $PAGE->set_heading("Semesters");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/view_semester.php');
    
    require_login();
    if($SESSION->oberole != "itm"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    $rec= $DB->get_records_sql("SELECT * FROM mdl_semester ORDER BY id DESC");
    if($rec)
    {
        echo "<h4 style='color:navy'>Note: Semesters are displayed in reverse chronological order.</h4><br>";
        $serialno = 0;
        $table = new html_table();
        $table->head = array('S. No.','Name', 'Year' , 'Start Date', 'End Date', 'Edit', 'Delete');
        foreach ($rec as $records) {
			$serialno++;
            $id = $records->id;
            $name = $records->name;
            $year = $records->year;
            $startdate = $records->startdate; $startdate = date('d-m-y', $startdate);
            $enddate = $records->enddate; $enddate = date('d-m-y', $enddate);
            $table->data[] = array($serialno, $name, $year, $startdate, $enddate, "<a href='./edit_semester.php?semester=$id' title='Edit') ><i class='icon fa fa-pencil text-info' aria-hidden='true' title='Edit' aria-label='Edit'></i></a>", "<a href='./delete_semester.php?semester=$id' title='Delete' onClick=\"return confirm('Are you sure you want to delete this semester?')\" ><i class='icon fa fa-trash text-danger' aria-hidden='true' title='Delete' aria-label='Delete'></i></a>");
        }
        echo html_writer::table($table);
        echo "<br />";
    }
    else
        echo "<h3>No semester found!</h3>";
    ?>
    <a class="btn btn-default" href="./report_itm.php">Go Back</a>
    <?php
    echo $OUTPUT->footer();    
?>