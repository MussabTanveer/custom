<?php
        // Report Header (Uni. name, Dept. name, course code and title)
        $un=$DB->get_records_sql('SELECT * FROM  `mdl_vision_mission` WHERE idnumber = ?', array("un"));
        if($un){
            foreach($un as $u){
                $uniName = $u->description;
            }
            $uniName = strip_tags($uniName); 
            echo "<h3 style='text-align:center'>".strtoupper($uniName)."</h3>";         
        }
        $dn=$DB->get_records_sql('SELECT * FROM  `mdl_vision_mission` WHERE idnumber = ?', array("dn"));
        if($dn){
            foreach($dn as $d){
                $deptName = $d->description;
            }
            $deptName = strip_tags($deptName); 
            echo "<h3 style='text-align:center'>".strtoupper($deptName)."</h3>";         
        }
        $course = $DB->get_record('course',array('id' => $course_id));
        echo "<h4 style='text-align:center'>Course Code: <u>".($course->idnumber)."</u>,";
        echo " Course Title: <u>".($course->fullname)." (".($course->shortname).")</u></h4>";
        echo "<h4 style='text-align:center'>Course CLO-Wise Assessment Sheet</h4>";
        echo "<div style='float: right;'><h5>Key:</h5>";
        echo "<i class='fa fa-square' aria-hidden='true' style='color: #FE3939'> Fail </i><br>";
        echo "<i class='fa fa-square' aria-hidden='true' style='color: #05E177'> Pass </i><br>";
        echo "<i class='fa fa-times' aria-hidden='true'> Not attempted </i><br><br></div>";
        
        // Get all students of course
        $recStudents=$DB->get_records_sql("SELECT u.id AS sid, u.username AS seatnum, substring(u.username,4,8) AS seatorder, u.firstname, u.lastname
        FROM mdl_role_assignments ra, mdl_user u, mdl_course c, mdl_context cxt
        WHERE ra.userid = u.id
        AND ra.contextid = cxt.id
        AND cxt.contextlevel = ?
        AND cxt.instanceid = c.id
        AND c.id = ?
        AND (roleid=5) ORDER BY seatorder", array(50, $course_id));
        $stdids = array();
        $seatnos = array();
        foreach($recStudents as $records){
            $id = $records->sid;
            $seatno = $records->seatnum ;
            array_push($stdids,$id);
            array_push($seatnos,$seatno);
        }

        //Get course clo with its plo, level, passing percentage
		$courseclos=$DB->get_records_sql(
        "SELECT
        clo.id AS cloid,
        clo.shortname AS cloname,
        plo.idnumber AS ploidn,
        clokpi.kpi AS passpercent,
        clocohortkpi.kpi AS cohortpasspercent,
        taxlvl.level
        FROM
        mdl_competency_coursecomp cc,
        mdl_competency clo,
        mdl_competency plo,
        mdl_clo_kpi clokpi,
        mdl_clo_cohort_kpi clocohortkpi,
        mdl_taxonomy_clo_level taxclolvl,
        mdl_taxonomy_levels taxlvl
        WHERE
        cc.courseid = ? AND cc.competencyid=clo.id AND clo.id=clokpi.cloid AND clo.id=clocohortkpi.cloid AND plo.id=clo.parentid AND clo.id=taxclolvl.cloid AND taxclolvl.levelid=taxlvl.id",
        array($course_id));
            
        $clonames = array(); $plonames = array(); $lnames = array(); $closid = array(); $clospasspercent = array(); $clocohortpasspercent = array();
        foreach ($courseclos as $recC) {
            $cid = $recC->cloid;
            $clo = $recC->cloname;
            $plo = $recC->ploidn;
            $level = $recC->level;
            $pp = $recC->passpercent;
            $cp = $recC->cohortpasspercent;
            array_push($closid, $cid); // array of clo ids
            array_push($clonames, $clo); // array of clo names
            array_push($plonames, $plo); // array of plo idnum
            array_push($lnames, $level); // array of levels
            array_push($clospasspercent, $pp); // array of clo individual stud pass percent
            array_push($clocohortpasspercent, $cp); // array of clo cohort course pass percent
        }
        $closidCountActivity = array();
        for($j=0; $j<count($closid); $j++)
            $closidCountActivity[$j]=0;
        
        // Get Parent Activities
        $parentActivity=$DB->get_records_sql("SELECT * FROM `mdl_parent_activity` WHERE courseid = ? ", array($course_id));
        $parentids = array(); $parentnames = array();
        foreach ($parentActivity as $paid) {
            $id = $paid->id;
            $name = $paid->name;
            array_push($parentids, $id); // array of parent activity ids
            array_push($parentnames, $name); // array of parent activity names
        }

        // Get Child Activities
        $childidsMulti = array(); $childmodulesMulti = array();
        for($i = 0; $i < count($parentids); $i++){
            $childActivity=$DB->get_records_sql("SELECT * FROM `mdl_parent_mapping` WHERE parentid = ? ", array($parentids[$i]));
            $childids = array(); $childmodules = array();
            foreach ($childActivity as $caid) {
                $id = $caid->childid;
                $module = $caid->module;
                array_push($childids, $id); // array of child activity ids
                array_push($childmodules, $module); // array of child modules
            }
            array_push($childidsMulti, $childids); // array of all child activity ids
            array_push($childmodulesMulti, $childmodules); // array of all child modules
        }
        /*var_dump($parentids); echo "<br>";
        var_dump($childidsMulti); echo "<br>";
        var_dump($childmodulesMulti); echo "<br>";*/

        /*
        // Get course online quiz ids
        $courseQuizId=$DB->get_records_sql("SELECT * FROM `mdl_quiz` WHERE course = ? ", array($course_id));
        $quizids = array();
        foreach ($courseQuizId as $qid) {
            $id = $qid->id;
            array_push($quizids, $id); // array of quiz ids
        }
        
        // Get course online assignment ids
        $courseAssignId=$DB->get_records_sql("SELECT * FROM `mdl_assign` WHERE course = ? ", array($course_id));
        $assignids = array();
        foreach ($courseAssignId as $aid) {
            $id = $aid->id;
            array_push($assignids, $id); // array of assign ids
        }
        

        // Get attempted course manual quiz/midterm/final ids
        $courseMQuizId=$DB->get_records_sql("SELECT * FROM `mdl_manual_quiz` WHERE courseid = ? AND id IN (SELECT quizid FROM `mdl_manual_quiz_attempt`)", array($course_id));
        $mquizids = array();
        foreach ($courseMQuizId as $qid) {
            $id = $qid->id;
            array_push($mquizids, $id); // array of quiz/mt/final ids
        }
        //print_r($mquizids);
        
        // Get attempted course manual assignment/project ids
        $courseMAssignId=$DB->get_records_sql("SELECT * FROM `mdl_manual_assign_pro` WHERE courseid = ? AND id IN (SELECT assignproid FROM `mdl_manual_assign_pro_attempt`)", array($course_id));
        $massignids = array();
        foreach ($courseMAssignId as $qid) {
            $id = $qid->id;
            array_push($massignids, $id); // array of assign/pro ids
        }
        //print_r($massignids);*/

        $quizids = 0;
        $assignids = 0;
        
        /**** ONLINE+MANUAL QUIZZES & ASSIGNMENTS ****/
        // Find students quiz records
        $seatnosQMulti = array();
        $closUniqueQMulti = array();
        $closQMulti = array();
        $resultQMulti = array();
        $cloQCount = array();
        $quiznames = array();

        // Find students assignment records
        $seatnosAMulti = array();
        $closUniqueAMulti = array();
        $closAMulti = array();
        $resultAMulti = array();
        $cloACount = array();
        $assignnames = array();
        
        // ONLINE CHILD ACTIVITIES MERGE
        $mod=0;
        for($p=0; $p < count($parentids); $p++){
            $seatnosQ = array();
            $closQ = array();
            $resultQ = array();
            $seatnosA = array();
            $closA = array();
            $resultA = array();
            
            $activityname = $parentnames[$p];
            for($i=0; $i < count($childidsMulti[$p]); $i++){
                if($childmodulesMulti[$p][$i] == 16){ // ONLINE QUIZ
                    $mod = 16;
                    //$quizids++;
                    $recQuiz=$DB->get_recordset_sql(
                    'SELECT
                    q.name AS quiz_name,
                    qa.userid,
                    u.idnumber AS std_id,
                    u.username AS seat_no,
                    CONCAT(u.firstname, " ", u.lastname) AS std_name,
                    qu.competencyid,
                    SUM(qua.maxmark) AS maxmark,
                    SUM(qua.maxmark*COALESCE(qas.fraction, 0)) AS marksobtained
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
                        AND qa.uniqueid=qua.questionusageid AND qu.id=qua.questionid AND qua.id=qas.questionattemptid AND qas.state IN ("gradedright", "gradedwrong", "gaveup", "gradedpartial")
                    GROUP BY qa.userid, qu.competencyid
                    ORDER BY qa.userid, qu.competencyid',
                    
                    array($childidsMulti[$p][$i],1));

                    //$seatnosQ = array();
                    //$closQ = array();
                    //$resultQ = array();
                    
                    //$quizname = "";
                    foreach($recQuiz as $rq){
                        $quizname = $rq->quiz_name;
                        $un = $rq->seat_no;
                        $clo=$rq->competencyid;
                        $qmax = $rq->maxmark; $qmax = number_format($qmax, 2); // 2 decimal places
                        $mobtained = $rq->marksobtained; $mobtained = number_format($mobtained, 2);
                        /*if( (($mobtained/$qmax)*100) > 50){
                            array_push($resultQ,"P");
                        }
                        else{
                            array_push($resultQ,"F");
                        }*/
                        array_push($resultQ,(($mobtained/$qmax)*100));
                        array_push($seatnosQ,$un);
                        array_push($closQ,$clo);
                    }
                }
                elseif($childmodulesMulti[$p][$i] == -1 || $childmodulesMulti[$p][$i] == -2 || $childmodulesMulti[$p][$i] == -3){ // MANUAL QUIZ/MIDTERM/FINAL
                    $mod = 16;
                    //$quizids++;
                    $recMQuiz=$DB->get_recordset_sql(
                        'SELECT
                        q.name AS quiz_name,
                        qa.userid,
                        u.username AS seat_no,
                        CONCAT(u.firstname, " ", u.lastname) AS std_name,
                        qu.cloid,
                        SUM(qu.maxmark) AS maxmark,
                        SUM(qa.obtmark) AS marksobtained
                        FROM
                            mdl_manual_quiz q,
                            mdl_manual_quiz_question qu,
                            mdl_manual_quiz_attempt qa,
                            mdl_user u
                        WHERE
                            q.id=? AND q.id=qu.mquizid AND q.id=qa.quizid AND qa.userid=u.id AND qu.id=qa.questionid
                        GROUP BY qa.userid, qu.cloid
                        ORDER BY qa.userid, qu.cloid',
                        
                        array($childidsMulti[$p][$i]));
                    
                    //$seatnosQ = array();
                    //$closQ = array();
                    //$resultQ = array();
                    
                    //$quizname = "";
                    foreach($recMQuiz as $rq){
                        $quizname = $rq->quiz_name;
                        $un = $rq->seat_no;
                        $clo=$rq->cloid;
                        $qmax = $rq->maxmark; $qmax = number_format($qmax, 2); // 2 decimal places
                        $mobtained = $rq->marksobtained; $mobtained = number_format($mobtained, 2);
                        /*if( (($mobtained/$qmax)*100) > 50){
                            array_push($resultQ,"P");
                        }
                        else{
                            array_push($resultQ,"F");
                        }*/
                        array_push($resultQ,(($mobtained/$qmax)*100));
                        array_push($seatnosQ,$un);
                        array_push($closQ,$clo);
                    }
                }
                elseif($childmodulesMulti[$p][$i] == 1){ // ONLINE ASSIGNMENT
                    // Get assign records
                    $mod = 1;
                    //$assignids++;
                    $recAssign=$DB->get_recordset_sql(
                        'SELECT
                        u.username AS seat_no,
                        a.name AS assign_name,
                        a.grade AS maxmark,
                        ag.grade AS marksobtained,
                        cmc.competencyid AS clo_id
                        FROM
                            mdl_assign a,
                            mdl_assign_grades ag,
                            mdl_user u,
                            mdl_course_modules cm,
                            mdl_competency_modulecomp cmc
                        WHERE
                            a.id=? AND ag.userid=u.id AND ag.grade != ? AND a.id=ag.assignment AND cm.course=? AND cm.module=? AND a.id=cm.instance AND cm.id=cmc.cmid
                        ORDER BY ag.userid',
                        
                    array($childidsMulti[$p][$i],-1,$course_id,1));
                    //$seatnosA = array();
                    //$closA = array();
                    //$resultA = array();
                    
                    //$assignname = "";
                    foreach($recAssign as $as){
                        $assignname = $as->assign_name;
                        $un = $as->seat_no;
                        $clo = $as->clo_id;
                        $amax = $as->maxmark; $amax = number_format($amax, 2); // 2 decimal places
                        $mobtained = $as->marksobtained; $mobtained = number_format($mobtained, 2);
                        /*if( (($mobtained/$amax)*100) > 50){
                            array_push($resultA,"P");
                        }
                        else{
                            array_push($resultA,"F");
                        }*/
                        array_push($resultA,(($mobtained/$amax)*100));
                        array_push($seatnosA,$un);
                        array_push($closA,$clo);
                    }
                }
                elseif($childmodulesMulti[$p][$i] == -4 || $childmodulesMulti[$p][$i] == -5){ // MANUAL ASSIGNMENT/PROJECT
                    // Get assign/pro records
                    $mod = 1;
                    //$assignids++;
                    $recMAssign=$DB->get_recordset_sql(
                        'SELECT
                        u.username AS seat_no,
                        a.name AS assign_name,
                        a.maxmark AS maxmark,
                        att.obtmark AS marksobtained,
                        a.cloid AS clo_id
                        FROM
                            mdl_manual_assign_pro a,
                            mdl_user u,
                            mdl_manual_assign_pro_attempt att
                        WHERE
                            a.id=? AND att.userid=u.id AND a.id=att.assignproid
                        ORDER BY att.userid',
                        
                    array($childidsMulti[$p][$i]));

                    //$seatnosA = array();
                    //$closA = array();
                    //$resultA = array();
                    
                    //$assignname = "";
                    foreach($recMAssign as $as){
                        $assignname = $as->assign_name;
                        $un = $as->seat_no;
                        $clo = $as->clo_id;
                        $amax = $as->maxmark; $amax = number_format($amax, 2); // 2 decimal places
                        $mobtained = $as->marksobtained; $mobtained = number_format($mobtained, 2);
                        /*if( (($mobtained/$amax)*100) > 50){
                            array_push($resultA,"P");
                        }
                        else{
                            array_push($resultA,"F");
                        }*/
                        array_push($resultA,(($mobtained/$amax)*100));
                        array_push($seatnosA,$un);
                        array_push($closA,$clo);
                    }
                }
                elseif($childmodulesMulti[$p][$i] == -6){ // MANUAL OTHER
                    // Get other records
                    $mod = 1;
                    //$assignids++;
                    $recMOther=$DB->get_recordset_sql(
                        'SELECT
                        u.username AS seat_no,
                        o.name AS other_name,
                        o.maxmark AS maxmark,
                        att.obtmark AS marksobtained,
                        o.cloid AS clo_id
                        FROM
                            mdl_manual_other o,
                            mdl_user u,
                            mdl_manual_other_attempt att
                        WHERE
                            o.id=? AND att.userid=u.id AND o.id=att.otherid
                        ORDER BY att.userid',
                        
                    array($childidsMulti[$p][$i]));

                    //$seatnosA = array();
                    //$closA = array();
                    //$resultA = array();
                    
                    //$assignname = "";
                    foreach($recMOther as $as){
                        $assignname = $as->other_name;
                        $un = $as->seat_no;
                        $clo = $as->clo_id;
                        $amax = $as->maxmark; $amax = number_format($amax, 2); // 2 decimal places
                        $mobtained = $as->marksobtained; $mobtained = number_format($mobtained, 2);
                        /*if( (($mobtained/$amax)*100) > 50){
                            array_push($resultA,"P");
                        }
                        else{
                            array_push($resultA,"F");
                        }*/
                        array_push($resultA,(($mobtained/$amax)*100));
                        array_push($seatnosA,$un);
                        array_push($closA,$clo);
                    }
                }
            }
            if($mod == 16){
                $quizids++;
                $cloQuizUnique = array_unique($closQ);
                array_push($cloQCount,count($cloQuizUnique));
                array_push($seatnosQMulti,$seatnosQ);
                array_push($closUniqueQMulti,$cloQuizUnique);
                array_push($closQMulti,$closQ);
                array_push($resultQMulti,$resultQ);
                array_push($quiznames,$activityname);
                $mod=0;
            }
            elseif($mod == 1){
                $assignids++;
                $cloAssignUnique = array_unique($closA);
                array_push($seatnosAMulti,$seatnosA);
                array_push($closAMulti,$closA);
                array_push($resultAMulti,$resultA);
                array_push($cloACount,count($cloAssignUnique));
                array_push($closUniqueAMulti,$cloAssignUnique);
                array_push($assignnames,$activityname);
                $mod=0;
            }
        }
        /*var_dump($quiznames); echo "<br>";
        var_dump($cloQCount); echo "<br>";
        var_dump($seatnosQMulti); echo "<br>";
        var_dump($closUniqueQMulti); echo "<br>";
        var_dump($closQMulti); echo "<br>";
        var_dump($resultQMulti); echo "<br>";*/

        /* MANUAL QUIZ/MIDTERM/FINAL
        for($i=0; $i < count($mquizids); $i++){
            $recMQuiz=$DB->get_recordset_sql(
            'SELECT
            q.name AS quiz_name,
            qa.userid,
            u.username AS seat_no,
            CONCAT(u.firstname, " ", u.lastname) AS std_name,
            qu.cloid,
            SUM(qu.maxmark) AS maxmark,
            SUM(qa.obtmark) AS marksobtained
            FROM
                mdl_manual_quiz q,
                mdl_manual_quiz_question qu,
                mdl_manual_quiz_attempt qa,
                mdl_user u
            WHERE
                q.id=? AND q.id=qu.mquizid AND q.id=qa.quizid AND qa.userid=u.id AND qu.id=qa.questionid
            GROUP BY qa.userid, qu.cloid
            ORDER BY qa.userid, qu.cloid',
            
            array($mquizids[$i]));
            $seatnosQ = array();
            $closQ = array();
            $resultQ = array();
            
            $quizname = "";
            foreach($recMQuiz as $rq){
                $quizname = $rq->quiz_name;
                $un = $rq->seat_no;
                $clo=$rq->cloid;
                $qmax = $rq->maxmark; $qmax = number_format($qmax, 2); // 2 decimal places
                $mobtained = $rq->marksobtained; $mobtained = number_format($mobtained, 2);
                //if( (($mobtained/$qmax)*100) > 50){
                //    array_push($resultQ,"P");
                //}
                //else{
                //    array_push($resultQ,"F");
                //}
                array_push($resultQ,(($mobtained/$qmax)*100));
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
        */
        /*
        echo "<br><br>"; print_r($quiznames); echo "<br><br>"; print_r($cloQCount);
        echo "<br><br>"; print_r($seatnosQMulti); echo "<br><br>"; print_r($closUniqueQMulti);
        echo "<br><br>"; print_r($closQMulti); echo "<br><br>"; print_r($resultQMulti);*/
        
        /**** ONLINE+MANUAL ASSIGNMENTS ****/
        // Find students assignment records
        /*
        $seatnosAMulti = array();
        $closUniqueAMulti = array();
        $closAMulti = array();
        $resultAMulti = array();
        $cloACount = array();
        $assignnames = array();
        
        
        // ONLINE ASSIGNMENTS
        for($i=0; $i < count($assignids); $i++){
            // Get assign records
            $recAssign=$DB->get_recordset_sql(
                'SELECT
                u.username AS seat_no,
                a.name AS assign_name,
                a.grade AS maxmark,
                ag.grade AS marksobtained,
                cmc.competencyid AS clo_id
                FROM
                    mdl_assign a,
                    mdl_assign_grades ag,
                    mdl_user u,
                    mdl_course_modules cm,
                    mdl_competency_modulecomp cmc
                WHERE
                    a.id=? AND ag.userid=u.id AND ag.grade != ? AND a.id=ag.assignment AND cm.course=? AND cm.module=? AND a.id=cm.instance AND cm.id=cmc.cmid
                ORDER BY ag.userid',
                
            array($assignids[$i],-1,$course_id,1));
            $seatnosA = array();
            $closA = array();
            $resultA = array();
            
            $assignname = "";
            foreach($recAssign as $as){
                $assignname = $as->assign_name;
                $un = $as->seat_no;
                $clo = $as->clo_id;
                $amax = $as->maxmark; $amax = number_format($amax, 2); // 2 decimal places
                $mobtained = $as->marksobtained; $mobtained = number_format($mobtained, 2);
                array_push($resultA,(($mobtained/$amax)*100));
                array_push($seatnosA,$un);
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
        
        // MANUAL ASSIGNMENTS/PROJECTS
        for($i=0; $i < count($massignids); $i++){
            // Get assign records
            $recMAssign=$DB->get_recordset_sql(
                'SELECT
                u.username AS seat_no,
                a.name AS assign_name,
                a.maxmark AS maxmark,
                att.obtmark AS marksobtained,
                a.cloid AS clo_id
                FROM
                    mdl_manual_assign_pro a,
                    mdl_user u,
                    mdl_manual_assign_pro_attempt att
                WHERE
                    a.id=? AND att.userid=u.id AND a.id=att.assignproid
                ORDER BY att.userid',
                
            array($massignids[$i]));
            $seatnosA = array();
            $closA = array();
            $resultA = array();
            
            $assignname = "";
            foreach($recMAssign as $as){
                $assignname = $as->assign_name;
                $un = $as->seat_no;
                $clo = $as->clo_id;
                $amax = $as->maxmark; $amax = number_format($amax, 2); // 2 decimal places
                $mobtained = $as->marksobtained; $mobtained = number_format($mobtained, 2);
                //if( (($mobtained/$amax)*100) > 50){
                //    array_push($resultA,"P");
                //}
                //else{
                //    array_push($resultA,"F");
                //}
                array_push($resultA,(($mobtained/$amax)*100));
                array_push($seatnosA,$un);
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
        */
        for($i=0; $i<($quizids/*+count($mquizids)*/); $i++)
            for($j=0; $j<count($closid); $j++)
                if(in_array($closid[$j], $closUniqueQMulti[$i]))
                    $closidCountActivity[$j]++;
        
        for($i=0; $i<($assignids/*+count($massignids)*/); $i++)
            for($j=0; $j<count($closid); $j++)
                if(in_array($closid[$j], $closUniqueAMulti[$i]))
                    $closidCountActivity[$j]++;
        
    ?>
    <!-- Now display data in formatted way -->
    <div id="container">
    <table class="generaltable" border="1">
        <tr>
            <th>Seat Number</th>
            <?php /****** CLO, Taxonomy, PLO ******/
            for($i=0; $i<count($closid); $i++) {
                if($closidCountActivity[$i]>0){
                ?>
                <th colspan="<?php echo $closidCountActivity[$i]; ?>"><?php echo $clonames[$i].", Taxonomy: ".strtoupper($lnames[$i]).", ".$plonames[$i]."<br>Passing Percentage: ".$clospasspercent[$i]."%"; ?></th>
                <?php
                }
            }
            ?>
            <th colspan="<?php echo count($closid); ?>">CLO Status (pass/fail)</th>
        </tr>
        <tr>
            <th></th>
            <?php
            /****** Activity Names + Attempt ******/
            for($i=0; $i<count($closid); $i++){
                $attemptno = 1;
                for($j=0; $j<($quizids/*+count($mquizids)*/); $j++)
                    if(in_array($closid[$i], $closUniqueQMulti[$j])){
                    ?>
                    <th><?php echo $quiznames[$j]."<br>(Attempt: ".$attemptno.")"; $attemptno++; ?></th>
                    <?php
                    }
                for($j=0; $j<($assignids/*+count($massignids)*/); $j++)
                    if(in_array($closid[$i], $closUniqueAMulti[$j])){
                    ?>
                    <th><?php echo $assignnames[$j]."<br>(Attempt: ".$attemptno.")"; $attemptno++; ?></th>
                    <?php
                    }
            }
            /****** CLOS ******/
            for($i=0; $i<count($closid); $i++) {
                ?>
                <th><?php echo $clonames[$i]; ?></th>
                <?php
            }
            ?>
        </tr>
        <?php
        $cohort_clo_stat = array(); // cohort course clo status -> increment for pass
        $activity_clo_stat = array(); // activity clo status -> increment for pass
        $total_attempts = 0;
        $all_clo_pass = 0;
        for($i=0; $i<count($closid); $i++) {
            $total_attempts += $closidCountActivity[$i];
        }
        //print_r($total_attempts);
        for($i=0; $i<$total_attempts; $i++)
            $activity_clo_stat[$i] = 0; // initialize all clos count with 0
        for($i=0; $i<count($closid); $i++)
            $cohort_clo_stat[$i] = 0; // initialize all clos status with 0
        $stdIdIndexToStore=0;
        foreach ($seatnos as $seatno) {
            $attempt_idx = 0; // re-initialize index for every student
            $ind_stud_clo_stat = array(); // individual student clo status -> 1 for pass, 0 for fail
            for($i=0; $i<count($closid); $i++)
                $ind_stud_clo_stat[$i] = 0; // set all clos status to fail
            $flag_all_clo_pass = 1;
        
        ?>
        <tr>
            <th> <?php echo strtoupper($seatno) ?> </th>
            <?php
            /****** QUIZZES/ASSIGNMENTS RECORDS ******/
            for($i=0; $i<count($closid); $i++){
                for($j=0; $j<($quizids/*+count($mquizids)*/); $j++)
                    if(in_array($closid[$i], $closUniqueQMulti[$j])){
                        $flag=0;
                        for($k=0; $k<count($seatnosQMulti[$j]); $k++){
                            if($seatno == $seatnosQMulti[$j][$k] && $closid[$i] == $closQMulti[$j][$k])
                            {
                                $flag=1;
                                //if($resultQMulti[$j][$k] == 'P')
                                if($resultQMulti[$j][$k] >= $clospasspercent[$i]){
                                    echo "<td><i class='fa fa-square' aria-hidden='true' style='color: #05E177'><span style='display: none'>P</span></i></td>";
                                    $ind_stud_clo_stat[$i] = 1; // set status pass
                                    $activity_clo_stat[$attempt_idx]++; // increment for attempt pass
                                    $attempt_idx++;
                                }
                                else {
                                    echo "<td><i class='fa fa-square' aria-hidden='true' style='color: #FE3939'><span style='display: none'>F</span></i></td>";
                                    $attempt_idx++;
                                }
                            }
                        }
                        if($flag==0)
                        {
                            echo '<td><i class="fa fa-times" aria-hidden="true"></i><span style="display: none">&#10005;</span></td>';
                            $attempt_idx++;
                        }
                    }
                for($j=0; $j<($assignids/*+count($massignids)*/); $j++)
                    if(in_array($closid[$i], $closUniqueAMulti[$j])){
                        $flag=0;
                        for($k=0; $k<count($seatnosAMulti[$j]); $k++){
                            if($seatno == $seatnosAMulti[$j][$k] && $closid[$i] == $closAMulti[$j][$k])
                            {
                                $flag=1;
                                //if($resultAMulti[$j][$k] == 'P')
                                if($resultAMulti[$j][$k] >= $clospasspercent[$i]){
                                    echo "<td><i class='fa fa-square' aria-hidden='true' style='color: #05E177'><span style='display: none'>P</span></i></td>";
                                    $ind_stud_clo_stat[$i] = 1; // set status pass
                                    $activity_clo_stat[$attempt_idx]++; // increment for attempt pass
                                    $attempt_idx++;
                                }
                                else {
                                    echo "<td><i class='fa fa-square' aria-hidden='true' style='color: #FE3939'><span style='display: none'>F</span></i></td>";
                                    $attempt_idx++;
                                }
                            }
                        }
                        if($flag==0)
                        {
                            echo '<td><i class="fa fa-times" aria-hidden="true"></i><span style="display: none">&#10005;</span></td>';
                            $attempt_idx++;
                        }
                    }
            }
            /****** Student CLOS status ******/
            for($i=0; $i<count($closid); $i++) {
                if($ind_stud_clo_stat[$i]){
                    echo "<td><i class='fa fa-square' aria-hidden='true' style='color: #05E177'><span style='display: none'>P</span></i></td>";
                    $cohort_clo_stat[$i]++;
                }
                else {
                    echo "<td><i class='fa fa-square' aria-hidden='true' style='color: #FE3939'><span style='display: none'>F</span></i></td>";
                    $flag_all_clo_pass = 0;
                }
            }


            //code for storing the results into the database
                
                /*for ($i=0; $<$stdids; $i++)
                {
                  
                    echo "<br/>";
                   
                    
                }*/
                /* echo $stdids[$stdIdIndexToStore];
                 echo "<br/>";
                //echo "$seatno<br/>";
                echo "$course_id<br/>"; 
                var_dump($closid); echo "<br/>";  
                  var_dump($ind_stud_clo_stat);
                  echo "<br/>"; */

                   for ($i=0 ; $i<count($closid); $i++)
                   {
                        $record = new stdClass();
                        $record->userid = $stdids[$stdIdIndexToStore];
                        $record->courseid = $course_id;
                        $record->cloid = $closid[$i];
                        $record->status = $ind_stud_clo_stat[$i];

                        /*echo "<br/><br/><br/>";
                        echo "$stdids[$stdIdIndexToStore]<br/>";
                        echo "$course_id<br/>";
                        echo "$closid[$i]<br/>";
                        echo "$ind_stud_clo_stat[$i]<br/>";*/

                         $flag=$DB->get_records_sql('SELECT * FROM  `mdl_clo_wise_result` WHERE courseid = ? AND userid = ? AND cloid = ?', array($course_id,$stdids[$stdIdIndexToStore],$closid[$i] ));
                         if($flag) //update
                         {
                             $sql_update="UPDATE mdl_clo_wise_result SET status =?       WHERE userid=? AND courseid = ? AND cloid = ?";
                                
                              $DB->execute($sql_update, array($ind_stud_clo_stat[$i],
                               $stdids[$stdIdIndexToStore], $course_id ,$closid[$i] ));
                        }

                         if(!$flag)//insert
                            $lastinsertid = $DB->insert_record('clo_wise_result', $record);
                    }
            
                $stdIdIndexToStore++;
            ?>
        </tr>
        <?php
        if($flag_all_clo_pass)
            $all_clo_pass++;
        }
        //print_r($activity_clo_stat);
        // Total Colspan for last 2 rows
        $colspan = 0;
        for($i=0; $i<count($closid); $i++) {
            $colspan += $closidCountActivity[$i];
        }
        $colspan++; // include seat num col
        ?>
        <tr>
            <th>CLO Attempts Level Aggregate</th>
            <?php
            /****** CLO Attempts Level Aggregate (Quantitative) ******/
            for($i=0; $i<$total_attempts; $i++) {
                echo "<td>".number_format((($activity_clo_stat[$i]/count($recStudents))*100),3)."%</td>";
            }
            for($i=0; $i<count($closid); $i++) {
                echo "<td></td>";
            }
            ?>
        </tr>
        <tr>
            <!--Course Level Aggregate Response (Quantitative)-->
            <th colspan="<?php echo $colspan; ?>" style="text-align: right;">Course Level Aggregate Response (Quantitative):</th>
            <?php
            /****** Course CLOS status (Quantitative) ******/
            for($i=0; $i<count($closid); $i++) {
                echo "<td>".number_format((($cohort_clo_stat[$i]/count($recStudents))*100),3)."%</td>";
            }
            $pass = array(); $fail = array();
            for($i=0; $i<count($closid); $i++) {
                array_push($pass, number_format((($cohort_clo_stat[$i]/count($recStudents))*100),3));
                array_push($fail, (100 - number_format((($cohort_clo_stat[$i]/count($recStudents))*100),3)));
            }
            ?>
        </tr>
        <!--
        <tr>
            <!--Individual CLO Status (pass/fail)-->
            <!--<th colspan="<?php echo $colspan; ?>" style="text-align: right;">Course Level Status (pass/fail):</th>-->
            <?php
            /****** Course CLOS status (pass/fail) ******/
            /* for($i=0; $i<count($closid); $i++) {
                if(($cohort_clo_stat[$i]/count($recStudents))*100 >= $clocohortpasspercent[$i])
                    echo "<td><i class='fa fa-square' aria-hidden='true' style='color: #05E177'><span style='display: none'>P</span></i></td>";
                else
                    echo "<td><i class='fa fa-square' aria-hidden='true' style='color: #FE3939'><span style='display: none'>F</span></i></td>";
            } */
            ?>
        <!--
        </tr>
        -->
        <tr>
            <!--Percentage of students passed in all CLOs-->
            <th colspan="<?php echo $colspan; ?>" style="text-align: right;">Percentage of students passed in all CLOs:</th>
            <td colspan="<?php echo count($closid); ?>">
            <?php
            /****** Percentage of students passed in all CLOs ******/
            echo number_format($all_clo_pass/count($recStudents)*100, 3)."%";
            ?>
            </td>
        </tr>
        <tr>
            <!--Course Level Status (pass/fail)-->
            <th colspan="<?php echo $colspan; ?>" style="text-align: right;">Course Level Status (pass/fail):</th>
            <td colspan="<?php echo count($closid); ?>">
            <?php
            /****** Course CLOS status (pass/fail) ******/
            if(($all_clo_pass/count($recStudents))*100 >= 50)
                echo "<i class='fa fa-square' aria-hidden='true' style='color: #05E177'><span style='display: none'>P</span></i>";
            else
                echo "<i class='fa fa-square' aria-hidden='true' style='color: #FE3939'><span style='display: none'>F</span></i>";
            ?>
            </td>
        </tr>
    </table>
    </div>

    <script>
        new PerfectScrollbar('#container');
    </script>

    <button id="myButton" class="btn btn-primary">Export to Excel</button>
    <?php require 'print_template.html'; ?>

    <!-- Export html Table to xls -->
    <script type="text/javascript" >
        $(document).ready(function(e){
            $("#myButton").click(function(e){ 
                $(".generaltable").table2excel({
                    name: "file name",
                    filename: "CLO-Report",
                    fileext: ".xls"
                });
            });
        });
    </script>

    <div id="container" style="width: 100%;">
        <canvas id="canvas"></canvas>
    </div>

    <!-- Vertical Bar Chart -->
    <script>
        var color = Chart.helpers.color;
        var barChartData = {
            labels: <?php echo json_encode($clonames); ?>,
            datasets: [{
                label: 'Pass',
                backgroundColor: color(window.chartColors.green).alpha(0.5).rgbString(),
                borderColor: window.chartColors.green,
                borderWidth: 1,
                data: <?php echo json_encode($pass); ?>
            }, {
                label: 'Fail',
                backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
                borderColor: window.chartColors.red,
                borderWidth: 1,
                data: <?php echo json_encode($fail); ?>
            }]

        };
		
        window.onload = function() {
            var ctx = document.getElementById("canvas").getContext("2d");
            window.myBar = new Chart(ctx, {
                type: 'bar',
                data: barChartData,
                options: {
					scales: {
						yAxes: [{
							ticks: {
								beginAtZero:true
							}
						}]
					},
                    responsive: true,
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Course Level CLO Status'
                    }
                }
            });

        };

    </script>