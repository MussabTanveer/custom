<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Course Report");
    $PAGE->set_heading("Course Report");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/course_report.php');
    echo $OUTPUT->header();
    require_login();
?>
<style>
td{
    text-align:center;
}
th{
    text-align:center;
}
</style>

<?php

    if(isset($_GET['course']))
    {
        $course_id=$_GET['course'];
        
        // Get Grading Items
        $rec=$DB->get_records_sql("SELECT * FROM mdl_grading_policy gp, mdl_grading_mapping mg WHERE gp.courseid = ? AND gp.id = mg.gradingitem ORDER BY mg.id", array($course_id));

        // Get all students of course
        $recStudents=$DB->get_records_sql("SELECT u.id AS sid, u.username AS seatnum, u.firstname, u.lastname
        FROM mdl_role_assignments ra, mdl_user u, mdl_course c, mdl_context cxt
        WHERE ra.userid = u.id
        AND ra.contextid = cxt.id
        AND cxt.contextlevel = ?
        AND cxt.instanceid = c.id
        AND c.id = ?
        AND (roleid=5)", array(50, $course_id));

        if($rec){
            $modules = array();
            $instances = array();
            $gnames = array();
            foreach($rec as $records){
                $module = $records->module;
                $instance = $records->instance ;
                $gname = $records->name;
                array_push($modules,$module);
                array_push($gnames,$gname);
                array_push($instances,$instance);

            }
            $stdids = array();
            $seatnos = array();
            foreach($recStudents as $records){
                $id = $records->sid;
                $seatno = $records->seatnum ;
                array_push($stdids,$id);
                array_push($seatnos,$seatno);

            }
            //var_dump($modules);
            //var_dump($gnames);
            //var_dump($instances);
            //var_dump($stdids);
            //var_dump($seatnos);
            
            ?>
            <table class="generaltable" border="1">
                <?php
                $flagmid = 0;
                $flagfinal = 0;
                /****** MID TERM ******/
                if(in_array("mid term", $gnames)){
                    $flagmid = 1;
                    $pos = array_search('mid term', $gnames);
                    $recMid=$DB->get_recordset_sql(
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
                            q.id=? AND q.id=qs.quizid AND qu.id=qs.questionid AND us.id=qa.userid   AND qu.category=qc.id AND q.id=qa.quiz AND c.id=qu.competencyid
                            AND qa.uniqueid=qua.questionusageid AND qu.id=qua.questionid AND qua.id=qas.questionattemptid AND qas.fraction IS NOT NULL  
                        ORDER BY qa.attempt, qa.userid, qu.id',
                        
                        array($instances[$pos]));
                        
                        $seatnosM = array();
                        $qnamesM = array();
                        $closM = array();
                        $resultM = array();
                        foreach($recMid as $fe){
                            $un = $fe->username;
                            $qname = $fe->name;
                            $clo=$fe->shortname;
                            $qmax = $fe->maxmark; $qmax = number_format($qmax, 2); // 2 decimal places
                            $mobtained = $fe->marksobtained; $mobtained = number_format($mobtained, 2);
                            if( (($mobtained/$qmax)*100) > 50){
                                array_push($resultM,"<font color='green'>P</font>");
                            }
                            else{
                                array_push($resultM,"<font color='red'>F</font>");
                            }
                            array_push($seatnosM,$un);
                            array_push($qnamesM,$qname);
                            array_push($closM,$clo);
                        }
                        $qnameMidUnique = array_unique($qnamesM);
                        $tot_quesMid = count($qnameMidUnique);
                }
                /****** FINAL EXAM ******/
                if(in_array("final exam", $gnames)){
                    $flagfinal = 1;
                    $pos = array_search('final exam', $gnames);
                    $recFinal=$DB->get_recordset_sql(
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
                            q.id=? AND q.id=qs.quizid AND qu.id=qs.questionid AND us.id=qa.userid   AND qu.category=qc.id AND q.id=qa.quiz AND c.id=qu.competencyid
                            AND qa.uniqueid=qua.questionusageid AND qu.id=qua.questionid AND qua.id=qas.questionattemptid AND qas.fraction IS NOT NULL  
                        ORDER BY qa.attempt, qa.userid, qu.id',
                        
                        array($instances[$pos]));
                        
                        $seatnosF = array();
                        $qnamesF = array();
                        $closF = array();
                        $resultF = array();
                        foreach($recFinal as $fe){
                            $un = $fe->username;
                            $qname = $fe->name;
                            $clo=$fe->shortname;
                            $qmax = $fe->maxmark; $qmax = number_format($qmax, 2); // 2 decimal places
                            $mobtained = $fe->marksobtained; $mobtained = number_format($mobtained, 2);
                            if( (($mobtained/$qmax)*100) > 50){
                                array_push($resultF,"<font color='green'>P</font>");
                            }
                            else{
                                array_push($resultF,"<font color='red'>F</font>");
                            }
                            array_push($seatnosF,$un);
                            array_push($qnamesF,$qname);
                            array_push($closF,$clo);
                        }
                        $qnameFinalUnique = array_unique($qnamesF);
                        $tot_quesFinal = count($qnameFinalUnique);
                }
                ?>
                <tr>
                    <th>Seat Number</th>
                    <?php /****** MID TERM ******/ ?>
                    <th colspan="<?php echo $tot_quesMid ?>">Mid Term</th>
                    <?php /****** FINAL EXAM ******/ ?>
                    <th colspan="<?php echo $tot_quesFinal ?>">Final Exam</th>
                </tr>
                <tr>
                    <th></th>
                    <?php
                    /****** MID TERM ******/
                    foreach($qnameMidUnique as $q){
                        echo "<th>$q</th>";
                    }
                    
                    /****** FINAL EXAM ******/
                    foreach($qnameFinalUnique as $q){
                        echo "<th>$q</th>";
                    }
                    ?>
                </tr>
                <tr>
                    <th></th>
                    <?php
                    /****** MID TERM ******/
                    for($i=0; $i < count($qnameMidUnique); $i++){
                        echo "<th>$closM[$i]</th>";
                    }
                    
                    /****** FINAL EXAM ******/
                    for($i=0; $i < count($qnameFinalUnique); $i++){
                        echo "<th>$closF[$i]</th>";
                    }
                    ?>
                </tr>

                <?php
                foreach ($seatnos as $seatno) {
                    ?>
                    <tr> 
                        <td>  <?php echo "$seatno" ?> </td>
                        <?php
                            /****** MID TERM ******/
                            $flag=0;
                            for($i=0 ; $i<count($seatnosM); $i++)
                            {
                                if($seatno == $seatnosM[$i])
                                {
                                    $flag=1;
                                     echo "<td>$resultM[$i]</td>";
                                }
                            }
                            if($flag==0)
                            {
                                foreach ($qnameMidUnique as $quesUnique)
                                {
                                    echo "<td>x</td>";
                                }
                            }
                            /****** FINAL EXAM ******/
                            $flag=0;
                            for($i=0 ; $i<count($seatnosF); $i++)
                            {
                                if($seatno == $seatnosF[$i])
                                {
                                    $flag=1;
                                     echo "<td>$resultF[$i]</td>";
                                }
                            }
                            if($flag==0)
                            {
                                foreach ($qnameFinalUnique as $quesUnique)
                                {
                                    echo "<td>x</td>";
                                }
                            }
                        ?>
                     </tr>
                <?php
                }

                ?>

            </table>
            <?php
        }
        else{
            echo "<h5 style='color:red'> <br />Found no mapped graded activity item of this course! </h5> <br /><a href=./grading_policy.php?course=$course_id>Add a grading policy item</a>.<br /><a href=./map_grading_item.php?course=$course_id>Map activities to grading policy item</a>.";
        }
    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./teacher_courses.php">Back</a>
    <?php
    }
    echo $OUTPUT->footer();
?>
