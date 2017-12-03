<?php 
    require_once('../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Domains & Levels");
    $PAGE->set_heading("Mapping of Domains & Levels");
    $PAGE->set_url($CFG->wwwroot.'/custom/display_plo_domain_clo_level.php');
    
    echo $OUTPUT->header();
    require_login();

    if((isset($_POST['submit']) && isset( $_POST['fwid'])) || (isset($SESSION->fid7) && $SESSION->fid7 != "xyz"))
    {
        if(isset($SESSION->fid7) && $SESSION->fid7 != "xyz")
        {
            $fw_id=$SESSION->fid7;
            $SESSION->fid7 = "xyz";
        }
        else
            $fw_id=$_POST['fwid'];
        //echo "FW ID : $fw_id";
    ?>

        <?php
        /*
        // Get PLO
        $recPLO=$DB->get_records_sql("SELECT * FROM mdl_competency WHERE competencyframeworkid = ? AND idnumber LIKE 'plo%' ORDER BY id", array($fw_id));
        // Get PLO CLO
        $rec2=$DB->get_recordset_sql('SELECT clo.parentid AS cparent, plo.id AS ploid, clo.idnumber AS cloidnum, clo.shortname AS cloname, plo.shortname AS ploname FROM  mdl_competency clo, mdl_competency plo WHERE plo.id=clo.parentid AND plo.parentid != 0 AND plo.competencyframeworkid = ? AND clo.competencyframeworkid = ? ORDER BY clo.idnumber', array($fw_id, $fw_id));
        */
        // Get CLO & Level
        $rec=$DB->get_recordset_sql('SELECT clo.shortname AS cname, levels.name AS lname, levels.level AS lvl FROM mdl_competency clo, mdl_taxonomy_levels levels, mdl_taxonomy_clo_level clolevel WHERE clo.id=clolevel.cloid AND levels.id=clolevel.levelid AND clolevel.frameworkid = ?', array($fw_id, $fw_id));
        // Get Domains
        $recDom=$DB->get_records_sql("SELECT * FROM mdl_taxonomy_domain ORDER BY id");
        // Get PLO Domain
        $rec2=$DB->get_recordset_sql('SELECT plo.id AS pid, plo.shortname AS pname, plodom.domainid AS domid FROM mdl_competency plo, mdl_taxonomy_domain dom, mdl_taxonomy_plo_domain plodom WHERE plo.id=plodom.ploid AND dom.id=plodom.domainid AND plodom.frameworkid = ?', array($fw_id));

        if($rec){
            ?>
            <!-- CLO-Level Mapping -->
            <br />
            <h3>Mapping of CLOs to Taxonomy Levels</h3>
            <table class="generaltable">
                <tr>
                    <th>CLOs</th>
                    <th>Level Names</th>
                    <th>Levels</th>
                </tr>
                
                <?php
                foreach ($rec as $records) {
                    $cn = $records->cname;
                    $ln = $records->lname;
                    $lvl = $records->lvl;
                    ?>
                    <tr>
                        <td><?php echo $cn ?></td>
                        <td><?php echo $ln ?></td>
                        <td><?php echo $lvl ?></td>
                    </tr>
                    <?php
                }
                ?>
                
            </table>

            <!-- PLO-Domain Mapping -->
            <br />
            <h3>Mapping of PLOs to Taxonomy Domains</h3>
            <table class="generaltable">
                <tr>
                    <th>PLOs</th>
                    <?php
                    $dids = array();
                    foreach ($recDom as $rDom) {
                        $id = $rDom->id;
                        $name = $rDom->name;
                        array_push($dids,$id);
                       ?>
                        <th><?php echo $name ?></th>
                       <?php
                    }
                    ?>
                </tr>
                
                <?php
                $arrlength = count($dids); // domain count
                $one = 0; $first = 0; $temp_plidn = ""; $ploid_temp = array(); $domids = array();
                foreach ($rec2 as $records) {
                    $pidn = $records->pid; // store new record plo id
                    //$clidn = substr($clidn,0,6);
                    if($one == 0){
                        $ploid_temp[] = $records;
                        $one++;
                    }
                    else if($pidn == $temp_plidn){ // match new plo id with last record
                        $ploid_temp[] = $records;
                    }
                    else{
                        ?>
                        <tr>
                            <?php
                            foreach($ploid_temp as $data){ // loop as many times as course clo count
                                $pn = $data->pname;
                                $did = $data->domid;
                                
                                if($first == 0){ // display plo name only once
                                    ?>
                                    <td><?php echo $pn; ?></td>
                                    <?php
                                    $first++;
                                }
                                $domids[] = $did;
                            }
                            $arrlength2 = count($domids);
                            $flag = 0;
                            for($i = 0; $i<$arrlength; $i++){
                                for($j = 0; $j<$arrlength2; $j++){
                                    if($dids[$i] == $domids[$j]){
                                        ?>
                                        <td>&#10003;</td>
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
                        unset($ploid_temp);
                        unset($domids);
                        $ploid_temp[] = $records;
                    }
                    $temp_plidn = $pidn; // store last record clo idnum
                }
                ?>
                
                        <tr>
                            <?php // now print very last course record
                            foreach($ploid_temp as $data){ // loop as many times as course clo count
                                $pn = $data->pname;
                                $did = $data->domid;
                                
                                if($first == 0){ // display plo name only once
                                    ?>
                                    <td><?php echo $pn; ?></td>
                                    <?php
                                    $first++;
                                }
                                $domids[] = $did;
                            }
                            $arrlength2 = count($domids);
                            $flag = 0;
                            for($i = 0; $i<$arrlength; $i++){
                                for($j = 0; $j<$arrlength2; $j++){
                                    if($dids[$i] == $domids[$j]){
                                        ?>
                                        <td>&#10003;</td>
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

            </table>

            <a href="./display_outcome_framework-4.php">Back</a>

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
        <a href="./display_outcome_framework-4.php">Back</a>
    <?php
        echo $OUTPUT->footer();
    }?>
