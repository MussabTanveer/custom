<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Objectives & Outcomes");
    $PAGE->set_heading("Mapping of Objectives & Outcomes");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/display_peo_plo_clo.php');
    
    echo $OUTPUT->header();
    require_login();
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <script src="../script/table2excel/jquery.table2excel.min.js"></script>
    <?php

    if((isset($_POST['submit']) && isset( $_POST['fwid'])) || (isset($SESSION->fid6) && $SESSION->fid6 != "xyz"))
    {
        if(isset($SESSION->fid6) && $SESSION->fid6 != "xyz")
        {
            $fw_id=$SESSION->fid6;
            $SESSION->fid6 = "xyz";
        }
        else
            $fw_id=$_POST['fwid'];
        //echo "FW ID : $fw_id";
    ?>

        <?php
        // Get PEO
        $recPEO=$DB->get_records_sql('SELECT * FROM mdl_competency WHERE competencyframeworkid = ? AND parentid = 0 ORDER BY id', array($fw_id));
        // Get PLO PEO
        $rec=$DB->get_recordset_sql('SELECT plo.parentid AS pparent, plo.idnumber AS pidn, peo.id AS peoid, plo.shortname AS ploname, peo.shortname AS peoname FROM  mdl_competency plo, mdl_competency peo WHERE peo.id=plo.parentid AND peo.parentid = 0 AND peo.competencyframeworkid = ? AND plo.competencyframeworkid = ? ORDER BY plo.id', array($fw_id, $fw_id));
        // Get PLO
        $recPLO=$DB->get_records_sql("SELECT * FROM mdl_competency WHERE competencyframeworkid = ? AND idnumber LIKE 'plo%' ORDER BY id", array($fw_id));
        // Get PLO CLO
        $rec2=$DB->get_recordset_sql('SELECT clo.parentid AS cparent, plo.id AS ploid, clo.idnumber AS cloidnum, clo.shortname AS cloname, plo.shortname AS ploname FROM  mdl_competency clo, mdl_competency plo WHERE plo.id=clo.parentid AND plo.parentid != 0 AND plo.competencyframeworkid = ? AND clo.competencyframeworkid = ? ORDER BY clo.idnumber', array($fw_id, $fw_id));
        // use ORDER BY RAND() for random ordering of records

        if($recPEO){
            ?>
            <!-- PEO-PLO Mapping -->
            <br />
            <h3>Mapping of PLOs to PEOs</h3>
            <table class="generaltable" id="peo_plo">
                <tr>
                    <th>PLOS</th>
                    <?php
                    $peoids = array();
                    foreach ($recPEO as $rPEO) {
                        $id = $rPEO->id;
                        $idnum = $rPEO->idnumber;
                        array_push($peoids,$id);
                       ?>
                        <th><?php echo $idnum ?></th>
                       <?php
                    }
                    ?>
                </tr>
                
                <?php
                $arrlength = count($peoids);
                foreach ($rec as $records) {
                    $pln = $records->ploname;
                    $pidnum = $records->pidn;
                    $plparentid = $records->pparent;
                    $pen = $records->peoname;
                    ?>
                    <tr>
                        <td><?php echo "$pidnum: $pln" ?></td>
                        <?php
                        for($x = 0; $x<$arrlength; $x++){
                            if($peoids[$x] == $plparentid){
                                ?>
                                <td>&#10005;</td>
                                <?php
                            }
                            else{
                                ?>
                                <td></td>
                                <?php
                            }
                        }
                        ?>
                    </tr>
                    <?php
                }
                ?>
                
            </table>

            <button id="myButton" class="btn btn-primary">Export to Excel</button><br />

            <!-- Export html Table to xls -->
            <script type="text/javascript" >
                $(document).ready(function(e){
                    $("#myButton").click(function(e){ 
                        $("#peo_plo").table2excel({
                            name: "file name",
                            filename: "peo-plo",
                            fileext: ".xls"
                        });
                    });
                });
            </script>

            <!-- PLO-CLO Mapping -->
            <br />
            <h3>Mapping of Courses to PLOs</h3>
            <table class="generaltable" id="plo_clo">
                <tr>
                    <th>Courses</th>
                    <?php
                    $ploids = array();
                    foreach ($recPLO as $rPLO) {
                        $id = $rPLO->id;
                        $idnum = $rPLO->idnumber;
                        array_push($ploids,$id);
                       ?>
                        <th><?php echo $idnum ?></th>
                       <?php
                    }
                    ?>
                </tr>
                
                <?php
                $arrlength = count($ploids); // plo count
                $counter = array(); // arr to find occurence of plo in courses
                for ($i = 0; $i < $arrlength; $i++) {
                    $counter[$i] = 0;
                }
                $one = 0; $first = 0; $temp_clidn = ""; $cloid_temp = array(); $clopids = array();
                foreach ($rec2 as $records) {
                    $clidn = $records->cloidnum; // store new record clo idnum
                    $clidn = substr($clidn,0,6);
                    if($one == 0){
                        $cloid_temp[] = $records;
                        $one++;
                    }
                    else if($clidn == $temp_clidn){ // match new clo idnum with last record clo idnum
                        $cloid_temp[] = $records;
                    }
                    else{
                        ?>
                        <tr>
                            <?php
                            foreach($cloid_temp as $data){ // loop as many times as course clo count
                                $clidn2 = $data->cloidnum;
                                $clidn2 = substr($clidn2,0,6);
                                //$cln = $data->cloname;
                                $clparentid = $data->cparent;
                                //$pen = $data->ploname;
                                
                                if($first == 0){ // display course code only once
                                    ?>
                                    <td><?php echo $clidn2; ?></td>
                                    <?php
                                    $first++;
                                }
                                $clopids[] = $clparentid;
                            }
                            $arrlength2 = count($clopids);
                            $flag = 0;
                            for($i = 0; $i<$arrlength; $i++){
                                for($j = 0; $j<$arrlength2; $j++){
                                    if($ploids[$i] == $clopids[$j]){
                                        $counter[$i]++;
                                        ?>
                                        <td>&#10005;</td>
                                        <?php
                                        $flag = 1;
                                        break;
                                    }
                                }
                                if($flag == 0){
                                    ?>
                                    <td></td>
                                    <?php
                                }
                                else{
                                    $flag = 0;
                                }
                            }
                            ?>
                        </tr>
                        <?php
                        $first = 0;
                        unset($cloid_temp);
                        unset($clopids);
                        $cloid_temp[] = $records;
                    }
                    $temp_clidn = $clidn; // store last record clo idnum
                }
                ?>
                
                        <tr>
                            <?php // now print very last course record
                            foreach($cloid_temp as $data){ // loop as many times as course clo count
                                $clidn2 = $data->cloidnum;
                                $clidn2 = substr($clidn2,0,6);
                                //$cln = $data->cloname;
                                $clparentid = $data->cparent;
                                //$pen = $data->ploname;
                                
                                if($first == 0){ // display course code only once
                                    ?>
                                    <td><?php echo $clidn2; ?></td>
                                    <?php
                                    $first++;
                                }
                                $clopids[] = $clparentid;
                            }
                            $arrlength2 = count($clopids);
                            $flag = 0;
                            for($i = 0; $i<$arrlength; $i++){
                                for($j = 0; $j<$arrlength2; $j++){
                                    if($ploids[$i] == $clopids[$j]){
                                        $counter[$i]++;
                                        ?>
                                        <td>&#10005;</td>
                                        <?php
                                        $flag = 1;
                                        break;
                                    }
                                }
                                if($flag == 0){
                                    ?>
                                    <td></td>
                                    <?php
                                }
                                else{
                                    $flag = 0;
                                }
                            }
                            ?>
                        </tr>
                        <tr>
                            <th>Count:</th>
                            <?php
                            for($a=0;$a<$arrlength;$a++){
                            ?>
                                <td><?php echo $counter[$a] ?></td>
                            <?php
                            }
                            ?>
                        </tr>

            </table>

            <button id="myButton2" class="btn btn-primary">Export to Excel</button><br /><br />

            <!-- Export html Table to xls -->
            <script type="text/javascript" >
                $(document).ready(function(e){
                    $("#myButton2").click(function(e){ 
                        $("#plo_clo").table2excel({
                            name: "file name",
                            filename: "plo-courses",
                            fileext: ".xls"
                        });
                    });
                });
            </script>

            <?php require '../templates/print_template.html'; ?>
            <a class="btn btn-default" href="./display_outcome_framework.php">Go Back</a>
            <br/>
            <br/>
             <a class="btn btn-default" href="./report_chairman.php">Go Back to Chairman Reports & Forms</a>

            <?php
            echo $OUTPUT->footer();
        }
        else{
            echo "<h3>No mapping found!</h3>";
            echo $OUTPUT->footer();
        }

    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./display_outcome_framework.php">Back</a>
    <?php
        echo $OUTPUT->footer();
    }?>
