<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("My Semester Progress");
    $PAGE->set_heading("Semester Progress Report");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/student/display_semester_progress.php');
    
    require_login();
    if($SESSION->oberole != "student"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    ?>
    <script src="../script/chart/Chart.bundle.js"></script>
    <script src="../script/chart/utils.js"></script>
    <?php

    if(isset($_POST['submit']) && isset( $_POST['semesterid']))
    {
        $semester_id=$_POST['semesterid'];
        //Get student courses
        $rec=$DB->get_records_sql('SELECT c.id, c.fullname, c.shortname, c.idnumber
        
            FROM mdl_course c
        
            INNER JOIN mdl_context cx ON c.id = cx.instanceid
        
            AND cx.contextlevel = ?
        
            INNER JOIN mdl_role_assignments ra ON cx.id = ra.contextid
        
            INNER JOIN mdl_role r ON ra.roleid = r.id
        
            INNER JOIN mdl_user usr ON ra.userid = usr.id
        
            WHERE r.shortname = ?
        
            AND usr.id = ? AND c.semesterid = ?', array('50', 'student', $USER->id, $semester_id));
            
        if($rec){
            //$i=0;
            $courseids = array();
            $coursenames = array();
            $passarray = array(); $failarray = array();
            $allarray = array(); $cleararray = array();
            $tot_comp = 0;
            $tot_clear = 0;
            foreach ($rec as $records) {
                $id = $records->id;
                $fname = $records->fullname;
                $sname = $records->shortname;
                array_push($courseids,$id);
                array_push($coursenames,$sname);

                //Get all course competencies
                $recAll=$DB->get_records_sql('SELECT competencyid FROM mdl_competency_coursecomp
                WHERE courseid=? ',
                array($id));

                //Get all course competencies that user cleared
                $recClear=$DB->get_records_sql('SELECT DISTINCT cloid FROM mdl_consolidated_report_student crs
                WHERE course=? AND userid=? AND status=?',
                array($id,$USER->id,1));

                $all = count($recAll);
                array_push($allarray,$all);
                $tot_comp += $all;
                $clear = count($recClear);
                array_push($cleararray,$clear);
                $tot_clear += $clear;
                if($all == 0){
                    $pass = 0;
                    array_push($passarray,$pass);
                    array_push($failarray,100-$pass);
                }
                else{
                    $pass = number_format(($clear/$all)*100, 2);
                    array_push($passarray,$pass);
                    array_push($failarray,100-$pass);
                }
                //echo $sname."<br>";
                //echo "Total: $all<br>";
                //echo "Clear: $clear<br>";
                //echo "Pass: $pass%<br>";
                //echo "<br>";
                ?>
                <!--
                <h3><?php echo $sname ?></h3>
                <p><?php echo "You are proficient in $clear out of $all competencies in this course." ?></p>
                <div id="canvas-holder" style="width:50%; margin:0px auto;">
                    <canvas id="chart-area-<?php echo $i ?>"></canvas>
                </div>
                -->
                <?php
                //$i++;
            }
            $progress = number_format(($tot_clear/$tot_comp)*100, 2);
            ?>
            <br />
            <h3>Semester Progress</h3>
            <p><?php echo $tot_clear ?> out of <?php echo $tot_comp ?> competencies are proficient (<?php echo $progress ?>% Complete).</p>
            <progress class="progress" value="<?php echo $progress ?>" max="100"></progress>
            <br />
            <h3>Semester Courses' Progress</h3>
            <br />
            <?php
            for($i=0;$i<count($rec);$i++){
                ?>
                <h4><?php echo $coursenames[$i] ?></h4>
                <p><?php echo "You are proficient in $cleararray[$i] out of $allarray[$i] competencies in this course." ?></p>
                <div id="canvas-holder" style="width:50%; margin:0px auto;">
                    <canvas id="chart-area-<?php echo $i ?>"></canvas>
                </div>
                <?php
            }
            
            ?>
            
            <!-- Doughnut Chart -->
            <script>
            var total = <?php echo json_encode($i); ?>;
            var cnames = <?php echo json_encode($coursenames); ?>;
            var pass = <?php echo json_encode($passarray); ?>;
            var fail = <?php echo json_encode($failarray); ?>;
            //alert(total); alert(cnames);
            var config = [];
            for(var j=0; j<total; j++){
                config[j] = {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [
                                pass[j],
                                fail[j],
                            ],
                            backgroundColor: [
                                window.chartColors.green,
                                window.chartColors.red,
                            ],
                            label: 'Dataset 1'
                        }],
                        labels: [
                            "Completed",
                            "Incomplete",
                        ]
                    },
                    options: {
                        responsive: true,
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: "% Progress"
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                };
            }
            
            window.onload = function() {
                for(var k=0; k<total; k++){
                    var id = "chart-area-" + k;
                    var ctx = document.getElementById(id).getContext("2d");
                    window.myDoughnut = new Chart(ctx, config[k]);
                }
            };
            </script>
            <?php
        }
        else{
            echo "<h3>No courses found!</h3>";
        }
    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./report_student.php">Back</a>
    <?php
    }
    echo $OUTPUT->footer();
?>
