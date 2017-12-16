<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Quiz Results");
    $PAGE->set_heading("Results");
    $PAGE->set_url($CFG->wwwroot.'/custom/display_quiz_grid.php');
    
    echo $OUTPUT->header();
    require_login();

    if(isset($_POST['submit']) && isset( $_POST['quizid']))
    {
        $quiz_id=$_POST['quizid'];
        //echo "Quiz ID : $quiz_id";
        ?>

        <?php
        // Display Quiz Info
        echo "<h3>Question category wise grid</h3>";
       //$rec1=$DB->get_recordset_sql('SELECT DISTINCT us.idnumber FROM mdl_user us, mdl_quiz_attempts qa 
       // WHERE us.id=qa.userid');
          
        $rec=$DB->get_recordset_sql(
         


'SELECT 
          qa.userid,
         us.idnumber,
            qa.attempt,
            qu.name,
            c.shortname,
            qu.questiontext,
            qua.rightanswer,
            qua.responsesummary,
            qua.maxmark,
            qua.maxmark*qas.fraction AS marksobtained,
            qc.name AS category
        FROM
         
            mdl_quiz q,
            mdl_quiz_slots qs,
            mdl_user us,
            mdl_question qu,
            mdl_question_categories qc,
            mdl_quiz_attempts qa,
            mdl_question_attempts qua,
            mdl_competency c,
            mdl_question_attempt_steps qas
        WHERE 
            q.id=? AND q.id=qs.quizid AND qu.id=qs.questionid AND us.id=qa.userid   AND qu.category=qc.id AND q.id=qa.quiz AND c.id=qu.competencyid
            AND qa.uniqueid=qua.questionusageid AND qu.id=qua.questionid AND qua.id=qas.questionattemptid AND qas.fraction IS NOT NULL  
        ORDER BY qa.attempt, qa.userid',
        
        array($quiz_id));



        if($rec){
            $table = new html_table();
            $table->head = array('Student ID', 'No. of Attempts', 'Question Name','CLO', 'Question',  'Max Marks', 'Marks Obtained');
      

             
            foreach ($rec as $records  ) {
                $uid = $records->idnumber;
                $attempt = $records->attempt;
                $qname = $records->name;
                $competency=$records->shortname;
                $qtext = $records->questiontext;
                 
             

               // $qrightans = $records->rightanswer;
               // $qresponse = $records->responsesummary;
                $qmax = $records->maxmark; $qmax = number_format($qmax, 2); // 2 dp
                $mobtained = $records->marksobtained; $mobtained = number_format($mobtained, 2);
                //$cname = $records->category;
                $table->data[] = array($uid, $attempt, $qname,$competency ,$qtext,  $qmax, $mobtained);
            }
            $rec->close(); // Don't forget to close the recordset!
            echo html_writer::table($table);
        }


     





        else{
            echo "<h3>No students have attempted the quiz!</h3>";
        }

        /*
        SELECT 
            mdl_quiz.id,
            mdl_question.id,
            mdl_question.questiontext,
            mdl_question_categories.name
        FROM
            mdl_quiz
            INNER JOIN mdl_quiz_slots ON mdl_quiz.id = mdl_quiz_slots.quizid
            INNER JOIN mdl_question ON mdl_quiz_slots.questionid = mdl_question.id
            INNER JOIN mdl_question_categories ON mdl_question.category = mdl_question_categories.id
        WHERE 
            mdl_quiz.id = 1
                
            
        SELECT 
            mdl_quiz.id,
            mdl_quiz_attempts.userid
        FROM
            mdl_quiz
            INNER JOIN mdl_quiz_attempts ON mdl_quiz.id = mdl_quiz_attempts.quiz
        WHERE 
            mdl_quiz.id = 1


        SELECT
            quiza.userid,
            qa.questionid,
            ques.name,
            qa.maxmark,
            qa.questionsummary,
            qa.rightanswer,
            qa.responsesummary,
            qa.maxmark*qas.fraction AS marksobtained,
            qc.name AS category

        FROM mdl_quiz_attempts quiza
        JOIN mdl_question_usages qu ON qu.id = quiza.uniqueid
        JOIN mdl_question_attempts qa ON qa.questionusageid = qu.id
        JOIN mdl_question_attempt_steps qas ON qas.questionattemptid = qa.id
        LEFT JOIN mdl_question_attempt_step_data qasd ON qasd.attemptstepid = qas.id,
        mdl_quiz q
        JOIN mdl_quiz_slots qs ON q.id = qs.quizid
        JOIN mdl_question ques ON qs.questionid = ques.id
        JOIN mdl_question_categories qc ON ques.category = qc.id
        
        WHERE quiza.quiz = 1 AND qas.fraction IS NOT NULL AND q.id = 1
        GROUP BY quiza.userid, qa.questionid
        ORDER BY quiza.userid, qa.questionid

        SELECT
            qa.userid,
            qa.attempt,
            qu.name,
            qu.questiontext,
            qua.rightanswer,
            qua.responsesummary as StudentAns,
            qua.maxmark,
            qc.name as category

        FROM
            mdl_quiz q,
            mdl_quiz_slots qs,
            mdl_question qu,
            mdl_question_categories qc,
            mdl_quiz_attempts qa,
            mdl_question_attempts qua

        WHERE
            q.id=qs.quizid AND qu.id=qs.questionid AND qu.category=qc.id AND q.id=qa.quiz
            AND qa.uniqueid=qua.questionusageid AND qu.id=qua.questionid

        ORDER BY qa.attempt, qa.userid
        */

        ?>

        <?php
        echo $OUTPUT->footer();
    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./display_courses.php">Back</a>
    <?php 
        echo $OUTPUT->footer();
    }?>
