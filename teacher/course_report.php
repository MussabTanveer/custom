<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Course Report");
    $PAGE->set_heading("Course Report");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/course_report.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
?>
<link href="../css/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet">
<script src="../script/perfect-scrollbar/perfect-scrollbar.js"></script>

<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/table2excel/jquery.table2excel.js"></script>
<style>
#container {
    position: relative;
    margin: 0px auto;
    padding: 0px;
    width: 100%;
    overflow: auto;
}

/* Change the alignment of scrollbars */
/* Recommendation: modify CSS directly */
.ps__rail-x {
    top: 0px;
    bottom: auto; /* If using `top`, there shouldn't be a `bottom`. */
}
.ps__rail-y {
    left: 0px;
    right: auto; /* If using `left`, there shouldn't be a `right`. */
}
.ps__thumb-x {
    top: 2px;
    bottom: auto; /* If using `top`, there shouldn't be a `bottom`. */
}
.ps__thumb-y {
    left: 2px;
    right: auto; /* If using `left`, there shouldn't be a `right`. */
}

td{
    text-align:center;
}
th{
    text-align:center;
}
</style>

<?php

    if(!empty($_GET['course']))
    {
        $course_id=$_GET['course'];
        $coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
        
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
        echo "<h4 style='text-align:center'>Course Marks-Wise Assessment Sheet</h4>";
        
        // Get Grading Items
        $rec=$DB->get_records_sql("SELECT * FROM mdl_grading_policy gp, mdl_grading_mapping mg WHERE gp.courseid = ? AND gp.id = mg.gradingitem AND gp.courseid = mg.courseid ORDER BY mg.id", array($course_id));

        // Get all students of course
        $recStudents=$DB->get_records_sql("SELECT u.id AS sid, substring(u.username,4,8) AS seatorder, u.username AS seatnum, u.firstname, u.lastname
        FROM mdl_role_assignments ra, mdl_user u, mdl_course c, mdl_context cxt
        WHERE ra.userid = u.id
        AND ra.contextid = cxt.id
        AND cxt.contextlevel = ?
        AND cxt.instanceid = c.id
        AND c.id = ?
        AND (roleid=5) ORDER BY seatorder", array(50, $course_id));

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

            $quizCount=0;       //online
            $assignCount=0;     //online
            $projectCount=0;    //manual
            $otherCount=0;      //manual
            $MquizCount=0;      //manual
            $MassignCount=0;    //manual
            
            $flagquiz = 0;      //online
            $flagmid = 0;       //online
            $flagfinal = 0;     //manual
            $flagproject = 0;   //manual
            $flagother = 0;     //manual
            $flagassign = 0;    //online
            $flagmquiz = 0;     //manual
            $flagmmid = 0;      //manual
            $flagmassign = 0;   //manual


            /****** ONLINE QUIZZES ******/
            if(in_array("quiz", $gnames) && in_array(16, $modules)){
                
                $seatnosQMulti = array();
                $qnamesQMulti = array();
                $closQMulti = array();
                $resultQMulti = array();
                $tot_quesQuiz = array();
                $namesQ = array();

                for($i=0; $i<count($gnames); $i++)
                {
                    if ($gnames[$i]=="quiz" && $modules[$i]==16)
                    {
                        $flagquiz = 1;
                        $quizCount++;

                    $recQuiz=$DB->get_recordset_sql(
                    'SELECT
                        q.name as quizname,
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
                        qua.maxmark*COALESCE(qas.fraction, 0) AS marksobtained,
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
                        AND qa.uniqueid=qua.questionusageid AND qu.id=qua.questionid AND qua.id=qas.questionattemptid AND qas.state IN ("gradedright", "gradedwrong", "gaveup", "gradedpartial")
                    ORDER BY qa.userid, qu.id',
                    
                    array($instances[$i], 1));
                    
                    $seatnosQ = array();
                    $qnamesQ = array();
                    $closQ = array();
                    $resultQ = array();
                    //$quiznames = array();
                    $quizname = "";
                    foreach($recQuiz as $fe){
                        $quizname = $fe->quizname;
                        // echo $name;
                        $un = $fe->username;
                        $qname = $fe->name;
                        $clo=$fe->shortname;
                        $qmax = $fe->maxmark; $qmax = number_format($qmax, 2); // 2 decimal places
                        $mobtained = $fe->marksobtained; $mobtained = number_format($mobtained, 2);
                        /*if((($mobtained/$qmax)*100) > 50){
                            array_push($resultQ,"<font color='green'>P</font>");
                        }
                        else{
                            array_push($resultQ,"<font color='red'>F</font>");
                        }*/
                        array_push($resultQ, $mobtained);

                        //array_push($quiznames,$quizname);
                        // var_dump($quiznames);
                        // echo "<br>";
                        array_push($seatnosQ,$un);
                        $qname = $qname."(".$qmax.")";
                        array_push($qnamesQ,$qname);
                        array_push($closQ,$clo);
                        
                        //echo $quizname;
                    }
                    array_push($namesQ,$quizname);
                    $qnameQuizUnique = array_unique($qnamesQ);
                    array_push($tot_quesQuiz,count($qnameQuizUnique));
                    
                    //var_dump($seatnosQ);
                    //echo "<br>";
                    array_push($seatnosQMulti,$seatnosQ);
                    array_push($qnamesQMulti,$qnameQuizUnique);
                    array_push($closQMulti,$closQ);
                    array_push($resultQMulti,$resultQ);
                    }
                        
                }
                // echo "$quizCount";
                //var_dump($quiznames);
            }

            /****** MANUAL QUIZZES ******/
            if(in_array("quiz", $gnames) && in_array(-1, $modules)){
                
                $seatnosMQMulti = array();
                $qnamesMQMulti = array();
                $closMQMulti = array();
                $resultMQMulti = array();
                $tot_quesMQuiz = array();
                $namesMQ = array();

                for($i=0; $i<count($gnames); $i++)
                {
                    if ($gnames[$i]=="quiz" && $modules[$i]==-1)
                    {
                        $flagmquiz = 1;
                        $MquizCount++;

                    $recMQuiz=$DB->get_recordset_sql(
                    'SELECT
                    q.name AS quizname,
                    u.username,
                    qu.cloid,
                    qu.quesname,
                    qu.maxmark AS maxmark,
                    qa.obtmark AS marksobtained,
                    c.shortname
                    FROM
                        mdl_manual_quiz q,
                        mdl_manual_quiz_question qu,
                        mdl_manual_quiz_attempt qa,
                        mdl_user u,
                        mdl_competency c
                    WHERE
                        q.id=? AND q.id=qu.mquizid AND q.id=qa.quizid AND qa.userid=u.id AND qu.id=qa.questionid AND qu.cloid=c.id
                    ORDER BY qa.userid, qu.id',
                    
                    array($instances[$i]));
                    
                    $seatnosMQ = array();
                    $qnamesMQ = array();
                    $closMQ = array();
                    $resultMQ = array();
                    //$quiznames = array();
                    $quizname = "";
                    foreach($recMQuiz as $fe){
                        $quizname = $fe->quizname;
                        // echo $name;
                        $un = $fe->username;
                        $qname = $fe->quesname;
                        $clo=$fe->shortname;
                        $qmax = $fe->maxmark; $qmax = number_format($qmax, 2); // 2 decimal places
                        $mobtained = $fe->marksobtained; $mobtained = number_format($mobtained, 2);
                        /*if((($mobtained/$qmax)*100) > 50){
                            array_push($resultMQ,"<font color='green'>P</font>");
                        }
                        else{
                            array_push($resultMQ,"<font color='red'>F</font>");
                        }*/
                        array_push($resultMQ, $mobtained);

                        //array_push($quiznames,$quizname);
                        // var_dump($quiznames);
                        // echo "<br>";
                        array_push($seatnosMQ,$un);
                        $qname = $qname."(".$qmax.")";
                        array_push($qnamesMQ,$qname);
                        array_push($closMQ,$clo);
                        
                        //echo $quizname;
                    }
                    array_push($namesMQ,$quizname);
                    $qnameMQuizUnique = array_unique($qnamesMQ);
                    array_push($tot_quesMQuiz,count($qnameMQuizUnique));
                    
                    //var_dump($seatnosQ);
                    //echo "<br>";
                    array_push($seatnosMQMulti,$seatnosMQ);
                    array_push($qnamesMQMulti,$qnameMQuizUnique);
                    array_push($closMQMulti,$closMQ);
                    array_push($resultMQMulti,$resultMQ);
                    }
                        
                }
                // echo "$quizCount";
                //var_dump($quiznames);
            }

            /****** ONLINE ASSIGNMENT ******/
            if(in_array("assignment", $gnames) && in_array(1, $modules)){
                
                $seatnosAMulti = array();
                $closAMulti = array();
                $resultAMulti = array();
                $maxmarkA = array();
                $namesA = array();

                for($i=0; $i<count($gnames); $i++)
                {
                    if ($gnames[$i]=="assignment" && $modules[$i]==1)
                    {
                        $flagassign = 1;
                        $assignCount++;

                        //Get assign comp
                        $recAssignCLO=$DB->get_records_sql("SELECT DISTINCT c.id, c.shortname AS clo_name
                        
                        FROM mdl_competency c, mdl_assign a, mdl_course_modules cm, mdl_competency_modulecomp cmc
                
                        WHERE a.id=? AND cm.course=? AND cm.module=? AND a.id=cm.instance AND cm.id=cmc.cmid AND cmc.competencyid=c.id
                        
                        ORDER BY cmc.competencyid",
                        
                        array($instances[$i],$course_id,1));
                        
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
                            
                        array($instances[$i],-1));
                    
                        $seatnosA = array();
                        $closA = array();
                        $resultA = array();
                        
                        $amax = 0;
                        $assignname = "";

                        foreach($recAssign as $as){
                            $assignname = $as->assign_name;
                            $un = $as->seat_no;
                            $amax = $as->maxmark; $amax = number_format($amax, 2); // 2 decimal places
                            $mobtained = $as->marksobtained; $mobtained = number_format($mobtained, 2);
                            /*if( (($mobtained/$amax)*100) > 50){
                                array_push($resultA,"<font color='green'>P</font>");
                            }
                            else{
                                array_push($resultA,"<font color='red'>F</font>");
                            }*/
                            array_push($resultA,$mobtained);
                            array_push($seatnosA,$un);
                        }
                        array_push($namesA,$assignname);
                        array_push($maxmarkA,$amax);

                        foreach($recAssignCLO as $asCLO){
                            $clo = $asCLO->clo_name;
                            array_push($closA,$clo);
                        }
            
                        //var_dump($seatnosQ);
                        //echo "<br>";
                        array_push($seatnosAMulti,$seatnosA);
                        array_push($closAMulti,$closA);
                        array_push($resultAMulti,$resultA);
                    }
                }
            }

            /****** MANUAL ASSIGNMENT ******/
            if(in_array("assignment", $gnames) && in_array(-4, $modules)){
                
                $seatnosMAMulti = array();
                $closMAMulti = array();
                $resultMAMulti = array();
                $maxmarkMA = array();
                $namesMA = array();

                for($i=0; $i<count($gnames); $i++)
                {
                    if ($gnames[$i]=="assignment" && $modules[$i]==-4)
                    {
                        $recMAssign=$DB->get_records_sql(
                            'SELECT
                            u.username AS seat_no,
                            a.name AS assign_name,
                            a.maxmark AS maxmark,
                            att.obtmark AS marksobtained,
                            c.shortname
                            FROM
                                mdl_manual_assign_pro a,
                                mdl_user u,
                                mdl_manual_assign_pro_attempt att,
                                mdl_competency c
                            WHERE
                                a.id=? AND att.userid=u.id AND a.id=att.assignproid AND a.cloid=c.id
                            ORDER BY att.userid',
                            
                        array($instances[$i]));
                    
                        $seatnosMA = array();
                        $closMA = array();
                        $resultMA = array();
                        
                        $mamax = 0;
                        $massignname = "";

                        if($recMAssign) {
                            $flagmassign = 1;
                            $MassignCount++;
                        }
                        foreach($recMAssign as $as){
                            $massignname = $as->assign_name;
                            $un = $as->seat_no;
                            $clo = $as->shortname;
                            $mamax = $as->maxmark; $mamax = number_format($mamax, 2); // 2 decimal places
                            $mobtained = $as->marksobtained; $mobtained = number_format($mobtained, 2);
                            /*if( (($mobtained/$amax)*100) > 50){
                                array_push($resultA,"<font color='green'>P</font>");
                            }
                            else{
                                array_push($resultA,"<font color='red'>F</font>");
                            }*/
                            array_push($resultMA,$mobtained);
                            array_push($seatnosMA,$un);
                            array_push($closMA,$clo);
                        }
                        array_push($namesMA,$massignname);
                        array_push($maxmarkMA,$mamax);
                        $closMA = array_unique($closMA);
                        
                        array_push($seatnosMAMulti,$seatnosMA);
                        array_push($closMAMulti,$closMA);
                        array_push($resultMAMulti,$resultMA);
                    }
                }
            }

            /****** MANUAL PROJECT ******/
            if(in_array("project", $gnames) && in_array(-5, $modules)){
                
                $seatnosPMulti = array();
                $closPMulti = array();
                $resultPMulti = array();
                $maxmarkP = array();
                $namesP = array();

                for($i=0; $i<count($gnames); $i++)
                {
                    if ($gnames[$i]=="project" && $modules[$i]==-5)
                    {
                        $recProject=$DB->get_records_sql(
                            'SELECT
                            u.username AS seat_no,
                            a.name AS pro_name,
                            a.maxmark AS maxmark,
                            att.obtmark AS marksobtained,
                            c.shortname
                            FROM
                                mdl_manual_assign_pro a,
                                mdl_user u,
                                mdl_manual_assign_pro_attempt att,
                                mdl_competency c
                            WHERE
                                a.id=? AND att.userid=u.id AND a.id=att.assignproid AND a.cloid=c.id
                            ORDER BY att.userid',
                            
                        array($instances[$i]));
                        
                        $seatnosP = array();
                        $closP = array();
                        $resultP = array();
                        $pmax=0;
                        $proname = "";

                        if($recProject) {
                            $flagproject = 1;
                            $projectCount++;
                        }
                        foreach($recProject as $as){
                            $proname = $as->pro_name;
                            $un = $as->seat_no;
                            $clo = $as->shortname;
                            $pmax = $as->maxmark; $pmax = number_format($pmax, 2); // 2 decimal places
                            $mobtained = $as->marksobtained; $mobtained = number_format($mobtained, 2);
                            /*if( (($mobtained/$pmax)*100) > 50){
                                array_push($resultP,"<font color='green'>P</font>");
                            }
                            else{
                                array_push($resultP,"<font color='red'>F</font>");
                            }*/
                            array_push($resultP,$mobtained);
                            array_push($seatnosP,$un);
                            array_push($closP,$clo);
                        }
                        array_push($namesP,$proname);
                        array_push($maxmarkP,$pmax);
                        $closP = array_unique($closP);
                        
                        array_push($seatnosPMulti,$seatnosP);
                        array_push($closPMulti,$closP);
                        array_push($resultPMulti,$resultP);
                        
                    }
                        
                }
            }

            /****** MANUAL OTHER ******/
            if(in_array("other", $gnames) && in_array(-6, $modules)){
                
                $seatnosOMulti = array();
                $closOMulti = array();
                $resultOMulti = array();
                $maxmarkO = array();
                $namesO = array();

                for($i=0; $i<count($gnames); $i++)
                {
                    if ($gnames[$i]=="other" && $modules[$i]==-6)
                    {
                        $recOther=$DB->get_records_sql(
                            'SELECT
                            u.username AS seat_no,
                            o.name AS other_name,
                            o.maxmark AS maxmark,
                            att.obtmark AS marksobtained,
                            c.shortname
                            FROM
                                mdl_manual_other o,
                                mdl_user u,
                                mdl_manual_other_attempt att,
                                mdl_competency c
                            WHERE
                                o.id=? AND att.userid=u.id AND o.id=att.otherid AND o.cloid=c.id
                            ORDER BY att.userid',
                            
                        array($instances[$i]));
                        
                        $seatnosO = array();
                        $closO = array();
                        $resultO = array();
                        $omax=0;
                        $othername = "";

                        if($recOther) {
                            $flagother = 1;
                            $otherCount++;
                        }
                        foreach($recOther as $o){
                            $othername = $o->other_name;
                            $un = $o->seat_no;
                            $clo = $o->shortname;
                            $omax = $o->maxmark; $omax = number_format($omax, 2); // 2 decimal places
                            $mobtained = $o->marksobtained; $mobtained = number_format($mobtained, 2);
                            /*if( (($mobtained/$pmax)*100) > 50){
                                array_push($resultP,"<font color='green'>P</font>");
                            }
                            else{
                                array_push($resultP,"<font color='red'>F</font>");
                            }*/
                            array_push($resultO,$mobtained);
                            array_push($seatnosO,$un);
                            array_push($closO,$clo);
                        }
                        array_push($namesO,$othername);
                        array_push($maxmarkO,$omax);
                        $closO = array_unique($closO);
                        
                        array_push($seatnosOMulti,$seatnosO);
                        array_push($closOMulti,$closO);
                        array_push($resultOMulti,$resultO);
                        
                    }
                        
                }
            }
            
            /****** ONLINE MID TERM ******/
            if(in_array("mid term", $gnames) && in_array(16, $modules)){
                
                $pos = array_search('mid term', $gnames);
                $pos2 = array_search(16, $modules);
                if($pos == $pos2){
                    $recMid=$DB->get_recordset_sql(
                    'SELECT
                        q.name as midname,
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
                        qua.maxmark*COALESCE(qas.fraction, 0) AS marksobtained,
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
                        AND qa.uniqueid=qua.questionusageid AND qu.id=qua.questionid AND qua.id=qas.questionattemptid AND qas.state IN ("gradedright", "gradedwrong", "gaveup", "gradedpartial")
                    ORDER BY qa.userid, qu.id',
                    
                    array($instances[$pos], 1));
                    
                    $seatnosM = array();
                    $qnamesM = array();
                    $closM = array();
                    $resultM = array();
                    $midname = "";

                    foreach($recMid as $fe){
                        $midname = $fe->midname;
                        $flagmid = 1;
                        $un = $fe->username;
                        $qname = $fe->name;
                        $clo=$fe->shortname;
                        $qmax = $fe->maxmark; $qmax = number_format($qmax, 2); // 2 decimal places
                        $mobtained = $fe->marksobtained; $mobtained = number_format($mobtained, 2);
                        /*if((($mobtained/$qmax)*100) > 50){
                            array_push($resultM,"<font color='green'>P</font>");
                        }
                        else{
                            array_push($resultM,"<font color='red'>F</font>");
                        }*/
                        array_push($resultM, $mobtained);

                        array_push($seatnosM,$un);
                        $qname = $qname."(".$qmax.")";
                        array_push($qnamesM,$qname);
                        array_push($closM,$clo);
                    }
                    $qnameMidUnique = array_unique($qnamesM);
                    $tot_quesMid = count($qnameMidUnique);
                }
            }

            /****** MANUAL MID TERM ******/
            if(in_array("mid term", $gnames) && in_array(-2, $modules)){
                
                $pos = array_search('mid term', $gnames);
                $pos2 = array_search(-2, $modules);
                if($pos == $pos2){
                    $recMMid=$DB->get_recordset_sql(
                    'SELECT
                    q.name AS midname,
                    u.username,
                    qu.cloid,
                    qu.quesname,
                    qu.maxmark AS maxmark,
                    qa.obtmark AS marksobtained,
                    c.shortname
                    FROM
                        mdl_manual_quiz q,
                        mdl_manual_quiz_question qu,
                        mdl_manual_quiz_attempt qa,
                        mdl_user u,
                        mdl_competency c
                    WHERE
                        q.id=? AND q.id=qu.mquizid AND q.id=qa.quizid AND qa.userid=u.id AND qu.id=qa.questionid AND qu.cloid=c.id
                    ORDER BY qa.userid, qu.id',
                    
                    array($instances[$pos]));
                    
                    $seatnosMM = array();
                    $qnamesMM = array();
                    $closMM = array();
                    $resultMM = array();
                    $mmidname = "";

                    foreach($recMMid as $fe){
                        $mmidname = $fe->midname;
                        $flagmmid = 1;
                        $un = $fe->username;
                        $qname = $fe->quesname;
                        $clo=$fe->shortname;
                        $qmax = $fe->maxmark; $qmax = number_format($qmax, 2); // 2 decimal places
                        $mobtained = $fe->marksobtained; $mobtained = number_format($mobtained, 2);
                        /*if((($mobtained/$qmax)*100) > 50){
                            array_push($resultMM,"<font color='green'>P</font>");
                        }
                        else{
                            array_push($resultMM,"<font color='red'>F</font>");
                        }*/
                        array_push($resultMM, $mobtained);

                        array_push($seatnosMM,$un);
                        $qname = $qname."(".$qmax.")";
                        array_push($qnamesMM,$qname);
                        array_push($closMM,$clo);
                    }
                    $qnameMMidUnique = array_unique($qnamesMM);
                    $tot_quesMMid = count($qnameMMidUnique);
                }
            }
            
            /****** MANUAL FINAL EXAM ******/
            if(in_array("final exam", $gnames) && in_array(-3, $modules)){
                
                $pos = array_search('final exam', $gnames);
                $pos2 = array_search(-3, $modules);
                if($pos == $pos2){
                    $recFinal=$DB->get_recordset_sql(
                    'SELECT
                    q.name AS finalname,
                    u.username,
                    qu.cloid,
                    qu.quesname,
                    qu.maxmark AS maxmark,
                    qa.obtmark AS marksobtained,
                    c.shortname
                    FROM
                        mdl_manual_quiz q,
                        mdl_manual_quiz_question qu,
                        mdl_manual_quiz_attempt qa,
                        mdl_user u,
                        mdl_competency c
                    WHERE
                        q.id=? AND q.id=qu.mquizid AND q.id=qa.quizid AND qa.userid=u.id AND qu.id=qa.questionid AND qu.cloid=c.id
                    ORDER BY qa.userid, qu.id',
                    
                    array($instances[$pos]));
                    
                    $seatnosF = array();
                    $qnamesF = array();
                    $closF = array();
                    $resultF = array();
                    $finalname = "";

                    foreach($recFinal as $fe){
                        $finalname = $fe->finalname;
                        $flagfinal = 1;
                        $un = $fe->username;
                        $qname = $fe->quesname;
                        $clo=$fe->shortname;
                        $qmax = $fe->maxmark; $qmax = number_format($qmax, 2); // 2 decimal places
                        $mobtained = $fe->marksobtained; $mobtained = number_format($mobtained, 2);
                        /*if( (($mobtained/$qmax)*100) > 50){
                            array_push($resultF,"<font color='green'>P</font>");
                        }
                        else{
                            array_push($resultF,"<font color='red'>F</font>");
                        }*/
                        array_push($resultF, $mobtained);

                        array_push($seatnosF,$un);
                        $qname = $qname."(".$qmax.")";
                        array_push($qnamesF,$qname);
                        array_push($closF,$clo);
                    }
                    $qnameFinalUnique = array_unique($qnamesF);
                    $tot_quesFinal = count($qnameFinalUnique);
                }
            }
            ?>

            <!-- Now display data in formatted way -->
            <div id="container">
            <table class="generaltable" border="1">
                <tr>
                    <th>Seat Number</th>

                    <?php
                    /****** ONLINE QUIZZES ******/
                    for($i=0 ; $i<$quizCount; $i++)
                    {?>
                        <th colspan="<?php echo $tot_quesQuiz[$i] ?>"><?php echo $namesQ[$i] ?></th>
                    <?php
                    }
                    
                    /****** MANUAL QUIZZES ******/
                    for($i=0 ; $i<$MquizCount; $i++)
                    {?>
                        <th colspan="<?php echo $tot_quesMQuiz[$i] ?>"><?php echo $namesMQ[$i] ?></th>
                    <?php
                    }
                    
                    /****** ONLINE ASSIGNMENT ******/
                    for($i=0 ; $i<$assignCount; $i++)
                    {?>
                        <th><?php echo $namesA[$i] ?></th>
                    <?php
                    }

                    /****** MANUAL ASSIGNMENT ******/
                    for($i=0 ; $i<$MassignCount; $i++)
                    {?>
                        <th><?php echo $namesMA[$i] ?></th>
                    <?php
                    }
                    
                    /****** MANUAL PROJECT ******/
                    for($i=0 ; $i<$projectCount; $i++)
                    {?>
                        <th><?php echo $namesP[$i] ?></th>
                        <?php
                    }

                    /****** MANUAL OTHER ******/
                    for($i=0 ; $i<$otherCount; $i++)
                    {?>
                        <th><?php echo $namesO[$i] ?></th>
                        <?php
                    }
                    
                    /****** ONLINE MID TERM ******/
                    if($flagmid){
                    ?>
                    <th colspan="<?php echo $tot_quesMid ?>"><?php echo $midname ?></th>
                    <?php }

                    /****** MANUAL MID TERM ******/
                    if($flagmmid){
                    ?>
                    <th colspan="<?php echo $tot_quesMMid ?>"><?php echo $mmidname ?></th>
                    <?php }
                    
                    /****** MANUAL FINAL EXAM ******/ 
                    if($flagfinal){
                    ?>
                    <th colspan="<?php echo $tot_quesFinal ?>"><?php echo $finalname ?></th>
                    <?php } ?>
                </tr>
                <tr>
                    <th></th>

                    <?php
                    /****** ONLINE QUIZZES ******/
                    for($i=0; $i<$quizCount; $i++)
                    {
                        for($j=0; $j<$tot_quesQuiz[$i]; $j++){
                           ?> <th> <?php echo $qnamesQMulti[$i][$j]; ?> </th>
                       <?php 
                       }
                    }

                    /****** MANUAL QUIZZES ******/
                    for($i=0; $i<$MquizCount; $i++)
                    {
                        for($j=0; $j<$tot_quesMQuiz[$i]; $j++){
                           ?> <th> <?php echo $qnamesMQMulti[$i][$j]; ?> </th>
                       <?php 
                       }
                    }
                    
                    /****** ONLINE ASSIGNMENT ******/
                    for($i=0 ; $i<$assignCount; $i++)
                    {?>
                        <th><?php echo $maxmarkA[$i] ?></th>
                    <?php
                    }

                    /****** MANUAL ASSIGNMENT ******/
                    for($i=0 ; $i<$MassignCount; $i++)
                    {?>
                        <th><?php echo $maxmarkMA[$i] ?></th>
                    <?php
                    }
                    
                    /****** MANUAL PROJECT ******/
                    for($i=0 ; $i<$projectCount; $i++)
                    {?>
                        <th><?php echo $maxmarkP[$i] ?></th>
                    <?php
                    }

                    /****** MANUAL OTHER ******/
                    for($i=0 ; $i<$otherCount; $i++)
                    {?>
                        <th><?php echo $maxmarkO[$i] ?></th>
                    <?php
                    }
                    
                    /****** ONLINE MID TERM ******/
                    if($flagmid){
                        foreach($qnameMidUnique as $q){
                            echo "<th>$q</th>";
                        }
                    }

                    /****** MANUAL MID TERM ******/
                    if($flagmmid){
                        foreach($qnameMMidUnique as $q){
                            echo "<th>$q</th>";
                        }
                    }
                    
                    /****** MANUAL FINAL EXAM ******/
                    if($flagfinal){
                        foreach($qnameFinalUnique as $q){
                            echo "<th>$q</th>";
                        }
                    }
                    ?>
                </tr>
                <tr>
                    <th></th>
                    <?php
                    /****** ONLINE QUIZZES ******/
                    for($i=0; $i<$quizCount; $i++)
                    {
                        for($j=0; $j<$tot_quesQuiz[$i]; $j++){
                           ?> <th> <?php echo $closQMulti[$i][$j]; ?> </th>
                       <?php 
                       }
                    }

                    /****** MANUAL QUIZZES ******/
                    for($i=0; $i<$MquizCount; $i++)
                    {
                        for($j=0; $j<$tot_quesMQuiz[$i]; $j++){
                           ?> <th> <?php echo $closMQMulti[$i][$j]; ?> </th>
                       <?php 
                       }
                    }
                    
                    /****** ONLINE ASSIGNMENT ******/
                    for($i=0; $i<$assignCount; $i++)
                    {
                        ?>
                        <th> 
                        <?php
                        for($j=0; $j<count($closAMulti[$i]); $j++){
                           echo $closAMulti[$i][$j];
                           echo " ";
                        }
                        ?>
                        </th>
                        <?php
                    }

                    /****** MANUAL ASSIGNMENT ******/
                    for($i=0; $i<$MassignCount; $i++)
                    {
                        ?>
                        <th> 
                        <?php
                        for($j=0; $j<count($closMAMulti[$i]); $j++){
                           echo $closMAMulti[$i][$j];
                           echo " ";
                        }
                        ?>
                        </th>
                        <?php
                    }

                    /****** MANUAL PROJECT ******/
                    for($i=0; $i<$projectCount; $i++)
                    {
                        ?>
                        <th>
                        <?php
                        for($j=0; $j<count($closPMulti[$i]); $j++){
                           echo $closPMulti[$i][$j];
                           echo " ";
                        }
                        ?>
                        </th>
                        <?php
                    }

                    /****** MANUAL OTHER ******/
                    for($i=0; $i<$otherCount; $i++)
                    {
                        ?>
                        <th>
                        <?php
                        for($j=0; $j<count($closOMulti[$i]); $j++){
                           echo $closOMulti[$i][$j];
                           echo " ";
                        }
                        ?>
                        </th>
                        <?php
                    }

                    /****** ONLINE MID TERM ******/
                    if($flagmid){
                        for($i=0; $i < count($qnameMidUnique); $i++){
                            echo "<th>$closM[$i]</th>";
                        }
                    }

                    /****** MANUAL MID TERM ******/
                    if($flagmmid){
                        for($i=0; $i < count($qnameMMidUnique); $i++){
                            echo "<th>$closMM[$i]</th>";
                        }
                    }
                    
                    /****** MANUAL FINAL EXAM ******/
                    if($flagfinal){
                        for($i=0; $i < count($qnameFinalUnique); $i++){
                            echo "<th>$closF[$i]</th>";
                        }
                    }
                    ?>
                </tr>

                <?php
                foreach ($seatnos as $seatno) {
                    ?>
                    <tr> 
                        <th>  <?php echo strtoupper($seatno) ?> </th>
                        <?php

                            /****** ONLINE QUIZZES ******/
                            for($i=0; $i<$quizCount; $i++)
                            {
                                $flag=0;
                                for($j=0; $j<count($seatnosQMulti[$i]); $j++){
                                    if($seatno == $seatnosQMulti[$i][$j])
                                    {
                                        $flag=1;
                                        ?>
                                        <td> <?php echo $resultQMulti[$i][$j]; ?> </td>
                                        <?php
                                    }
                                }
                                if($flag==0)
                                {
                                    for($j=0; $j<$tot_quesQuiz[$i]; $j++){
                                        echo '<td><i class="fa fa-times" aria-hidden="true"></i><span style="display: none">&#10005;</span></td>';
                                    }
                                }
                            }

                            /****** MANUAL QUIZZES ******/
                            for($i=0; $i<$MquizCount; $i++)
                            {
                                $flag=0;
                                for($j=0; $j<count($seatnosMQMulti[$i]); $j++){
                                    if($seatno == $seatnosMQMulti[$i][$j])
                                    {
                                        $flag=1;
                                        ?>
                                        <td> <?php echo $resultMQMulti[$i][$j]; ?> </td>
                                        <?php
                                    }
                                }
                                if($flag==0)
                                {
                                    for($j=0; $j<$tot_quesMQuiz[$i]; $j++){
                                        echo '<td><i class="fa fa-times" aria-hidden="true"></i><span style="display: none">&#10005;</span></td>';
                                    }
                                }
                            }

                            /****** ONLINE ASSIGNMENT ******/
                            for($i=0; $i<$assignCount; $i++)
                            {
                                $flag=0;
                                for($j=0; $j<count($seatnosAMulti[$i]); $j++){
                                    if($seatno == $seatnosAMulti[$i][$j])
                                    {
                                        $flag=1;
                                        ?>
                                        <td> <?php echo $resultAMulti[$i][$j]; ?> </td>
                                        <?php
                                    }
                                }
                                if($flag==0)
                                {
                                    echo '<td><i class="fa fa-times" aria-hidden="true"></i><span style="display: none">&#10005;</span></td>';
                                }
                            }

                            /****** MANUAL ASSIGNMENT ******/
                            for($i=0; $i<$MassignCount; $i++)
                            {
                                $flag=0;
                                for($j=0; $j<count($seatnosMAMulti[$i]); $j++){
                                    if($seatno == $seatnosMAMulti[$i][$j])
                                    {
                                        $flag=1;
                                        ?>
                                        <td> <?php echo $resultMAMulti[$i][$j]; ?> </td>
                                        <?php
                                    }
                                }
                                if($flag==0)
                                {
                                    echo '<td><i class="fa fa-times" aria-hidden="true"></i><span style="display: none">&#10005;</span></td>';
                                }
                            }

                            /****** MANUAL PROJECT ******/
                            for($i=0; $i<$projectCount; $i++)
                            {
                                $flag=0;
                                for($j=0; $j<count($seatnosPMulti[$i]); $j++){
                                    if($seatno == $seatnosPMulti[$i][$j])
                                    {
                                        $flag=1;
                                        ?>
                                        <td> <?php echo $resultPMulti[$i][$j]; ?> </td>
                                        <?php
                                    }
                                }
                                if($flag==0)
                                {
                                    echo '<td><i class="fa fa-times" aria-hidden="true"></i><span style="display: none">&#10005;</span></td>';
                                }
                            }

                            /****** MANUAL OTHER ******/
                            for($i=0; $i<$otherCount; $i++)
                            {
                                $flag=0;
                                for($j=0; $j<count($seatnosOMulti[$i]); $j++){
                                    if($seatno == $seatnosOMulti[$i][$j])
                                    {
                                        $flag=1;
                                        ?>
                                        <td> <?php echo $resultOMulti[$i][$j]; ?> </td>
                                        <?php
                                    }
                                }
                                if($flag==0)
                                {
                                    echo '<td><i class="fa fa-times" aria-hidden="true"></i><span style="display: none">&#10005;</span></td>';
                                }
                            }
                            
                            /****** ONLINE MID TERM ******/
                            if($flagmid){
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
                                        echo '<td><i class="fa fa-times" aria-hidden="true"></i><span style="display: none">&#10005;</span></td>';
                                    }
                                }
                            }

                            /****** MANUAL MID TERM ******/
                            if($flagmmid){
                                $flag=0;
                                for($i=0 ; $i<count($seatnosMM); $i++)
                                {
                                    if($seatno == $seatnosMM[$i])
                                    {
                                        $flag=1;
                                        echo "<td>$resultMM[$i]</td>";
                                    }
                                }
                                if($flag==0)
                                {
                                    foreach ($qnameMMidUnique as $quesUnique)
                                    {
                                        echo '<td><i class="fa fa-times" aria-hidden="true"></i><span style="display: none">&#10005;</span></td>';
                                    }
                                }
                            }

                            /****** MANUAL FINAL EXAM ******/
                            if($flagfinal){
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
                                        echo '<td><i class="fa fa-times" aria-hidden="true"></i><span style="display: none">&#10005;</span></td>';
                                    }
                                }
                            }
                        ?>
                     </tr>
                <?php
                }

                ?>

            </table>
            </div>

            <script>
                new PerfectScrollbar('#container');
            </script>

            <button id="myButton" class="btn btn-primary">Export to Excel</button>
            <?php require '../templates/print_template.html'; ?>

            <!-- Export html Table to xls -->
            <script type="text/javascript" >
                $(document).ready(function(e){
                    $("#myButton").click(function(e){ 
                        $(".generaltable").table2excel({
                            name: "file name",
                            filename: "Course-Report",
                            fileext: ".xls"
                        });
                    });
                });
            </script>
            <?php
        }
        else{
            echo "<h5 style='color:red'> <br />Found no mapped graded activity item of this course! </h5> <br /><a href=./grading_policy.php?course=$course_id>Add a grading policy item</a>.<br /><a href=./map_grading_item.php?course=$course_id>Map activities to grading policy item</a>.";
        }
        echo "<a class='btn btn-default' href='./report_teacher.php?course=$course_id'>Go Back</a>";
    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./teacher_courses.php">Back</a>
    <?php
    }
    echo $OUTPUT->footer();
?>
