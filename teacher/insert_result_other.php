<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Upload Result");
    $PAGE->set_heading("Upload Result");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/insert_result_other.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    if(isset($_POST['submit']))
	{
        // GET DATA
        $other_id = $_POST['other_id'];
        
        // Check marks already entered or not
        $edit = 0;
        $check=$DB->get_records_sql('SELECT *  FROM mdl_manual_other_attempt WHERE otherid = ?', array($other_id));
        if($check){
            $edit = 1;
        }

        $sidarray = array();
		foreach ($_POST['studid'] as $sid)
		{
			array_push($sidarray,$sid);
        }
        $marksarray = array();
		foreach ($_POST['marks'] as $mobt)
		{
            if($mobt == "") {
                $mobt = NULL;
            }
			array_push($marksarray,$mobt);	
        }
        //var_dump ($other_id); echo "<br>";
        //var_dump ($sidarray); echo "<br>";
        //var_dump ($marksarray); echo "<br>";

        //FILTER DATA FOR NULL RECORDS
        $len = count($sidarray);
        for ($i=0 ; $i<$len; $i++){ // loop stud id times
            if($marksarray[$i] == NULL){
                //echo "hello$i<br>";
                unset($sidarray[$i]); // remove student seat number
                unset($marksarray[$i]); // remove student marks
            }
        }
        //print_r ($sidarray); echo "<br>";
        //print_r ($marksarray); echo "<br>";
        $sidarray = array_values($sidarray);
        $marksarray = array_values($marksarray);
        //print_r ($sidarray); echo "<br>";
        //print_r ($marksarray); echo "<br>";

        // INSERT DATA
        try {
            $transaction = $DB->start_delegated_transaction();
            for ($j=0 ; $j<sizeof($sidarray); $j++){ // loop stud id times
                if(!$edit) {
                    $record = new stdClass();
                    $record->otherid = $other_id;
                    $record->userid = $sidarray[$j];
                    $record->obtmark = $marksarray[$j];
                    $DB->insert_record('manual_other_attempt', $record);
                }
                else {
                    $sql_update="UPDATE mdl_manual_other_attempt SET obtmark=? WHERE otherid=? AND userid=?";
                    $DB->execute($sql_update, array($marksarray[$j], $other_id, $sidarray[$j]));
                }
                //$sql="INSERT INTO manual_assign_pro_attempt (assignproid,userid,obtmark) VALUES ('$other_id','$sidarray[$j]','$marksarray[$j]')";
                //$DB->execute($sql);
            }
            $transaction->allow_commit();
            echo "<font color = green > Result has been uploaded! </font> <br>";
        } catch(Exception $e) {
            $transaction->rollback($e);
            echo "<font color = red > Failed to upload result! </font> <br>";
        }
    }
    else{
        echo "<font color = red > Invalid form submission! </font> <br>";
    }
    ?>
    <a href="./teacher_courses.php">Back</a>
    <?php
        echo $OUTPUT->footer();
    ?>
