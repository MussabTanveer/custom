<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/table2excel/jquery.table2excel.min.js"></script>

<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Activity Results");
    $PAGE->set_heading("Activity Results");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/display_quiz_grid.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    if(!empty($_GET['course']) && !empty($_GET['quizid']))
    {
        $course_id=$_GET['course'];
        $coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
        $quiz_id=$_GET['quizid'];
        //echo "Quiz ID : $quiz_id";
        
        // Display Quiz Info
        //echo "<h3>Quiz Question Grid</h3>";
        //$rec1=$DB->get_recordset_sql('SELECT DISTINCT us.idnumber FROM mdl_user us, mdl_quiz_attempts qa 
        // WHERE us.id=qa.userid');
        $quiz_ques=$DB->get_records_sql('SELECT * from mdl_quiz_slots WHERE quizid=?', array($quiz_id));
        $tot_ques = count($quiz_ques);
        $rec=$DB->get_recordset_sql(
        'SELECT 
            qa.userid,
            us.idnumber,
            us.username,
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
            q.id=? AND qa.attempt=? AND q.id=qs.quizid AND qu.id=qs.questionid AND us.id=qa.userid   AND qu.category=qc.id AND q.id=qa.quiz AND c.id=qu.competencyid
            AND qa.uniqueid=qua.questionusageid AND qu.id=qua.questionid AND qua.id=qas.questionattemptid AND qas.fraction IS NOT NULL
        ORDER BY qa.attempt, qa.userid',
        
        array($quiz_id, 1));

        if($rec){
            ?>
            <table class="generaltable">
                <tr class="table-head">
                    <th> Seat No. </th>
                    <th> Question # </th>
                    <th> Question </th>
                    <th> CLO </th>
                    <th> Max Marks </th>
                    <th> Marks Obtained </th>
                </tr>
                <?php
                    $count = 0; $first = 0;
                    foreach ($rec as $records){
                        if($count === $tot_ques){ // 1 student record collected
                            //echo $count;
                            
                            foreach($data_temp as $data){ // loop as many times as comp count
                                ?>
                                <tr>
                                <?php
                                $uid = $data->idnumber;
                                $un = $data->username;
                                // $attempt = $data->attempt;
                                $qname = $data->name;
                                $qtext = $data->questiontext;
                                $competency=$data->shortname;
                                // $qrightans = $data->rightanswer;
                                // $qresponse = $data->responsesummary;
                                $qmax = $data->maxmark; $qmax = number_format($qmax, 2); // 2 decimal places
                                $mobtained = $data->marksobtained; $mobtained = number_format($mobtained, 2);
                                //$cname = $data->category;
                                
                                if($first === 0){ // display stud no & name only once
                                    ?>
                                    <td><?php echo $un;?></td>
                                    <td><?php echo $qname;?></td>
                                    <td><?php echo $qtext;?></td>
                                    <td><?php echo $competency;?></td>
                                    <td><?php echo $qmax;?></td>
                                    <td><?php echo $mobtained;?></td>
                                    <?php
                                    $first++;
                                }
                                else{
                                    ?>
                                    <td> </td>
                                    <td><?php echo $qname;?></td>
                                    <td><?php echo $qtext;?></td>
                                    <td><?php echo $competency;?></td>
                                    <td><?php echo $qmax;?></td>
                                    <td><?php echo $mobtained;?></td>
                                    <?php
                                }
                                ?>
                                </tr>
                                <?php
                            }
                            $count = 0;
                            $first = 0;
                            unset($data_temp);
                        }
                        //echo $count;
                        $data_temp[] = $records;
                        $count++;
                    }
                    if($data_temp){
                    foreach($data_temp as $data){ //  // now print very last student record
                        ?>
                        <tr>
                        <?php
                        $uid = $data->idnumber;
                        $un = $data->username;
                        // $attempt = $data->attempt;
                        $qname = $data->name;
                        $qtext = $data->questiontext;
                        $competency=$data->shortname;
                        // $qrightans = $data->rightanswer;
                        // $qresponse = $data->responsesummary;
                        $qmax = $data->maxmark; $qmax = number_format($qmax, 2); // 2 decimal places
                        $mobtained = $data->marksobtained; $mobtained = number_format($mobtained, 2);
                        //$cname = $data->category;
                        
                        if($first === 0){ // display stud no & name only once
                            ?>
                            <td><?php echo $un;?></td>
                            <td><?php echo $qname;?></td>
                            <td><?php echo $qtext;?></td>
                            <td><?php echo $competency;?></td>
                            <td><?php echo $qmax;?></td>
                            <td><?php echo $mobtained;?></td>
                            <?php
                            $first++;
                        }
                        else{
                            ?>
                            <td> </td>
                            <td><?php echo $qname;?></td>
                            <td><?php echo $qtext;?></td>
                            <td><?php echo $competency;?></td>
                            <td><?php echo $qmax;?></td>
                            <td><?php echo $mobtained;?></td>
                            <?php
                        }
                        ?>
                        </tr>
                        <?php
                    }
                    }
                    ?>
                </table>
                <button id="myButton" class="btn btn-primary">Export to Excel</button>

            <!-- Export html Table to xls -->
            <script type="text/javascript" >
                $(document).ready(function(e){
                    $("#myButton").click(function(e){ 
                        $(".generaltable").table2excel({
                            name: "file name",
                            filename: "quiz-report",
                            fileext: ".xls"
                        });
                    });
                });
            </script>
            <?php
        }
        else{
            echo "<h3>No students have attempted this quiz!</h3>";
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
        <a href="./teacher_courses.php">Back</a>
    <?php 
        echo $OUTPUT->footer();
    }?>
