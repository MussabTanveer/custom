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
        $quiz_id = $_POST['quizid'];

        // Check marks already entered or not
        $edit = 0;
        $useridsupdate = array();
        $check=$DB->get_records_sql('SELECT *  FROM mdl_manual_quiz_attempt WHERE quizid = ?', array($quiz_id));
        if($check){
            $edit = 1;
            foreach($check as $c){
                $userid = $c->userid;
                array_push($useridsupdate, $userid);
            }
            $useridsupdate = array_unique($useridsupdate);
            $useridsupdate = array_values($useridsupdate);
        }
        
        // GET DATA
        $ques_count = $_POST['quescount'];
        
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
            if($mobt == "") {
                $mobt = NULL;
            }
            array_push($marksarray,$mobt);	
        }
        //var_dump ($ques_count); echo "<br>";
        //var_dump ($quiz_id); echo "<br>";
        //var_dump ($qidarray); echo "<br>";
        //print_r ($sidarray); echo "<br>";
        //print_r ($marksarray); echo "<br>";

        //FILTER DATA FOR NULL RECORDS
        $qcount=0; // to track ques count
        $flag = 0;
        $i = 0;
        $lens = count($sidarray);
        $lenm = count($marksarray);
        for ($j=0 ; $j<$lens; $j++){ // loop stud id times
            for (; $i<$lenm; $i++){ // loop marks obt time (for inserting marks of particular stud ques count times)
                $qcount++;
                if($marksarray[$i] != NULL){
                    //echo "hello";
                    //echo is_null($marksarray[$i]);
                    $flag = 1; // true if student record has marks
                }
                if($qcount == $ques_count){
                    $i++;
                    $qcount=0; // set ques count to 0
                    break;
                }
            }
            if(!$flag){
                //echo "hello";
                unset($sidarray[$j]); // remove student seat number
                for($q=0; $q<$ques_count; $q++){
                    unset($marksarray[$i-$ques_count+$q]); // remove student marks
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
        $qidx = 0; // to track ques index
        $qcount=0; // to track ques count
        try {
            $transaction = $DB->start_delegated_transaction();
            for ($j=0 ; $j<sizeof($sidarray); $j++){ // loop stud id times
                for (; $i<sizeof($marksarray) ; $i++){ // loop marks obt time (for inserting marks of particular stud ques count times)
                    $qcount++;
                    if(!$edit) {
                        $record = new stdClass();
                        $record->quizid = $quiz_id;
                        $record->userid = $sidarray[$j];
                        $record->questionid = $qidarray[$qidx];
                        $record->obtmark = $marksarray[$i];
                        $DB->insert_record('manual_quiz_attempt', $record);
                    }
                    else {
                        if(in_array($sidarray[$j], $useridsupdate)){
                            $sql_update="UPDATE mdl_manual_quiz_attempt SET obtmark=? WHERE quizid=? AND userid=? AND questionid=?";
                            $DB->execute($sql_update, array($marksarray[$i], $quiz_id, $sidarray[$j], $qidarray[$qidx]));
                        }
                        else{
                            $record = new stdClass();
                            $record->quizid = $quiz_id;
                            $record->userid = $sidarray[$j];
                            $record->questionid = $qidarray[$qidx];
                            $record->obtmark = $marksarray[$i];
                            $DB->insert_record('manual_quiz_attempt', $record);
                        }
                    }
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
