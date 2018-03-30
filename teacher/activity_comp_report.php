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
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/activity_comp_report.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

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
        
                /* Query for finding quiz ques clos' plo and plos' peos
                SELECT DISTINCT clo.id, clo.shortname AS CLO, plo.shortname AS PLO, peo.shortname AS PEO
                FROM mdl_competency clo, mdl_competency plo, mdl_competency peo, mdl_quiz q, mdl_quiz_slots qs, mdl_question qu
                WHERE q.id=2 AND q.id=qs.quizid AND qu.id=qs.questionid AND qu.competencyid = clo.id AND peo.id=plo.parentid AND plo.id=clo.parentid
                ORDER BY qu.competencyid
                */
                
            $rec=$DB->get_recordset_sql(
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
                
                array($quiz_id,1));

              $kpisArray = array();


            if($rec){
                $serialno = 0;
                ?>
                <h3>Activity CLO Report</h3>
                <!-- Display Students' Quiz Competency Results -->
                <table class="generaltable">
                    <tr class="table-head">
                        <th> S. No. </th>
                        <th> Seat No. </th>
                        <th> Student Name </th>
                        <?php
                        $tot_comp = 0; // total comp count
                        $label = array();
                        $cloids = array();
                        foreach ($recordsComp as $recC) {
                            $compid = $recC->id;
                            $comp = $recC->idnumber;
                            array_push($cloids, $compid); // array of clo ids
                            array_push($label, $comp); // array of clo idnumbers
                            $tot_comp++;
                        ?>
                        <th> <?php echo $comp; ?> </th>
                        <?php
                        }
                         
                        ?>
                    </tr>

                    <?php


                foreach ($cloids as $id) {
            
            
                    $kpis=$DB->get_records_sql("SELECT kpi FROM mdl_clo_kpi
                    WHERE cloid = ? ORDER BY cloid",array($id));

                    if($kpis)
                    {

                        foreach($kpis as $kp)
                        {
                            $kpi = $kp->kpi;
                            array_push($kpisArray, $kpi);
                        }
                     }
                
                  }
                 /*  var_dump($kpisArray);echo "<br>";
                  var_dump($cloids);echo "<br>";
                  var_dump($label);*/

                  $kpiIndex=0;


                    $count = 0; $first = 0; $i = 0;
                    $tot_stdnt = 0; // total students count
                    $pass = array(); $fail = array();
                    
                    for($x = 0; $x < $tot_comp; $x++) { // initialize array
                        $pass[$x] = 0;
                    }

                    foreach ($rec as $records){
                         $kpiIndex=0;
                        if($count === $tot_comp){ // 1 student record collected
                            $tot_stdnt++;
                            //echo $count;
                            ?>
                            <tr>
                            <?php
                            foreach($data_temp as $data){ // loop as many times as comp count
                                $uid = $data->userid;
                                $sid = $data->std_id;
                                $seat = $data->seat_no;
                                $uname = $data->std_name;
                                $max = $data->maxmark;
                                $obt = $data->marksobtained;
                                
                                if($first === 0){ // display stud no & name only once
                                    $serialno++;
                                    ?>
                                    <td><?php echo $serialno;?></td>
                                    <td><?php echo $seat;?></td>
                                    <td><?php echo $uname;?></td>
                                    <?php
                                    $first++;
                                }
                    ?>
                            <td><?php
                                //echo "Obt =$obt max= $max";
                              //  echo "<br>";
                              //  echo "$kpisArray[$kpiIndex]";
                                $kpiToPass = $kpisArray[$kpiIndex];
                                $kpiIndex++;
                              //  echo "$kpiToPass<br>";

                                if( (($obt/$max)*100) >= $kpiToPass){
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
                      $kpiIndex=0;
                    ?>

                    <tr>
                        <?php // now print very last student record
                        $tot_stdnt++;
                        foreach($data_temp as $data){
                            $uid = $data->userid;
                            $sid = $data->std_id;
                            $seat = $data->seat_no;
                            $uname = $data->std_name;
                            $max = $data->maxmark;
                            $obt = $data->marksobtained;
                            
                            if($first === 0){
                                $serialno++;
                                ?>
                                <td><?php echo $serialno;?></td>
                                <td><?php echo $seat;?></td>
                                <td><?php echo $uname;?></td>
                                <?php
                                $first++;
                            }
                            ?>
                        <td><?php
                          //  echo "Obt =$obt max= $max";
                         //   echo "<br>";
                              //  echo "$kpisArray[$kpiIndex]";
                                $kpiToPass = $kpisArray[$kpiIndex];
                                $kpiIndex++;
                              //  echo "$kpiToPass<br>";

                            if( (($obt/$max)*100) >= $kpiToPass){
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
                $a_id = substr($activity_id,1);
                $rec=$DB->get_records_sql('SELECT * FROM mdl_consolidated_report cr WHERE cr.course = ? AND cr.module = ? AND cr.instance = ? AND cr.form = ?', array($courseid, $mod, $a_id,"online"));
                
                if($rec == NULL){
                    for($x=0; $x<$tot_comp; $x++){
                        $sql="INSERT INTO mdl_consolidated_report (course, module, instance, cloid, pass, fail,form) VALUES ($courseid, $mod, $a_id, $cloids[$x], $pass[$x], $fail[$x],'online')";
                        $DB->execute($sql);
                    }
                }
                
            }
            else{
                echo "<h3>No students have attempted the activity!</h3>";
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
                u.username AS seat_no,
                CONCAT(u.firstname, " ", u.lastname) AS std_name,
                a.grade AS maxmark,
                ag.grade AS marksobtained
                FROM
                    mdl_assign a,
                    mdl_assign_grades ag,
                    mdl_user u
                WHERE
                    a.id=? AND ag.userid=u.id AND ag.grade != ? AND a.id=ag.assignment
                ORDER BY ag.userid',
                
            array($assign_id,-1));
              $kpisArray = array();

            if($rec){
                $serialno = 0;
            ?>
                <h3>Assignment CLO Report</h3>
                <!-- Display Students' Assign Competency Results -->
                <table class="generaltable">
                    <tr class="table-head">
                        <th> S. No. </th>
                        <th> Seat No. </th>
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

                        //var_dump($cloids);
                      foreach ($cloids as $id) {
            
            
                    $kpis=$DB->get_records_sql("SELECT kpi FROM mdl_clo_kpi
                    WHERE cloid = ? ORDER BY cloid",array($id));

                    if($kpis)
                    {

                        foreach($kpis as $kp)
                        {
                            $kpi = $kp->kpi;
                            array_push($kpisArray, $kpi);
                        }
                     }
                
                  }
                  var_dump($kpisArray);echo "<br>";
                  /*var_dump($cloids);echo "<br>";
                  var_dump($label);*/

                    $i=0; $tot_stdnt = 0; // total students count
                    $pass = array(); $fail = array();
                       $kpiIndex = 0;
                    for($x = 0; $x < $tot_comp; $x++) { // initialize array
                        $pass[$x] = 0;
                    }

                    foreach ($rec as $records){
                        $serialno++;
                        $tot_stdnt++;
                        ?>
                        <tr>
                        <?php
                        $uid = $records->userid;
                        $sid = $records->std_id;
                        $seat = $records->seat_no;
                        $uname = $records->std_name;
                        $max = $records->maxmark;
                        $obt = $records->marksobtained;
                        $result = ($obt/$max)*100;

                        ?>
                        <td><?php echo $serialno;?></td>
                        <td><?php echo $seat;?></td>
                        <td><?php echo $uname;?></td>
                        <?php
                            for($k=0;$k<$tot_comp;$k++){
                            ?>
                            <td><?php
                            // echo "Obt =$obt max= $max";
                              //  echo "<br>";
                                //  echo "$pass[$i] at i = $i<br>";
                           // echo "<br>";
                            $kpiToPass = $kpisArray[$kpiIndex];
                             //  $kpiIndex++;
                               // echo "$kpiToPass<br>";

                                if($result >= $kpiToPass ){
                                    $pass[$i]++;
                                   // $i++;
                                    echo "<font color='green'>Pass</font>";
                                }
                                else{
                                   // $i++;
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
                $a_id = substr($activity_id,1);
                $rec=$DB->get_records_sql('SELECT * FROM mdl_consolidated_report cr WHERE cr.course = ? AND cr.module = ? AND cr.instance = ? AND cr.form = ?', array($courseid, $mod, $a_id,'online'));
                
                if($rec == NULL){
                    for($x=0; $x<$tot_comp; $x++){
                        $sql="INSERT INTO mdl_consolidated_report (course, module, instance, cloid, pass, fail,form) VALUES ($courseid, $mod, $a_id, $cloids[$x], $pass[$x], $fail[$x],'online')";
                        $DB->execute($sql);
                    }
                }
            
            }
            else{
                echo "<h3>No students have attempted the activity!</h3>";
            }
        
        }


        echo $OUTPUT->footer();

        ?>
        <!-- Vertical Bar Chart -->
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
    <!-- Export html Table to xls -->
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
        <a href="./teacher_courses.php">Back</a>
    <?php 
        echo $OUTPUT->footer();
    }?>
