<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Rubric");
    $PAGE->set_heading("Rubric");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/view_rubric.php');
    
    echo $OUTPUT->header();
	require_login();
	$rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <script src="../script/table2excel/jquery.table2excel.min.js"></script>
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
    if(!empty($_GET['rubric']))
    {
        $rubric_id=$_GET['rubric'];
        // Dispaly rubric criterion and scale
        $rubricInfo=$DB->get_records_sql('SELECT * FROM mdl_rubric WHERE id= ?', array($rubric_id));

        if($rubricInfo){
            foreach ($rubricInfo as $rInfo) {
                $name = $rInfo->name;
                $description = $rInfo->description;
            }
            echo "<h3>$name <a href='edit_rubric.php?edit=rubric&rubric=$rubric_id'><i class='fa fa-pencil text-info' aria-hidden='true' title='Edit' aria-label='Edit'></i></a></h3>";
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
                    <th>Criterion <?php echo ($i+1)." <a href='edit_rubric.php?edit=criterion&rubric=$rubric_id&criterion=$criteriaId[$i]&num=".($i+1)."'><i class='fa fa-pencil text-info' aria-hidden='true' title='Edit' aria-label='Edit'></i></a> <a href='delete_rubric_criterion_scale.php?rubric=$rubric_id&criterion=$criteriaId[$i]'><i class='fa fa-trash text-danger' aria-hidden='true' title='Delete' aria-label='Delete'></i></a><br>".$criteriaDesc[$i] ?></th>
                    <?php
                    $scaleInfo=$DB->get_records_sql('SELECT * FROM mdl_rubric_scale WHERE rubric = ? AND criterion = ?', array($rubric_id, $criteriaId[$i]));
                    //$s = 1;
                    $temp=0;
                    foreach ($scaleInfo as $sInfo) {
                        $id = $sInfo->id;
                        $description = $sInfo->description;
                        $score = $sInfo->score;
                        echo "<td>$description <a href='edit_rubric.php?edit=scale&rubric=$rubric_id&scale=$id&snum=".($temp+1)."&cnum=".($i+1)."'><i class='fa fa-pencil text-info' aria-hidden='true' title='Edit' aria-label='Edit'></i></a> <a href='delete_rubric_criterion_scale.php?rubric=$rubric_id&scale=$id'><i class='fa fa-trash text-danger' aria-hidden='true' title='Delete' aria-label='Delete'></i></a><br>Score: $score</td>";
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
            <button id="myButton" class="btn btn-primary">Export to Excel</button>
            <a class="btn btn-default" href="./select_rubric.php">Go Back</a>
            
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
        }
    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./report_chairman.php">Back</a>
    <?php
    }
    echo $OUTPUT->footer();
?>
