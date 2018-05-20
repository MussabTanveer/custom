<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/table2excel/jquery.table2excel.min.js"></script>
<script src="../script/formcache/formcache.min.js"></script>
<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Assessment Marks");
    $PAGE->set_heading("Assessment Marks");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/noneditingteacher/assessment_marks.php');
    
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
        input[type='number'] {
            -moz-appearance:textfield;
            max-width: 75px;
            border: none;
        }
        input[type='number']:focus {
            outline: none;
            border: none;
        }
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
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

        // Associated CLOs with Course and their Mapping

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
                        $criteriaMaxScore = array();
                        $scount = array();
                        $sscoreMulti = array();
                        $snoMulti = array();
                        ?>
                        
                        <br />
                        <table id="myTable" style="border: medium solid #000;" border="3" width="100%" cellpadding="10px">
                            <?php
                            $maxScales=0;
                            for($i=0; $i<count($criteriaDesc); $i++){
                                $maxScore = 0;
                                $scount[$i] = 0;
                                $sscore = array();
                                $sno = array();
                            ?>
                            <tr>
                                <th>Criterion <?php echo ($i+1)."<br>".$criteriaDesc[$i] ?></th>
                                <?php
                                $scaleInfo=$DB->get_records_sql('SELECT * FROM mdl_rubric_scale WHERE rubric = ? AND criterion = ?', array($rubric_id, $criteriaId[$i]));
                                $temp=0;
                                $sc = 1;
                                foreach ($scaleInfo as $sInfo) {
                                    $scount[$i]++;
                                    //$id = $sInfo->id;
                                    $description = $sInfo->description;
                                    $score = $sInfo->score;
                                    array_push($sscore, $score);
                                    array_push($sno, "Scale ".$sc);
                                    echo "<td>$description<br>Score: $score</td>";
                                    if($score>$maxScore)
                                        $maxScore = $score;
                                    $temp++;
                                    $sc++;
                                }
                                array_push($criteriaMaxScore, $maxScore);
                                array_push($sscoreMulti, $sscore);
                                array_push($snoMulti, $sno);
                                if($temp>$maxScales)
                                    $maxScales=$temp;
                                ?>
                            </tr>
                            <?php
                            }
                            //print_r($criteriaMaxScore);
                            ?>
                        </table>
                        <br><br>
                        <button id="myButton" class="btn btn-success">Export to Excel</button><br><br>
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

                        <!-- Export html Table to xls -->
                        <script type="text/javascript" >
                            $(document).ready(function(e){
                                $("#myButton").click(function(e){ 
                                    $("#myTable").table2excel({
                                        name: "file name",
                                        filename: "rubric",
                                        fileext: ".xls"
                                    });
                                });
                            });
                        </script>

                        <?php
                        // FORM TO ENTER MARKS
                        $cnames=array();
                        $cids=array();
                        $stdids=array();
                        if($criterionInfo)
                        {
                            $i=0;
                            foreach ($criterionInfo as $c) {
                                $id = $c->id;
                                $i++;
                                $cname='Criterion '.$i;
                                array_push ($cnames,$cname);
                                array_push ($cids,$id);
                            }
                        }
                        echo "<br>";
                        echo "<h3><u>Enter Marks</u></h3>";
                        ?>
            <form method="post" action="insert_result.php" id="myForm">
                <table border='10' cellpadding='10' id ="mytable">
                <tr>
                    <th> Seat No. </th>
                    <?php
                    $ccount=0;
                    foreach ($cnames as $cname){
                        $ccount++;

                        ?><th> <?php echo $cname ; ?> </th>
                        <?php
                    }
                    ?>
                </tr>
                <?php
                
                $users=$DB->get_records_sql("SELECT u.id AS sid,substring(u.username,4,8) AS seatnum, u.firstname, u.lastname
                FROM mdl_role_assignments ra, mdl_user u, mdl_course c, mdl_context cxt
                WHERE ra.userid = u.id
                AND ra.contextid = cxt.id
                AND cxt.contextlevel = 50
                AND cxt.instanceid = c.id
                AND c.id = $course_id
                AND (roleid=5) ORDER BY seatnum");
                
                if($users)
                {
                    foreach ($users as $user ) {
                        

                    ?>
                    <tr>

                        <td>
                            <?php echo $user->seatnum; array_push ($stdids,$user->sid); ?>
                        </td>
                        <?php

  
                        for($i=0; $i<count($cids); $i++){
                        //foreach ($cnames as $cname){
                        ?>
                            <td style="background-color: #ECEEEF;">
                                <!--<input type="number" name="marks[]" step="0.001" min="0" max="<?php echo $criteriaMaxScore[$i]; ?>" required />-->
                                <select name="marks[]" class="select custom-select" required>
                                    <?php
                                    for($j=0; $j<$scount[$i]; $j++){
                                    ?>
                                        <option value="<?php echo $sscoreMulti[$i][$j]; ?>"><?php echo $snoMulti[$i][$j]; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </td >
                    <?php
                        }  ?>
                    </tr> <?php
                    }
                }
            ?>
            </table>
            <input type="hidden" value='<?php echo $ccount; ?>' name="ccount">
            <input type="hidden" value='<?php echo $aid; ?>' name="aid">
            <?php
            foreach($cids as $cid)
            {
            echo '<input type="hidden" name="cid[]" value="'. $cid. '">';
            }
            foreach($stdids as $sid)
            {
            echo '<input type="hidden" name="studid[]" value="'. $sid. '">';
            }
            ?>
            <br />
            <input type="submit" value="Submit Result" name="submit" class="btn btn-primary">
        </form>
        <br />
        <button id="myButton2" class="btn btn-success">Export to Excel</button>
        
        <script type="text/javascript" >
            //<!-- Export html Table to xls -->
            $(document).ready(function(e){
                $("#myButton2").click(function(e){ 
                    $("#mytable").table2excel({
                        name: "file name",
                        filename: "assessment_grading",
                        fileext: ".xls"
                    });
                });
            });
            //<!-- Cache form data -->
            var value = 'assess' + <?php echo json_encode($aid); ?>; // assessment id is the form key
            //alert(value);
            $("#myForm").formcache({key:value});
            //$("#myForm").formcache();
            //$("#myForm").formcache("clear");
            //$("#myForm").formcache('removeCaches');
            //$("#myForm").formcache.clear();
        </script>
                        <?php
                    }
                }
                else{
                    echo "<font color=red>No Rubric attached to CLO!</font><br>";
                }
            }
            else{
                echo "<font color=red>No CLO belonging to Psychomotor/Affective Taxonomy Domain found!</font><br>";
            }
        }
        else{
            echo "<font color=red>No CLO has been mapped to this course!</font><br>";
        }
    }
    else{
        ?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="../teacher/teacher_courses.php">Back</a>
    <?php
    }
    echo $OUTPUT->footer();
?>
