<script src="../script/chart/Chart.bundle.js"></script>
<script src="../script/chart/utils.js"></script>

<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/table2excel/jquery.table2excel.min.js"></script>

<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Activity CLO");
    $PAGE->set_heading("Activity CLO Report");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/student/activity_comp_report_student.php');
    
    echo $OUTPUT->header();
    require_login();

    if(isset($_POST['submit']) && isset( $_POST['activityid']) && isset($_POST['courseid']))
    {
        $activity_id=$_POST['activityid'];
        $courseid=$_POST['courseid'];

        /******************** QUIZ ***************************/
        if(substr($activity_id,0,1) == 'Q'){
            $quiz_id = substr($activity_id,1);
            $mod = 16;
            //Get ques comp
            $recordsComp=$DB->get_records_sql("SELECT DISTINCT c.id, c.idnumber

            FROM mdl_competency c, mdl_quiz q, mdl_quiz_slots qs, mdl_question qu

            WHERE q.id=? AND q.id=qs.quizid AND qu.id=qs.questionid AND qu.competencyid = c.id
            
            ORDER BY qu.competencyid",
            
            array($quiz_id));
            
            $rec=$DB->get_recordset_sql(
                'SELECT
                qa.userid,
                u.idnumber AS std_id,
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
                qa.userid=? AND q.id=? AND qa.attempt=? AND q.id=qs.quizid AND qu.id=qs.questionid AND qu.category=qc.id AND q.id=qa.quiz AND qa.userid=u.id
                    AND qa.uniqueid=qua.questionusageid AND qu.id=qua.questionid AND qua.id=qas.questionattemptid AND qas.fraction IS NOT NULL
                GROUP BY qa.userid, qu.competencyid
                ORDER BY qa.userid, qu.competencyid',
                
            array($USER->id,$quiz_id,1));
            
            if($rec ){
                ?>
                <h3>Quiz CLO Report</h3>
                <!-- Display Students' Quiz Competency Results -->
                <table class="generaltable">
                    <tr class="table-head">
                        <th> Student ID </th>
                        <th> Student Name </th>
                        <?php
                        $temp = '';
                        $tot_comp = 0; // total comp count
                        $label = array();
                        $cloids = array();
                        foreach ($recordsComp as $recC) {
                            $compid = $recC->id;
                            $comp = $recC->idnumber;
                            array_push($cloids, $compid); // array of clo ids
                            array_push($label, $comp); // array of clo names
                            $tot_comp++;
                        ?>
                        <th> <?php echo $comp; ?> </th>
                        <?php
                        }
                        ?>
                    </tr>

                    <?php
                    $count = 0; $first = 0; $i = 0;
                    $tot_stdnt = 0; // total students count
                    $pass = array(); $fail = array();
                    
                    for($x = 0; $x < $tot_comp; $x++) { // initialize array
                        $pass[$x] = 0;
                    }

                    foreach ($rec as $records){
                        if($count === $tot_comp){ // 1 student record collected
                            $tot_stdnt++;
                            //echo $count;
                            ?>
                            <tr>
                            <?php
                            foreach($data_temp as $data){ // loop as many times as comp count
                                $uid = $data->userid;
                                $sid = $data->std_id;
                                $uname = $data->std_name;
                                $max = $data->maxmark;
                                $obt = $data->marksobtained;
                                
                                if($first === 0){ // display stud name only once
                                    ?>
                                    <td><?php echo $sid;?></td>
                                    <td><?php echo $uname;?></td>
                                    <?php
                                    $first++;
                                }
                            ?>
                            <td><?php
                                if( (($obt/$max)*100) > 50){
                                    $pass[$i]++;
                                    $i++;
                                    echo "<font color='green'>Pass</font>";
                                }
                                else{
                                    $i++;
                                    echo "<font color='red'>Fail</font>";
                                }
                                ?>
                            </td>
                    <?php
                            }
                            $count = 0;
                            $first = 0;
                            $i = 0;
                            unset($data_temp);
                            ?>
                            </tr>
                            <?php
                        }
                        //echo $count;
                        $data_temp[] = $records;
                        $count++;
                    }
                    ?>

                    <tr>
                        <?php // now print very last student record
                        $tot_stdnt++;
                        foreach($data_temp as $data){
                            $uid = $data->userid;
                            $sid = $data->std_id;
                            $uname = $data->std_name;
                            $max = $data->maxmark;
                            $obt = $data->marksobtained;
                            
                            if($first === 0){
                                ?>
                                <td><?php echo $sid;?></td>
                                <td><?php echo $uname;?></td>
                                <?php
                                $first++;
                            }
                            ?>
                        <td><?php
                            if( (($obt/$max)*100) > 50){
                                $pass[$i]++;
                                $i++;
                                echo "<font color='green'>Pass</font>";
                            }
                            else{
                                $i++;
                                echo "<font color='red'>Fail</font>";
                            }
                            ?>
                        </td>
                        <?php
                        }
                        ?>
                    </tr>
                    
                </table>
                
                <button id="myButton" class="btn btn-primary">Export to Excel</button>
                
                <div id="container" style="width: 100%;">
                    <canvas id="canvas"></canvas>
                </div>

                <?php
                //echo $tot_stdnt;
                $arrlength = count($pass);
                for($x = 0; $x < $arrlength; $x++) {
                    //echo "<br>";
                    $pass[$x] = ($pass[$x]/$tot_stdnt)*100;
                    $fail[$x] = 100 - $pass[$x];
                    //echo $pass[$x];
                    //echo $fail[$x];
                }
                $rec->close(); // Don't forget to close the recordset!
                
                $rec=$DB->get_records_sql('SELECT * FROM mdl_consolidated_report_student cr WHERE cr.course = ? AND cr.module = ? AND cr.instance = ? AND cr.userid = ?', array($courseid, $mod, $quiz_id, $USER->id));
                
                if($rec == NULL){
                    for($x=0; $x<$tot_comp; $x++){
                        if($pass[$x] == 100){
                            $status = 1;
                        }
                        else{
                            $status = 0;
                        }
                        $sql="INSERT INTO mdl_consolidated_report_student (course, userid, module, instance, cloid, status) VALUES ($courseid, $USER->id, $mod, $quiz_id, $cloids[$x], $status)";
                        $DB->execute($sql);
                    }
                }
            }
            else{
                echo "<h3>No record found!</h3>";
            }
        }

        /******************* ASSIGNMENT **********************/
        else if(substr($activity_id,0,1) == 'A'){
            $assign_id = substr($activity_id,1);
            $mod = 1;

            //Get assign comp
		    $recordsComp=$DB->get_records_sql("SELECT DISTINCT c.id, c.shortname
            
                    FROM mdl_competency c, mdl_assign a, mdl_course_modules cm, mdl_competency_modulecomp cmc
            
                    WHERE a.id=? AND cm.course=? AND cm.module=? AND a.id=cm.instance AND cm.id=cmc.cmid AND cmc.competencyid=c.id
                    
                    ORDER BY cmc.competencyid",
                    
                    array($assign_id,$courseid,$mod));
                    
            $rec=$DB->get_recordset_sql(
                'SELECT
                ag.userid,
                u.idnumber AS std_id,
                CONCAT(u.firstname, " ", u.lastname) AS std_name,
                a.grade AS maxmark,
                ag.grade AS marksobtained
                FROM
                    mdl_assign a,
                    mdl_assign_grades ag,
                    mdl_user u
                WHERE
                    ag.userid=? AND a.id=? AND ag.grade != ? AND ag.userid=u.id AND a.id=ag.assignment
                ORDER BY ag.userid',
                
            array($USER->id,$assign_id,-1));

            if($rec){
            ?>
                <h3>Assignment CLO Report</h3>
                <!-- Display Students' Assign Competency Results -->
                <table class="generaltable">
                    <tr class="table-head">
                        <th> Student ID </th>
                        <th> Student Name </th>
                        <?php
                        $tot_comp = 0; // total comp count
                        $label = array();
                        $cloids = array();
                        foreach ($recordsComp as $recC) {
                            $compid = $recC->id;
                            $comp = $recC->shortname;
                            array_push($cloids, $compid); // array of clo ids
                            array_push($label, $comp); // array of clo names
                            $tot_comp++;
                        ?>
                        <th> <?php echo $comp; ?> </th>
                        <?php
                        }
                        ?>
                    </tr>

                    <?php
                    $i=0; $tot_stdnt = 0; // total students count
                    $pass = array(); $fail = array();
                    for($x = 0; $x < $tot_comp; $x++) { // initialize array
                        $pass[$x] = 0;
                    }

                    foreach ($rec as $records){
                        $tot_stdnt++;
                        ?>
                        <tr>
                        <?php
                        $uid = $records->userid;
                        $sid = $records->std_id;
                        $uname = $records->std_name;
                        $max = $records->maxmark;
                        $obt = $records->marksobtained;
                        $result = ($obt/$max)*100;

                        ?>
                        <td><?php echo $sid;?></td>
                        <td><?php echo $uname;?></td>
                        <?php
                            for($k=0;$k<$tot_comp;$k++){
                            ?>
                            <td><?php
                                if($result > 50){
                                    $pass[$i]++;
                                    $i++;
                                    echo "<font color='green'>Pass</font>";
                                }
                                else{
                                    $i++;
                                    echo "<font color='red'>Fail</font>";
                                }
                                ?>
                            </td>
                            <?php
                            }
                        ?>
                        </tr>
                        <?php
                    
                    }
                    ?>
                    
                </table>

                <button id="myButton" class="btn btn-primary">Export to Excel</button>
                
                <div id="container" style="width: 100%;">
                    <canvas id="canvas"></canvas>
                </div>

                <?php
                $arrlength = count($pass);
                for($x = 0; $x < $arrlength; $x++) {
                    $pass[$x] = ($pass[$x]/$tot_stdnt)*100;
                    $fail[$x] = 100 - $pass[$x];
                }

                $rec->close(); // Don't forget to close the recordset!
                
                $rec=$DB->get_records_sql('SELECT * FROM mdl_consolidated_report_student cr WHERE cr.course = ? AND cr.module = ? AND cr.instance = ? AND cr.userid = ?', array($courseid, $mod, $assign_id, $USER->id));
                
                if($rec == NULL){
                    for($x=0; $x<$tot_comp; $x++){
                        if($pass[$x] == 100){
                            $status = 1;
                        }
                        else{
                            $status = 0;
                        }
                        $sql="INSERT INTO mdl_consolidated_report_student (course, userid, module, instance, cloid, status) VALUES ($courseid, $USER->id, $mod, $assign_id, $cloids[$x], $status)";
                        $DB->execute($sql);
                    }
                }
            
            }
            else{
                echo "<h3>No record found!</h3>";
            }
        
        }

        echo $OUTPUT->footer();

        ?>

        <script>
        var color = Chart.helpers.color;
        var barChartData = {
            labels: <?php echo json_encode($label); ?>,
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
                type: 'horizontalBar',
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
                        text: 'CLO Bar Chart'
                    }
                }
            });

        };

    </script>

    <script type="text/javascript" >
        $(document).ready(function(e){
            $("#myButton").click(function(e){ 
                $(".generaltable").table2excel({
                    name: "file name",
                    filename: "Report",
                    fileext: ".xls"
                });
            });
        });
    </script>

        <?php
    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./display_courses_student.php">Back</a>
    <?php 
        echo $OUTPUT->footer();
    }?>
