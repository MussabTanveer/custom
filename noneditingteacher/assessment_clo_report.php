<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Assessment CLO Report");
    $PAGE->set_heading("Assessment CLO Report");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/noneditingteacher/assessment_clo_report.php');
    
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
        echo "<h4 style='text-align:center'>Assessment CLO-Wise Report</h4>";
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
        
        $assessids = 0;
        
        // Find attached rubric and
        //Get course clo with its plo, level, passing percentage
        $recRub=$DB->get_recordset_sql('SELECT
            clo.id AS cloid,
            clo.shortname AS cloname,
            clo.idnumber,
            clo.description,
            ckpi.kpi AS passpercent,
            clocohortkpi.kpi AS cohortpasspercent,
            plo.shortname as ploname,
            plo.idnumber AS ploidn,
            taxlvl.id AS lvlid,
            taxlvl.name,
            taxlvl.level,
            taxdom.name as taxname
            FROM
            mdl_competency clo,
            mdl_clo_kpi ckpi,
            mdl_clo_cohort_kpi clocohortkpi,
            mdl_competency plo, 
            mdl_competency_coursecomp compcour,
            mdl_taxonomy_clo_level taxclolvl,
            mdl_taxonomy_levels taxlvl,
            mdl_taxonomy_domain taxdom
            WHERE clo.id=compcour.competencyid and clo.id=taxclolvl.cloid and clo.id=ckpi.cloid AND clo.id=clocohortkpi.cloid and taxclolvl.levelid=taxlvl.id and taxlvl.domainid=taxdom.id and plo.id=clo.parentid and courseid=?'
            ,array($course_id));
        
        if($recRub){
            $acloid = 0; $levelid = 0; $kpi = 0;
            $flag=0;
            foreach ($recRub as $records) {
                $clonames = array(); $plonames = array(); $lnames = array(); $closid = array(); $clospasspercent = array(); $clocohortpasspercent = array();
                $flag++;
                $acloid = $records->cloid;
                $levelid = $records->lvlid;

                $cid = $records->cloid;
                $clo = $records->cloname;
                $plo = $records->ploidn;
                $level = $records->level;
                $pp = $records->passpercent;
                $cp = $records->cohortpasspercent;
                array_push($closid, $cid); // array of clo ids
                array_push($clonames, $clo); // array of clo names
                array_push($plonames, $plo); // array of plo idnum
                array_push($lnames, $level); // array of levels
                array_push($clospasspercent, $pp); // array of clo individual stud pass percent
                array_push($clocohortpasspercent, $cp); // array of clo cohort course pass percent
                if($levelid>=7) // level belongs to psychomotor or affective domain
                    break;
            }
            $closidCountActivity = array();
            for($j=0; $j<count($closid); $j++)
                $closidCountActivity[$j]=0;
            if($flag){
                $flagR=0; $rubric_id=0;
                $recR=$DB->get_records_sql('SELECT rubric FROM mdl_clo_rubric WHERE cloid=?', array($acloid));
                foreach ($recR as $R) {
                    $flagR++;
                    $rubric_id = $R->rubric;
                }
                if($flagR){
                    // Get Assessments
                    $rubMaxM=$DB->get_records_sql("SELECT rs.id, MAX(rs.score) AS maxmark, rs.criterion FROM mdl_rubric r, mdl_rubric_scale rs WHERE r.id=? AND r.id=rs.rubric GROUP BY rs.criterion", array($rubric_id));
                    $assessmaxmarks=0;
                    foreach ($rubMaxM as $asmm) {
                        $assessmaxmarks += $asmm->maxmark;
                    }
                    // Get Assessments
                    $assessAct=$DB->get_records_sql("SELECT * FROM `mdl_practical_assessment` WHERE courseid = ? ", array($course_id));
                    $asIdsArr = array(); $asnames = array();
                    foreach ($assessAct as $asid) {
                        $id = $asid->id;
                        $name = $asid->assessment;
                        array_push($asIdsArr, $id); // array of assessment ids
                        array_push($asnames, $name); // array of assessment names
                    }

                    // Find students assessment records
                    $seatnosAsMulti = array();
                    $closUniqueAsMulti = array();
                    $closAsMulti = array();
                    $resultAsMulti = array();
                    $cloAsCount = array();
                    $assessnames = array();

                    // ASSESSMENTS
                    for($a=0; $a < count($asIdsArr); $a++){
                        $seatnosAs = array();
                        $closAs = array();
                        $resultAs = array();

                        $activityname = $asnames[$a];
                        $actid = $asIdsArr[$a];// ASSESSMENTS
                        
                        $recAssess=$DB->get_recordset_sql(
                        'SELECT
                        att.id,
                        substring(u.username,4,8) AS seatorder,
                        u.username AS seat_no,
                        SUM(att.obtmark) AS marksobtained,
                        att.userid
                        FROM mdl_assessment_attempt att, mdl_user u
                        WHERE att.aid=? AND att.userid=u.id
                        GROUP BY att.userid
                        ORDER BY seatorder, att.cid'
                        , array($actid));
                        
                        foreach($recAssess as $rA){
                            $un = $rA->seat_no;
                            $clo=$acloid;
                            $asmax = $assessmaxmarks; $asmax = number_format($asmax, 2); // 2 decimal places
                            $mobtained = $rA->marksobtained; $mobtained = number_format($mobtained, 2);
                            /*if( (($mobtained/$qmax)*100) > 50){
                                array_push($resultQ,"P");
                            }
                            else{
                                array_push($resultQ,"F");
                            }*/
                            array_push($resultAs,(($mobtained/$asmax)*100));
                            array_push($seatnosAs,$un);
                            array_push($closAs,$clo);
                        }
                        $assessids++;
                        $cloAsUnique = array_unique($closAs);
                        array_push($cloAsCount,count($cloAsUnique));
                        array_push($seatnosAsMulti,$seatnosAs);
                        array_push($closUniqueAsMulti,$cloAsUnique);
                        array_push($closAsMulti,$closAs);
                        array_push($resultAsMulti,$resultAs);
                        array_push($assessnames,$activityname);
                    }
                }
            }
        }

        for($i=0; $i<($assessids); $i++)
            for($j=0; $j<count($closid); $j++)
                if(in_array($closid[$j], $closUniqueAsMulti[$i]))
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
                for($j=0; $j<($assessids/*+count($massignids)*/); $j++)
                    if(in_array($closid[$i], $closUniqueAsMulti[$j])){
                    ?>
                    <th><?php echo $assessnames[$j]."<br>(Attempt: ".$attemptno.")"; $attemptno++; ?></th>
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
            /****** QUIZZES/ASSIGNMENTS/ASSESSMENT RECORDS ******/
            for($i=0; $i<count($closid); $i++){
                for($j=0; $j<($assessids/*+count($massignids)*/); $j++)
                    if(in_array($closid[$i], $closUniqueAsMulti[$j])){
                        $flag=0;
                        for($k=0; $k<count($seatnosAsMulti[$j]); $k++){
                            if($seatno == $seatnosAsMulti[$j][$k] && $closid[$i] == $closAsMulti[$j][$k])
                            {
                                $flag=1;
                                //if($resultAMulti[$j][$k] == 'P')
                                if($resultAsMulti[$j][$k] >= $clospasspercent[$i]){
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
            <!--Percentage of students passed in CLO-->
            <th colspan="<?php echo $colspan; ?>" style="text-align: right;">Percentage of students passed in CLO:</th>
            <td colspan="<?php echo count($closid); ?>">
            <?php
            /****** Percentage of students passed in CLO ******/
            echo number_format($all_clo_pass/count($recStudents)*100, 3)."%";
            ?>
            </td>
        </tr>
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
                    filename: "CLO-Report",
                    fileext: ".xls"
                });
            });
        });
    </script>

        <?php
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
