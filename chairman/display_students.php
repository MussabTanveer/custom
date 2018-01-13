<script src="../script/jquery/jquery-3.2.1.js"></script>

<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Students");
    $PAGE->set_heading("Select Student");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/display_students.php');
    
    echo $OUTPUT->header();
    require_login();

    //query to show all students
    $rec=$DB->get_records_sql('SELECT distinct u.id, CONCAT( u.firstname, " ", u.lastname ) AS student , u.idnumber FROM mdl_course as c, mdl_role_assignments AS ra, mdl_user AS u, mdl_context AS ct WHERE c.id = ct.instanceid AND ra.roleid =5 AND ra.userid = u.id AND ct.id = ra.contextid ORDER BY u.idnumber');
    
    $serialno=0;
    $batches=array();

    if($rec)//executing query to display all students
   	{
        foreach ($rec as $records)
        {
            $flag=1;
            $studentName=$records->student;
            $studentIdNumber=$records->idnumber;
            $sid=$records->id;
            $sbatch = substr($studentIdNumber,3,2);

            foreach ($batches as $batch) {
                //echo $batch;
                if ($sbatch == $batch)
                    $flag=0;
            }
            //echo $flag;
            if ($flag == 1)
                array_push($batches, $sbatch);
        }
        //var_dump($batches);
        $batchIndex=0;
         
         //var_dump($batches);

    foreach ($batches as $batch)
       {
        $serialno=0;
           // echo "$batch <br>";
            $table = new html_table();
            echo "<h3 align=center> Batch-$batch</h3>";
         $table->head = array('S. No.','Name', 'Seat No.');

        foreach ($rec as $records)
        {
            $studentName=$records->student;
            $studentIdNumber=$records->idnumber;
            $sid=$records->id;
            $sbatch = substr($studentIdNumber,3,2);
           // array_push($batches, $batch);

            if ($sbatch == $batch)
            {
                 $serialno++;
            $table->data[] = array($serialno, "<a href='display_course_progress.php?sid=$sid'>$studentName</a>", "<a href='display_course_progress.php?sid=$sid'>$studentIdNumber");

            }
           
        }

     echo html_writer::table($table);
        }

       
   	}
    echo $OUTPUT->footer();
?>
