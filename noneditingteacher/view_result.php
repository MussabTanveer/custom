<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/table2excel/jquery.table2excel.min.js"></script>
<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Assessment Result");
    $PAGE->set_heading("Assessment Result");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/noneditingteacher/view_result.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    ?>
    <style>
        th {
            color: navy;
        }
        td {
            color: maroon;
        }
        th, td {
            font-size: 16px;
        }
    </style>
    <?php

    if(!empty($_GET['assessmentid']) && !empty($_GET['course']))
    {
        $course_id=$_GET['course'];
		$course_id = (int)$course_id; // convert course id from string to int
		$coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
        $aid=$_GET['assessmentid'];

        $stdids=array();

        // Find attached rubric
        $rec=$DB->get_recordset_sql('SELECT
            clo.id AS cloid,
            clo.shortname,
            clo.idnumber,
            clo.description,
            plo.shortname as ploname,
            plo.idnumber,
            taxlvl.id AS lvlid,
            taxlvl.name,
            taxlvl.level,
            taxdom.name as taxname
            FROM
            mdl_competency clo,
            mdl_competency plo, 
            mdl_competency_coursecomp compcour,
            mdl_taxonomy_clo_level taxclolvl,
            mdl_taxonomy_levels taxlvl,
            mdl_taxonomy_domain taxdom
            WHERE clo.id=compcour.competencyid and clo.id=taxclolvl.cloid and taxclolvl.levelid=taxlvl.id and taxlvl.domainid=taxdom.id and plo.id=clo.parentid and courseid=?'
            ,array($course_id));
        
        if($rec){
            $cloid = 0; $levelid = 0;
            $flag=0;
            foreach ($rec as $records) {
                $flag++;
                $cloid = $records->cloid;
                $levelid = $records->lvlid;
                if($levelid>=7) // level belongs to psychomotor or affective domain
                    break;
            }

            $rec->close();

            if($flag){
                //echo "CLOID: $cloid LEVELID: $levelid";
                $flagR=0; $rubric_id=0;
                $recR=$DB->get_recordset_sql('SELECT rubric FROM mdl_clo_rubric WHERE cloid=?', array($cloid));
                foreach ($recR as $R) {
                    $flagR++;
                    $rubric_id = $R->rubric;
                }
                if($flagR){
                    //echo "RUBRICID: $rubric_id";
                    $rubricInfo=$DB->get_records_sql('SELECT * FROM mdl_rubric WHERE id= ?', array($rubric_id));

                    if($rubricInfo){
                        // DISPLAY RUBRIC VIEW
                        foreach ($rubricInfo as $rInfo) {
                            $name = $rInfo->name;
                            $description = $rInfo->description;
                        }
                        echo "<h3><u>Rubric</u></h3>";
                        echo "<h3>$name</h3>";
                        echo "<h4>$description</h4>";

                        $criterionInfo=$DB->get_records_sql('SELECT * FROM mdl_rubric_criterion WHERE rubric = ?', array($rubric_id));
                        $criteriaId = array();
                        $criteriaDesc = array();
                        foreach ($criterionInfo as $cInfo) {
                            $id = $cInfo->id;
                            $description = $cInfo->description;
                            array_push($criteriaId, $id);
                            array_push($criteriaDesc, $description);
                        }
                        ?>
                        
                        <br />
                        <table id="myTable" style="border: medium solid #000;" border="3" width="100%" cellpadding="10px">
                            <?php
                            $maxScales=0;
                            for($i=0; $i<count($criteriaDesc); $i++){
                            ?>
                            <tr>
                                <th>Criterion <?php echo ($i+1)."<br>".$criteriaDesc[$i] ?></th>
                                <?php
                                $scaleInfo=$DB->get_records_sql('SELECT * FROM mdl_rubric_scale WHERE rubric = ? AND criterion = ?', array($rubric_id, $criteriaId[$i]));
                                //$s = 1;
                                $temp=0;
                                foreach ($scaleInfo as $sInfo) {
                                    //$id = $sInfo->id;
                                    $description = $sInfo->description;
                                    $score = $sInfo->score;
                                    echo "<td>$description<br>Score: $score</td>";
                                    //$s++;
                                    $temp++;
                                }
                                if($temp>$maxScales)
                                    $maxScales=$temp;
                                ?>
                            </tr>
                            <?php
                            }
                            ?>
                        </table>
                        <br><br>
                        <script>
                            // create table header row
                            var max = <?php echo json_encode($maxScales); ?>;
                            var table = document.getElementById("myTable");
                            var row = table.insertRow(0);
                            var headerCell = document.createElement("th");
                            headerCell.innerHTML = "Criteria";
                            row.appendChild(headerCell);
                            for (var i = 0; i < max; i++) {
                                var headerCell = document.createElement("th");
                                headerCell.innerHTML = "Scale " + (i+1);
                                row.appendChild(headerCell);
                            }
                        </script>
                        <?php
                    }
                }
            }
        }
        
        //$ques=$DB->get_records_sql("SELECT * FROM mdl_manual_quiz_question  WHERE maid=$aid");

        $obtMarksq=$DB->get_records_sql("SELECT * FROM mdl_assessment_attempt  WHERE aid=$aid");

        //$ques=$DB->get_records_sql("SELECT * FROM mdl_manual_quiz_question  WHERE aid=$aid");

        $obtMarks=array();
        //$cloids=array();

        if($obtMarksq)
        {
            foreach ($obtMarksq as $omark) {
                $userid = $omark->userid;
                $obtmark=$omark->obtmark;
                array_push($obtMarks,$obtmark);
            }
        }
        else
        {
        echo "<font color=red>Assessment has not been graded yet!</font>";
            goto down;
        }
        
        ?>
        <table border='10' cellpadding='15' id ="mytable">
        <tr>
        <th> Seat No. </th>
        <?php
        for($i=0; $i<count($criteriaId); $i++){
            ?><th> <?php echo "Criterion ".($i+1);?> </th>
            <?php
        }
        ?>
        </tr>
        <?php
        
        $users=$DB->get_records_sql("SELECT u.id AS sid, u.username AS seatnum, u.firstname, u.lastname
            FROM mdl_role_assignments ra, mdl_user u, mdl_course c, mdl_context cxt
            WHERE ra.userid = u.id
            AND ra.contextid = cxt.id
            AND cxt.contextlevel = 50
            AND cxt.instanceid = c.id
            AND c.id = $course_id
            AND (roleid=5)");
        
        $obtmarksIndex=0;
        if($users)
        {
            foreach ($users as $user ) {
            ?>
            <tr>
                <td> <?php echo $user->seatnum; array_push ($stdids,$user->sid); ?> </td>
                
                <?php
                foreach ($criteriaId as $cid){
                ?>
                    <td ><?php echo $obtMarks[$obtmarksIndex]; 
                    $obtmarksIndex++; ?></td >
            
                <?php
                }  ?> </tr> <?php
            }
        }
        ?>
    </table>
    <br />
    <button id="myButton" class="btn btn-success">Export to Excel</button>
    <!-- Export html Table to xls -->
    <script type="text/javascript" >
        $(document).ready(function(e){
            $("#myButton").click(function(e){ 
                $("#mytable").table2excel({
                    name: "file name",
                    filename: "assessment_result",
                    fileext: ".xls"
                });
            });
        });
    </script>
<?php
    }
    else{
        ?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="../teacher/teacher_courses.php">Back</a>
    <?php
    }
down:
    echo $OUTPUT->footer();
    