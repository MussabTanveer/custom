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
            ?>
            
            <br />
            <table class="generaltable">
                <?php
                for($i=0; $i<count($criteriaDesc); $i++){
                    $scaleInfo=$DB->get_records_sql('SELECT * FROM mdl_rubric_scale WHERE rubric = ? AND criterion = ?', array($rubric_id, $criteriaId[$i]));
                ?>
                <tr>
                    <th>Criterion <?php echo ($i+1)."<br>".$criteriaDesc[$i] ?></th>
                    <?php
                    foreach ($scaleInfo as $sInfo) {
                        //$id = $sInfo->id;
                        $description = $sInfo->description;
                        $score = $sInfo->score;
                        echo "<td>$description<br>Score: $score</td>";
                    }
                    ?>
                </tr>
                <?php
                }
                ?>
            </table>
            
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
