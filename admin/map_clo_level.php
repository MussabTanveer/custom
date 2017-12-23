<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("CLOs and Levels");
    $PAGE->set_heading("Map CLOs to Taxonomy Levels");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/admin/map_clo_level.php');
    
    echo $OUTPUT->header();
    require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());

    if((isset($_POST['submit']) && isset( $_POST['fwid'])) || (isset($SESSION->fid9) && $SESSION->fid9 != "xyz")) 
    {
        if(isset($SESSION->fid9) && $SESSION->fid9 != "xyz")
        {
            $fw_id=$SESSION->fid9;
            $SESSION->fid9 = "xyz";
        }
        else
            $fw_id=$_POST['fwid'];
        //echo "FW ID : $fw_id";
    ?>

        <?php
        // Get CLOs
        $rec=$DB->get_records_sql("SELECT * FROM mdl_competency WHERE competencyframeworkid = ? AND idnumber NOT LIKE 'PLO%' AND parentid !=0 ORDER BY idnumber", array($fw_id));

        //Get level with its name and domain name
        $recLevels=$DB->get_records_sql("SELECT txl.id, txl.name AS level_name, txl.level, txd.name AS domain_name FROM mdl_taxonomy_levels txl, mdl_taxonomy_domain txd WHERE txl.domainid=txd.id");
        $levelid = array(); $lname = array(); $dname = array();
        foreach ($recLevels as $recL) {
            $lid = $recL->id;
            $lvl = $recL->level;
            $ln = $recL->level_name;
            $dn = $recL->domain_name;
            array_push($levelid, $lid); // array of level ids
            array_push($lname, $ln); // array of level names
            array_push($dname, $dn); // array of domain names
        }

        if($rec){
            $i = 0;
            $cloids = array();
            ?>
            <form action="confirm_level_clo.php" method="post">
                <table class="generaltable">
                    <tr class="table-head">
                        <th> CLOs </th>
                        <th> Taxonomy Level </th>
                        <th> Level Name </th>
                        <th> Domain Name </th>
                    </tr>
                    <?php
                    $i = 0;
                    foreach($rec as $records)
                    {
                        $cid = $records->id;
                        $sname = $records->shortname;
                        $cidnum = $records->idnumber;
                        array_push($cloids,$cid);
                    ?>
                                
                    <tr>
                        <td><?php echo $cidnum;?> </td>
                        <td>
                            <select required onChange="dropdownTip(this.value, <?php echo $i ?>)" name="level[]" class="select custom-select">
                                <option value=''>Choose..</option>
                                <?php
                                foreach ($recLevels as $recL) {
                                    $lid = $recL->id;
                                    $lvl = $recL->level;
                                    ?>
                                    <option value='<?php echo $lid; ?>'><?php echo $lvl; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </td>
                        <td id="lname<?php echo $i ?>"></td>
                        <td id="dname<?php echo $i ?>"></td>
                    </tr>
                    <?php
                        $i++;
                        }
                        global $SESSION;
                        $SESSION->cloids = $cloids;
                        
                        ?>
                </table>
			
                <input type="hidden" value='<?php echo $i; ?>' name="clocount">
                <input type="hidden" value='<?php echo $fw_id; ?>' name="fwid">
                <input type="submit" value="NEXT" name="submit" class="btn btn-primary">
	    	</form>
            
            <a href="./display_outcome_framework-3.php">Back</a>

            <?php
            echo $OUTPUT->footer();
            ?>
            <script>
                var levelid = <?php echo json_encode($levelid); ?>;
                var lnames = <?php echo json_encode($lname); ?>;
                var dnames = <?php echo json_encode($dname); ?>;
                /*alert(closid);
                alert(plos);
                alert(peos);*/
                function dropdownTip(value,id){
                    var lname = "lname" + id;
                    var dname = "dname" + id;
                    //console.log(value);
                    //console.log(id);
                    if(value == ''){
                        document.getElementById(lname).innerHTML = "";
                        document.getElementById(dname).innerHTML = "";
                    }
                    else{
                        for(var i=0; i<levelid.length ; i++){
                            if(levelid[i] == value){
                                document.getElementById(lname).innerHTML = lnames[i];
                                document.getElementById(dname).innerHTML = dnames[i];
                                break;
                            }
                        }
                    }
                }
            </script>
            <?php
        }
        else{
            echo "<h3>No CLOs found!</h3>";
            echo '<a href="./display_outcome_framework-3.php">Back</a>';
            echo $OUTPUT->footer();
        }

    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./display_outcome_framework-3.php">Back</a>
    <?php
        echo $OUTPUT->footer();
    }?>
