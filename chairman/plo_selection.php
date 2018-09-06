<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("PLO Selection");
    $PAGE->set_heading("PLO Selection");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/plo_selection.php');
    
    echo $OUTPUT->header();
    require_login();
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
    
   // global $SESSION;
   // $frameworkid = $SESSION->pfid;
   // echo isset($SESSION->pfid);
    if ( (isset($_POST['batchID']) || isset($SESSION->batchID)) && (isset($_POST['frameworkid']) || (isset($SESSION->pfid)) ) )
        {

         if (isset($SESSION->batchID) && $SESSION->batchID != "xyz" )
            {
                $batchID = $SESSION->batchID;
                $SESSION->batchID = "xyz";
            }
        else
            $batchID = $_POST['batchID'];
            
        

        if (isset($SESSION->pfid) && $SESSION->pfid != "xyz" )
           {
                $frameworkid = $SESSION->pfid;
                $SESSION->pfid = "xyz";
            }
        else
            $frameworkid = $_POST['frameworkid'];

      //  echo "$batchID $frameworkid";
        $rec=$DB->get_records_sql("SELECT c.id as id ,c.shortname as shortname ,c.description as description,c.idnumber as idnumber, cast(substring(c.idnumber,5,6) as INT) as sortorder FROM mdl_competency c WHERE idnumber LIKE 'PLO-%' AND competencyframeworkid = ? ORDER BY sortorder",array($frameworkid));
        if($rec)
        {   
            ?>
            <form method="post" action="plo_report.php" id="form_check">
            <?php
            $serialno=0;
            $table = new html_table();
            $table->head = array('S. No.','PLOs Number','Name','Description','Select');
            foreach($rec as $record)
            {
                $serialno++;
                $id = $record->id;
                $shortname = $record->shortname;
                $desc = $record->description;
                $idnumber = $record->idnumber;
                $table->data[] = array($serialno,  $idnumber,$shortname,$desc, '<input type="radio" value="'.$id.'" name="plosid">');

            }
            echo html_writer::table($table);
            ?>
            <input type="hidden" name="batchID" value="<?php echo $batchID; ?>">
            <input type="hidden" name="fwid" value="<?php echo $frameworkid; ?>">
            <input type='submit' value='NEXT' name='submit' class="btn btn-primary">
            </form>
            <?php
        }
        else
            echo "<font color=red> No PLOs Found For Selected Framework.<br/> </font>";
        ?>
        <a class="btn btn-default" style="margin-top: 20px" href="./select_batch.php">Go Back</a>
        <?php
        echo  $OUTPUT->footer();

    }
    else
    {
        echo "<font color=red> Something went wrong :( </font>";
        echo $OUTPUT->footer();

    }

    ?>