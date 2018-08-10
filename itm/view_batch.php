<?php 
   require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Batches");
    $PAGE->set_heading("Batches");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/view_batch.php');
    
    require_login();
    if($SESSION->oberole != "itm"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    $rec= $DB->get_records_sql("SELECT * FROM mdl_batch ORDER BY id DESC");
    if($rec)
    {
        echo "<h4 style='color:navy'>Note: Batches are displayed in reverse chronological order.</h4><br>";
        $serialno = 0;
        $table = new html_table();
        $table->head = array('S. No.','Name', 'Edit', 'Delete');
        foreach ($rec as $records) {
			$serialno++;
            $id = $records->id;
            $name = $records->name;
            $table->data[] = array($serialno, $name, "<a href='./edit_batch.php?batch=$id' title='Edit') ><i class='icon fa fa-pencil text-info' aria-hidden='true' title='Edit' aria-label='Edit'></i></a>", "<a href='./delete_batch.php?batch=$id' title='Delete' onClick=\"return confirm('Are you sure you want to delete this batch?')\" ><i class='icon fa fa-trash text-danger' aria-hidden='true' title='Delete' aria-label='Delete'></i></a>");
        }
        echo html_writer::table($table);
        echo "<br />";
    }
    else
        echo "<h3>No batch found!</h3>";
    ?>
    <a class="btn btn-default" href="./report_itm.php">Go Back</a>
    <?php
    echo $OUTPUT->footer();    
?>