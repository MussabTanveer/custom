<script src="../script/jquery/jquery-3.2.1.js"></script>
<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Students");
    $PAGE->set_heading("Select Student");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/action_file.php');
    echo $OUTPUT->header();
    require_login();
    ?>
    <?php
    if ((isset($_GET['cohort_dd'])||isset($_GET['inputtext'])) && isset($_GET['dropdown'])){
        $name=$_GET['inputtext'];
        $dropdown=$_GET['dropdown'];
        $cohortdown=$_GET['cohort_dd'];
        $bat=substr("$name",2,2);
    }
    ?>
    <script>
    // window.onload=alert(<?php echo $name ?>);
    </script>
    <?php
    if($dropdown=='name'){
    $rec=$DB->get_records_sql('SELECT distinct u.id, u.username, CONCAT( u.firstname, " ", u.lastname ) AS student , u.idnumber FROM mdl_course as c, mdl_role_assignments AS ra, mdl_user AS u, mdl_context AS ct WHERE c.id = ct.instanceid AND ra.roleid =5 AND ra.userid = u.id AND ct.id = ra.contextid AND (CONCAT( u.firstname, " ", u.lastname ) = ? OR CONCAT( u.firstname, " ", u.lastname ) LIKE ?) ORDER BY u.idnumber;',array($name,"%$name%"));
    $rec||die('<h3> No data available or wrong entry <h3>'.$OUTPUT->footer());
    }
    if($dropdown=='seatnumber'){
    $rec=$DB->get_records_sql('SELECT distinct u.id, u.username, CONCAT( u.firstname, " ", u.lastname ) AS student , u.idnumber FROM mdl_course as c, mdl_role_assignments AS ra, mdl_user AS u, mdl_context AS ct WHERE c.id = ct.instanceid AND ra.roleid =5 AND ra.userid = u.id AND ct.id = ra.contextid AND (u.username=? OR u.username LIKE ? )  ORDER BY u.idnumber;',array($name,"%$name%"));
    $rec||die('<h3> No data available or wrong entry <h3>'.$OUTPUT->footer());
    }
    if($dropdown=='batchnumber'){
    $rec=$DB->get_records_sql('SELECT distinct u.id, u.username, CONCAT( u.firstname, " ", u.lastname ) AS student , u.idnumber FROM mdl_course as c, mdl_role_assignments AS ra, mdl_user AS u, mdl_context AS ct WHERE c.id = ct.instanceid AND ra.roleid =5 AND ra.userid = u.id AND ct.id = ra.contextid AND (substr(u.username,4,2)=? OR substr(u.username,4,2) LIKE ? )  ORDER BY u.idnumber;',array($bat,"%$bat%"));
    $rec||die('<h3> No data available or wrong entry <h3>'.$OUTPUT->footer());
    }
    if($dropdown == 'cohort'){
    $rec=$DB->get_records_sql('SELECT distinct u.id, u.username, CONCAT( u.firstname, " ", u.lastname ) AS student , u.idnumber from mdl_course as c, mdl_role_assignments AS ra, mdl_user AS u, mdl_context AS ct,mdl_cohort AS co,mdl_cohort_members AS cm WHERE co.id = cm.cohortid AND cm.userid = u.id AND c.id = ct.instanceid AND ra.roleid =5 AND ra.userid = u.id AND ct.id = ra.contextid AND co.name ="'.$cohortdown.'" ORDER BY u.idnumber;');
    $rec||die('<h3> No data available <h3>'.$OUTPUT->footer());
    }
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
    echo $OUTPUT->footer();
?>
