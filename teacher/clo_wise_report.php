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

        // Get course quiz ids
        $courseQuizId=$DB->get_records_sql("SELECT * FROM `mdl_quiz` WHERE course = ? ", array($course_id));
        $quizids = array();
        foreach ($courseQuizId as $qid) {
            $id = $qid->id;
            $lvl = $recC->lvl;
            array_push($quizids, $cid); // array of quiz ids
        }

        for($i=0; $i < count($quizids); $i++){
            $recQuiz=$DB->get_recordset_sql(
            'SELECT
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

            foreach ($recQuiz as $recQ) {
                
            }
        }

    }

    ?>

    <table class="generaltable" border="1">
        <tr>
            <th>Seat Number</th>
            <?php /****** CLOS ******/
            foreach ($courseclos as $recC) {
                $cid =  $recC->cloid;
                $cname = $recC->cloname;
                $plname = $recC->ploname;
                $pename = $recC->peoname;
                ?>
                <th><?php echo $cname; ?></th>
                <?php
            }
            ?>

        </tr>
    </table>


<?php
    echo $OUTPUT->footer();

?>
