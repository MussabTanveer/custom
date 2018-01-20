<?php
	require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Map Grading Items");
    $PAGE->set_heading("Map Grading Items");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/map_grading_item.php');
    
    echo $OUTPUT->header();

    require_login();
    
    if(isset($_GET['course']))
    {
        $course_id=$_GET['course'];
        //echo "Course ID : $course_id";

        /*// Get CLOs
        $rec=$DB->get_records_sql("SELECT * FROM mdl_competency WHERE competencyframeworkid = ? AND idnumber NOT LIKE 'PLO%' AND parentid !=0 ORDER BY idnumber", array($fw_id));
        */
        // Get Grading Items
        $rec=$DB->get_records_sql("SELECT * FROM mdl_grading_policy WHERE courseid = ? ORDER BY id", array($course_id));

        /*//Get level with its name and domain name
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
        }*/

        if($rec){
            $recQ=$DB->get_records_sql('SELECT * FROM  `mdl_quiz` WHERE course = ?', array($course_id));
            $recA=$DB->get_records_sql('SELECT * FROM `mdl_assign` WHERE course = ?', array($course_id));
            
            if($recQ || $recA){
            $i = 0;
            $activityids = array();
            ?>
            <form action="confirm_grading_item.php" method="post">
                <table class="generaltable">
                    <tr class="table-head">
                        <th> Activities </th>
                        <th> Grading Items </th>
                    </tr>
                    <?php
                    $i = 0;
                    foreach($recQ as $records)
                    {
                        $qid = $records->id;
                        $qname = $records->name;
                        array_push($activityids,"Q".$qid);
                    ?>
                                
                    <tr>
                        <td><?php echo $qname;?> </td>
                        <td>
                            <select required name="gitem[]" class="select custom-select">
                                <option value=''>Choose..</option>
                                <?php
                                foreach ($rec as $recItem) {
                                    $gid = $recItem->id;
                                    $gname = $recItem->name;
                                    ?>
                                    <option value='<?php echo $gid; ?>'><?php echo $gname; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <?php
                        $i++;
                        }
                        foreach($recA as $records)
                        {
                            $aid = $records->id;
                            $aname = $records->name;
                            array_push($activityids,"A".$aid);
                        ?>
                                    
                        <tr>
                            <td><?php echo $aname;?> </td>
                            <td>
                                <select required name="gitem[]" class="select custom-select">
                                    <option value=''>Choose..</option>
                                    <?php
                                    foreach ($rec as $recItem) {
                                        $gid = $recItem->id;
                                        $gname = $recItem->name;
                                        ?>
                                        <option value='<?php echo $gid; ?>'><?php echo $gname; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <?php
                            $i++;
                        }
                        global $SESSION;
                        $SESSION->activityids = $activityids;
                        
                        ?>
                </table>
			
                <input type="hidden" value='<?php echo $i; ?>' name="activitycount">
                <input type="hidden" value='<?php echo $course_id; ?>' name="courseid">
                <input type="submit" value="NEXT" name="submit" class="btn btn-primary">
	    	</form>

            <?php
            }
        }
        else{
            echo "<h3>Found no graded item for this course!</h3>";

        }

    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="../index.php">Back</a>
    <?php
    }
    echo $OUTPUT->footer();
?>
