<script src="../script/chart/Chart.bundle.js"></script>
<script src="../script/chart/utils.js"></script>

<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Course CLO");
    $PAGE->set_heading("Consolidated Report");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/consolidated_report.php');
    
    echo $OUTPUT->header();
    require_login();

    if(isset($_POST['view_consolidated']) && isset($_POST['courseid']) && $_POST['activityid'])
    {
        $courseid=$_POST['courseid'];
        $activities = array();
        $activities = $_POST['activityid'];

		for ($i = 0; $i < sizeof($activities); $i++) {
			$activities[$i] = substr($activities[$i],1);
           // echo $activities[$i];
		}
        //echo $courseid;
        

        $rec=$DB->get_recordset_sql(
            "SELECT cr.id, cr.course, cr.module, cr.instance, cr.cloid, cr.pass,cr.fail, c.idnumber
            FROM mdl_consolidated_report cr, mdl_competency c
            WHERE cr.cloid=c.id AND cr.course=? AND cr.instance IN (".implode(',',$activities).")
            ORDER BY cr.cloid",
            array($courseid));
        
        $cloids = array();
        $label = array();
        $pass = array();
        $fail = array();
        $names = array();
        foreach($rec as $records){
            $i = $records->cloid;
            $c = $records->idnumber;
            $p = $records->pass;
            $f = $records->fail;
            $m = $records->module;
            $in = $records->instance;
            if($m == 16){
                $recName=$DB->get_records_sql(
                    'SELECT name
                    FROM mdl_quiz
                    WHERE id = ?',
                    array($in));
                foreach($recName as $rName){
                    $name = $rName->name;
                    array_push($names,$name);
                }
            }
            else if($m == 1){
                $recName=$DB->get_records_sql(
                    'SELECT name
                    FROM mdl_assign
                    WHERE id = ?',
                    array($in));
                foreach($recName as $rName){
                    $name = $rName->name;
                    array_push($names,$name);
                }
            }
            array_push($cloids,$i);
            array_push($label,$c);
            array_push($pass,$p);
            array_push($fail,$f);
        }
        ?>
        <h3>Attempt Stats</h3>
        <table class="generaltable">
        <tr>
            <th>CLOs</th>
            <th>Attempts Given</th>
        </tr>
        <?php
        $arrlength = count($cloids);
        $label2 = array();
        $label2 = $label;
        $count=0; $one=1; $first=0; $temp=0;
        for($x=0;$x<$arrlength;$x++){
            if($one){
                $count++;
                $label2[$x]=$label2[$x]." (".$names[$x].")"." (".$count.")";
                $one=0;
            }
            else if($temp != $cloids[$x]){
                ?>
                
                <tr>
                    <td><?php echo $label[$x-1]; ?></td>
                    <td><?php echo $count; ?></td>
                </tr>
                
                <?php
                $count=0;
                $count++;
                $label2[$x]=$label2[$x]." (".$names[$x].")"." (".$count.")";
            }
            else{
                $count++;
                $label2[$x]=$label2[$x]." (".$names[$x].")"." (".$count.")";
            }
            $temp = $cloids[$x];
        }
        ?>
            <tr>
                <td><?php echo $label[$x-1]; ?></td>
                <td><?php echo $count; ?></td>
            </tr>
        </table>
        <br />
        <div id="container" style="width: 100%;">
            <canvas id="canvas"></canvas>
        </div>

        <?php
        
        echo $OUTPUT->footer();

        ?>
        <!-- Vertical Bar Chart -->
        <script>
        var color = Chart.helpers.color;
        var barChartData = {
            labels: <?php echo json_encode($label2); ?>,
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
                        text: 'CLO Attempts Bar Chart'
                    }
                }
            });

        };

        </script>
    
        <?php
    }
    else
    {
         //end:
         ?> 

        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./display_courses-3.php">Back</a>
    <?php 

        echo $OUTPUT->footer();
    }

?>