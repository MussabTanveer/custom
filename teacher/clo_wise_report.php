<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("CLO Wise Report");
    $PAGE->set_heading("CLO Wise Report");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/clo_wise_report.php');
    
	require_login();
	if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
	}
    echo $OUTPUT->header();
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
        
        // Get all students of course
        $recStudents=$DB->get_records_sql("SELECT u.id AS sid, u.username AS seatnum, u.firstname, u.lastname
        FROM mdl_role_assignments ra, mdl_user u, mdl_course c, mdl_context cxt
        WHERE ra.userid = u.id
        AND ra.contextid = cxt.id
        AND cxt.contextlevel = ?
        AND cxt.instanceid = c.id
        AND c.id = ?
        AND (roleid=5)", array(50, $course_id));

        $stdids = array();
        $seatnos = array();
        foreach($recStudents as $records){
            $id = $records->sid;
            $seatno = $records->seatnum ;
            array_push($stdids,$id);
            array_push($seatnos,$seatno);
        }

        //Get course clo with its level, plo and peo
		$courseclos=$DB->get_records_sql(
        "SELECT clo.id AS cloid, clo.shortname AS cloname, plo.shortname AS ploname, peo.shortname AS peoname, levels.name AS lname, levels.level AS lvl
    
        FROM mdl_competency_coursecomp cc, mdl_competency clo, mdl_competency plo, mdl_competency peo, mdl_taxonomy_levels levels, mdl_taxonomy_clo_level clolevel

        WHERE cc.courseid = ? AND cc.competencyid=clo.id  AND peo.id=plo.parentid AND plo.id=clo.parentid AND 
        clo.id=clolevel.cloid AND levels.id=clolevel.levelid",
        
        array($course_id));
            
        $clonames = array(); $closid = array(); $plos = array(); $peos = array(); $levels = array(); $lvlno = array();
        foreach ($courseclos as $recC) {
            $cid = $recC->cloid;
            $clo = $recC->cloname;
            $plo = $recC->ploname;
            $peo = $recC->peoname;
            $lname = $recC->lname;
            $lvl = $recC->lvl;
            array_push($closid, $cid); // array of clo ids
            array_push($clonames, $clo); // array of clo names
            array_push($plos, $plo); // array of plos
            array_push($peos, $peo); // array of peos
            array_push($levels, $lname); // array of levels
            array_push($lvlno, $lvl); // array of level nos
        }
        $closidCountActivity = array();
        for($j=0; $j<count($closid); $j++)
            $closidCountActivity[$j]=0;

        // Get course quiz ids
        $courseQuizId=$DB->get_records_sql("SELECT * FROM `mdl_quiz` WHERE course = ? ", array($course_id));
        $quizids = array();
        foreach ($courseQuizId as $qid) {
            $id = $qid->id;
            $lvl = $recC->lvl;
            array_push($quizids, $id); // array of quiz ids
        }
        
        // Get course assignment ids
        $courseAssignId=$DB->get_records_sql("SELECT * FROM `mdl_assign` WHERE course = ? ", array($course_id));
        $assignids = array();
        foreach ($courseAssignId as $aid) {
            $id = $aid->id;
            array_push($assignids, $id); // array of assign ids
        }
        
        /**** QUIZZES ****/
        // Find students quiz records
        $seatnosQMulti = array();
        $closUniqueQMulti = array();
        $closQMulti = array();
        $resultQMulti = array();
        $cloQCount = array();
        $quiznames = array();
        
        for($i=0; $i < count($quizids); $i++){
            $recQuiz=$DB->get_recordset_sql(
            'SELECT
            q.name AS quiz_name,
            qa.userid,
            u.idnumber AS std_id,
            u.username AS seat_no,
            CONCAT(u.firstname, " ", u.lastname) AS std_name,
            qu.competencyid,
            SUM(qua.maxmark) AS maxmark,
            SUM(qua.maxmark*qas.fraction) AS marksobtained
            FROM
                mdl_quiz q,
                mdl_quiz_slots qs,
                mdl_question qu,
                mdl_question_categories qc,
                mdl_quiz_attempts qa,
                mdl_question_attempts qua,
                mdl_question_attempt_steps qas,
                mdl_user u
            WHERE
                q.id=? AND qa.attempt=? AND q.id=qs.quizid AND qu.id=qs.questionid AND qu.category=qc.id AND q.id=qa.quiz AND qa.userid=u.id
                AND qa.uniqueid=qua.questionusageid AND qu.id=qua.questionid AND qua.id=qas.questionattemptid AND qas.fraction IS NOT NULL
            GROUP BY qa.userid, qu.competencyid
            ORDER BY qa.userid, qu.competencyid',
            
            array($quizids[$i],1));

            $seatnosQ = array();
            $closQ = array();
            $resultQ = array();
            
            $quizname = "";
            foreach($recQuiz as $rq){
                $quizname = $rq->quiz_name;
                $un = $rq->seat_no;
                $clo=$rq->competencyid;
                $qmax = $rq->maxmark; $qmax = number_format($qmax, 2); // 2 decimal places
                $mobtained = $rq->marksobtained; $mobtained = number_format($mobtained, 2);
                if( (($mobtained/$qmax)*100) > 50){
                    array_push($resultQ,"P");
                }
                else{
                    array_push($resultQ,"F");
                }
                array_push($seatnosQ,$un);
                array_push($closQ,$clo);
            }
            array_push($quiznames,$quizname);
            $cloQuizUnique = array_unique($closQ);

            array_push($cloQCount,count($cloQuizUnique));
            array_push($seatnosQMulti,$seatnosQ);
            array_push($closUniqueQMulti,$cloQuizUnique);
            array_push($closQMulti,$closQ);
            array_push($resultQMulti,$resultQ);
        }

        /**** ASSIGNMENTS ****/
        // Find students assignment records
        $seatnosAMulti = array();
        $closUniqueAMulti = array();
        $closAMulti = array();
        $resultAMulti = array();
        $cloACount = array();
        $assignnames = array();
        
        for($i=0; $i < count($assignids); $i++){
            //Get assign comp
            $recAssignCLO=$DB->get_records_sql("SELECT DISTINCT c.id AS clo_id, c.shortname AS clo_name
            
            FROM mdl_competency c, mdl_assign a, mdl_course_modules cm, mdl_competency_modulecomp cmc
    
            WHERE a.id=? AND cm.course=? AND cm.module=? AND a.id=cm.instance AND cm.id=cmc.cmid AND cmc.competencyid=c.id
            
            ORDER BY cmc.competencyid",
            
            array($assignids[$i],$course_id,1));
            // Get assign records
            $recAssign=$DB->get_recordset_sql(
                'SELECT
                u.username AS seat_no,
                a.name AS assign_name,
                a.grade AS maxmark,
                ag.grade AS marksobtained
                FROM
                    mdl_assign a,
                    mdl_assign_grades ag,
                    mdl_user u
                WHERE
                    a.id=? AND ag.userid=u.id AND ag.grade != ? AND a.id=ag.assignment
                ORDER BY ag.userid',
                
            array($assignids[$i],-1));

            $seatnosA = array();
            $closA = array();
            $resultA = array();
            
            $assignname = "";
            foreach($recAssign as $as){
                $assignname = $as->assign_name;
                $un = $as->seat_no;
                $amax = $as->maxmark; $amax = number_format($amax, 2); // 2 decimal places
                $mobtained = $as->marksobtained; $mobtained = number_format($mobtained, 2);
                if( (($mobtained/$amax)*100) > 50){
                    array_push($resultA,"P");
                }
                else{
                    array_push($resultA,"F");
                }
                array_push($seatnosA,$un);
            }
            foreach($recAssignCLO as $asCLO){
                $clo = $asCLO->clo_id;
                array_push($closA,$clo);
            }

            array_push($assignnames,$assignname);
            $cloAssignUnique = array_unique($closA);

            array_push($seatnosAMulti,$seatnosA);
            array_push($closAMulti,$closA);
            array_push($resultAMulti,$resultA);

            array_push($cloACount,count($cloAssignUnique));
            array_push($closUniqueAMulti,$cloAssignUnique);

        }
        
        for($i=0; $i<count($quizids); $i++)
            for($j=0; $j<count($closid); $j++)
                if(in_array($closid[$j], $closUniqueQMulti[$i]))
                    $closidCountActivity[$j]++;
        
        
        
    }

    ?>

    <table class="generaltable" border="1">
        <tr>
            <th>Seat Number</th>
            <?php /****** CLOS ******/
            for($i=0; $i<count($closid); $i++) {
                if($closidCountActivity[$i]>0){
                ?>
                <th colspan="<?php echo $closidCountActivity[$i]; ?>"><?php echo $clonames[$i]; ?></th>
                <?php
                }
            }
            ?>
        </tr>
        <tr>
            <th></th>
            <?php /****** Activity Names ******/
            for($i=0; $i<count($closid); $i++)
                for($j=0; $j<count($quizids); $j++)
                    if(in_array($closid[$i], $closUniqueQMulti[$j])){
                    ?>
                    <th><?php echo $quiznames[$j]; ?></th>
                    <?php
                    }
            ?>
        </tr>
        <?php
        foreach ($seatnos as $seatno) {
        ?>
        <tr> 
            <td>  <?php echo "$seatno" ?> </td>
            <?php
            /****** QUIZZES RECORDS ******/
            for($i=0; $i<count($closid); $i++)
                for($j=0; $j<count($quizids); $j++)
                    if(in_array($closid[$i], $closUniqueQMulti[$j])){
                        $flag=0;
                        for($k=0; $k<count($seatnosQMulti[$j]); $k++){
                            if($seatno == $seatnosQMulti[$j][$k] && $closid[$i] == $closQMulti[$j][$k])
                            {
                                $flag=1;
                                if($resultQMulti[$j][$k] == 'P')
                                    echo "<td><i class='fa fa-square' aria-hidden='true' style='color: #05E177'></i></td>";
                                else
                                    echo "<td><i class='fa fa-square' aria-hidden='true' style='color: #FE3939'></i></td>";
                                
                            }
                        }
                        if($flag==0)
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
    echo $OUTPUT->footer();

?>
