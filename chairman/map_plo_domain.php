<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("PLOs and Domains");
    $PAGE->set_heading("Map PLOs to Taxonomy Domains");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/map_plo_domain.php');
    
    echo $OUTPUT->header();
    require_login();
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
    if((isset($_POST['submit']) && isset( $_POST['fwid'])) || (isset($SESSION->fid8) && $SESSION->fid8 != "xyz"))
    {
        if(isset($SESSION->fid8) && $SESSION->fid8 != "xyz")
        {
            $fw_id=$SESSION->fid8;
            $SESSION->fid8 = "xyz";
        }
        else
            $fw_id=$_POST['fwid'];
        //echo "FW ID : $fw_id";
    ?>

        <?php
        // Get PLOs
        $rec=$DB->get_records_sql("SELECT * FROM mdl_competency WHERE competencyframeworkid = ? AND idnumber LIKE 'PLO%'  ORDER BY id", array($fw_id));

        if($rec){
            $i = 0;
            ?>
            <form action="confirm_domain_plo.php" method="post" >
            <?php
            $ploids = array();
            $table = new html_table();
            $table->head = array('PLOs','COGNITIVE', 'PSYCHOMOTOR', 'AFFECTIVE');
            foreach ($rec as $records) {
                $id = $records->id;
                $sname = $records->shortname;
                $table->data[] = array($sname, '<input type="checkbox" value="'.$i.'" name="cognitive[]">', '<input type="checkbox" value="'.$i.'" name="psychomotor[]">', '<input type="checkbox" value="'.$i.'" name="affective[]">');
                array_push($ploids, $id);
                $i++;
            }
            echo html_writer::table($table);
            global $SESSION;
            $SESSION->ploids = $ploids;
            ?>
                <input type="hidden" value='<?php echo $i; ?>' name="plocount">
                <input type="hidden" value='<?php echo $fw_id; ?>' name="fwid">
                <input type="submit" value="NEXT" name="submit" class="btn btn-primary">
	    	</form>
            
            <a href="./display_outcome_framework-2.php">Back</a>

            <?php
            echo $OUTPUT->footer();
        }
        else{
            echo "<h3>No PLOs found!</h3>";
            echo '<a href="./display_outcome_framework-2.php">Back</a>';
            echo $OUTPUT->footer();
        }

    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./display_outcome_framework-2.php">Back</a>
    <?php
        echo $OUTPUT->footer();
    }?>
