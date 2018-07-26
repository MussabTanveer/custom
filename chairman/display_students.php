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
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <?php

    if(isset($_POST['inputtext'])){
        $nam=$_POST['inputtext'];
    }
    //query to get all cohorts
    $rec2=$DB->get_records_sql('SELECT c.name from mdl_cohort as c');
?>

    <form action="action_file.php" class="form-inline">
        <h5 autofocus style="text-align:center;">Filter Records By</h5>
        <p style="text-align:center;">
        <select autofocus id="Filtercheck" name="dropdown" onChange="cohortdropdown()" class="select custom-select">
        <option value="seatnumber">Seat number</option>
        <option value="batchnumber">Batch number</option>
        <option value="name">Name</option>
        <option value="cohort">Cohort</option>
        </select>
        <select style="display:none"  id="cohort_dd" name="cohort_dd" class="select custom-select">
        <?php
        foreach ($rec2 as $records2) {
            $name =  $records2->name;					
        ?>
        <option value='<?php echo $name; ?>'><?php echo $name; ?></option>
        <?php
        }
        ?>
        <input id="inputcheck" name="inputtext" type="text" placeholder="Enter Value" class="form-control">
        <input id="buttoncheck" type="submit" class="btn btn-info">
        </p>
    </form>
    <br />
    <script>
    function cohortdropdown(){
        var a=document.getElementById('Filtercheck').value;
        if (a=='cohort'){
            document.getElementById('inputcheck').style.display="none";
            document.getElementById('cohort_dd').style.display="inline";
        }
        else{
            document.getElementById('inputcheck').style.display="inline";
            document.getElementById('cohort_dd').style.display="none";
        }
    }
    </script>

<?php
    //query to show all students
    $rec=$DB->get_records_sql('SELECT distinct u.id, u.username, CONCAT( u.firstname, " ", u.lastname ) AS student , u.idnumber FROM mdl_course as c, mdl_role_assignments AS ra, mdl_user AS u, mdl_context AS ct WHERE c.id = ct.instanceid AND ra.roleid =5 AND ra.userid = u.id AND ct.id = ra.contextid ORDER BY u.idnumber');
    
    $serialno=0;
    $batches=array();

    if($rec) //executing query to display all students wrt batches
   	{
        foreach ($rec as $records) // first collect all batches
        {
            $flag=1;
            $studentName=$records->student;
            $studentIdNumber=$records->idnumber;
            $studentUname=$records->username;
            $sid=$records->id;
            $sbatch = substr($studentUname,3,2);

            foreach ($batches as $batch) { // check if batch already pushed
                //echo $batch;
                if ($sbatch == $batch)
                    $flag=0;
            }
            //echo $flag;
            if($flag == 1)
                array_push($batches, $sbatch); // push batch to batches array
        }
        //var_dump($batches);
        $batchIndex=0;

        foreach ($batches as $batch) // display particular batch
        {
            $serialno=0;
            // echo "$batch <br>";
            $table = new html_table();
            echo "<h3 align=center> Batch-$batch</h3>";
            $table->head = array('S. No.','Name', 'Seat No.');

            foreach ($rec as $records) // dispaly student rec of particular batch
            {
                $studentName=$records->student;
                $studentIdNumber=$records->idnumber;
                $studentUname=$records->username;
                $sid=$records->id;
                $sbatch = substr($studentUname,3,2);
                // array_push($batches, $batch);

                if ($sbatch == $batch)
                {
                    $serialno++;
                    $table->data[] = array($serialno, "<a href='display_course_progress.php?sid=$sid'>$studentName</a>", "<a href='display_course_progress.php?sid=$sid'>$studentUname");
                }
           
            }

            echo html_writer::table($table);
        }
   	}
    echo "<a class='btn btn-default' href='./report_chairman'>Go Back</a>";
    echo $OUTPUT->footer();
?>
