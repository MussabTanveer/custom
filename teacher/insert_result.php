<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Upload Result");
    $PAGE->set_heading("Upload Result");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/insert_result.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    if(isset($_POST['submit']))
	{
        $edit = 0;
        $check=$DB->get_records_sql('SELECT *  FROM mdl_manual_quiz_attempt WHERE quizid = ?', array($quizId));
        $checkbit=0;
        if($check){
            echo "<font color=red>Sorry, cannot upload marks because they have already been uploaded!</font>";
            $edit = 1;
        }
        // GET DATA
		$ques_count = $_POST['quescount'];
		$quiz_id = $_POST['quizid'];
        $qidarray = array();
		foreach ($_POST['quesid'] as $qid)
		{
			array_push($qidarray,$qid);
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
        //var_dump ($ques_count); echo "<br>";
        //var_dump ($quiz_id); echo "<br>";
        //var_dump ($qidarray); echo "<br>";
        //var_dump ($sidarray); echo "<br>";
        //var_dump ($marksarray); echo "<br>";

        // INSERT DATA
        $i = 0; // initialize
        $qidx = 0; // to track ques index
        $qcount=0; // to track ques count
        try {
            $transaction = $DB->start_delegated_transaction();
            for ($j=0 ; $j<sizeof($sidarray); $j++){ // loop stud id times
                for (; $i<sizeof($marksarray) ; $i++){ // loop marks obt time (for inserting marks of particular stud ques count times)
                    $qcount++;
                    $record = new stdClass();
                    $record->quizid = $quiz_id;
                    $record->userid = $sidarray[$j];
                    $record->questionid = $qidarray[$qidx];
                    $record->obtmark = $marksarray[$i];
                    $DB->insert_record('manual_quiz_attempt', $record);
                    //$sql="INSERT INTO mdl_manual_quiz_attempt (quizid,userid,questionid,obtmark) VALUES ('$quiz_id','$sidarray[$j]','$qidarray[$qidx]','$marksarray[$i]')";
                    //$DB->execute($sql);
                    $qidx++; // next ques id index
                    if($qcount == $ques_count){
                        $i++;
                        $qidx =0; // first ques id index
                        $qcount=0; // set ques count to 0
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
    <a href="./teacher_courses.php">Back</a>
    <?php
        echo $OUTPUT->footer();
    ?>
