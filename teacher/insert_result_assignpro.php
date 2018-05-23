<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Upload Result");
    $PAGE->set_heading("Upload Result");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/insert_result_assignpro.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    if(isset($_POST['submit']))
	{
        // GET DATA
        $aspro_id = $_POST['aspro_id'];
        
        // Check marks already entered or not
        $edit = 0;
        $check=$DB->get_records_sql('SELECT *  FROM mdl_manual_assign_pro_attempt WHERE assignproid = ?', array($aspro_id));
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
			array_push($marksarray,$mobt);	
        }
        //var_dump ($aspro_id); echo "<br>";
        //var_dump ($sidarray); echo "<br>";
        //var_dump ($marksarray); echo "<br>";

        // INSERT DATA
        try {
            $transaction = $DB->start_delegated_transaction();
            for ($j=0 ; $j<sizeof($sidarray); $j++){ // loop stud id times
                if(!$edit) {
                    $record = new stdClass();
                    $record->assignproid = $aspro_id;
                    $record->userid = $sidarray[$j];
                    $record->obtmark = $marksarray[$j];
                    $DB->insert_record('manual_assign_pro_attempt', $record);
                }
                else {
                    $sql_update="UPDATE mdl_manual_assign_pro_attempt SET obtmark=? WHERE assignproid=? AND userid=?";
                    $DB->execute($sql_update, array($marksarray[$j], $aspro_id, $sidarray[$j]));
                }
                //$sql="INSERT INTO manual_assign_pro_attempt (assignproid,userid,obtmark) VALUES ('$aspro_id','$sidarray[$j]','$marksarray[$j]')";
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
