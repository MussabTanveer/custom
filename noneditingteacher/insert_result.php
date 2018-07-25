<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Upload Result");
    $PAGE->set_heading("Upload Result");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/noneditingteacher/insert_result.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    if(isset($_POST['submit']))
	{
        // GET DATA
		$c_count = $_POST['ccount'];
        $a_id = $_POST['aid'];
        
        // Check marks already entered or not
        $edit = 0;
        $useridsupdate = array();
        $check=$DB->get_records_sql('SELECT * FROM mdl_assessment_attempt WHERE aid=?',array($a_id));
        if($check){
           $edit = 1;
           foreach($check as $c){
                $userid = $c->userid;
                array_push($useridsupdate, $userid);
            }
            $useridsupdate = array_unique($useridsupdate);
            $useridsupdate = array_values($useridsupdate);
        }

        $cidarray = array();
		foreach ($_POST['cid'] as $cid)
		{
			array_push($cidarray,$cid);
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
        // var_dump ($c_count); echo "<br>";
        // var_dump ($a_id); echo "<br>";
        // var_dump ($cidarray); echo "<br>";
        // var_dump ($sidarray); echo "<br>";
        // var_dump ($marksarray); echo "<br>";

        //FILTER DATA FOR NULL RECORDS
        $ccount=0; // to track crit count
        $flag = 0;
        $i = 0;
        $lens = count($sidarray);
        $lenm = count($marksarray);
        for ($j=0 ; $j<$lens; $j++){ // loop stud id times
            for (; $i<$lenm; $i++){ // loop marks obt time (for inserting marks of particular stud crit count times)
                $ccount++;
                if($marksarray[$i] != NULL){
                    //echo "hello";
                    //echo is_null($marksarray[$i]);
                    $flag = 1; // true if student record has marks
                }
                if($ccount == $c_count){
                    $i++;
                    $ccount=0; // set crit count to 0
                    break;
                }
            }
            if(!$flag){
                //echo "hello";
                unset($sidarray[$j]); // remove student seat number
                for($c=0; $c<$c_count; $c++){
                    unset($marksarray[$i-$c_count+$c]); // remove student marks
                }
            }
            $flag = 0;
        }
        //print_r ($sidarray); echo "<br>";
        //print_r ($marksarray); echo "<br>";
        $sidarray = array_values($sidarray);
        $marksarray = array_values($marksarray);
        //print_r ($sidarray); echo "<br>";
        //print_r ($marksarray); echo "<br>";
        
        // INSERT DATA
        $i = 0; // initialize
        $cidx = 0; // to track criteria index
        $ccount=0; // to track ques count
        try {
            $transaction = $DB->start_delegated_transaction();
            for ($j=0 ; $j<sizeof($sidarray); $j++){ // loop stud id times
                for (; $i<sizeof($marksarray) ; $i++){ // loop marks obt time (for inserting marks of particular stud ques count times)
                    $ccount++;
                    if(!$edit) {
                        $record = new stdClass();
                        $record->aid = $a_id;
                        $record->userid = $sidarray[$j];
                        $record->cid = $cidarray[$cidx];
                        $record->obtmark = $marksarray[$i];
                        $DB->insert_record('assessment_attempt', $record);
                    }
                    else {
                        if(in_array($sidarray[$j], $useridsupdate)){
                            $sql_update="UPDATE mdl_assessment_attempt SET obtmark=? WHERE aid=? AND userid=? AND cid=?";
                            $DB->execute($sql_update, array($marksarray[$i], $a_id, $sidarray[$j], $cidarray[$cidx]));
                        }
                        else{
                            $record = new stdClass();
                            $record->aid = $a_id;
                            $record->userid = $sidarray[$j];
                            $record->cid = $cidarray[$cidx];
                            $record->obtmark = $marksarray[$i];
                            $DB->insert_record('assessment_attempt', $record);
                        }
                    }
                    //$sql="INSERT INTO mdl_assessment_attempt (aid,userid,cid,obtmark) VALUES ('$aid','$stdids[$j]','$cids[$qidx]','$mrkobt[$i]')";
                    //$DB->execute($sql);
                    $cidx++; // next ques id index
                    if($ccount == $c_count){
                        $i++;
                        $cidx =0; // first crit id index
                        $ccount=0; // set crit count to 0
                        break;
                    }
                }
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
    <a href="../teacher/teacher_courses.php">Back</a>
    <?php
        echo $OUTPUT->footer();
    ?>
