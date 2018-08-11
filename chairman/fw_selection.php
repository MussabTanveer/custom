<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("OBE Frameworks");
    $PAGE->set_heading("OBE Framework Selection");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/fw_selection.php');
    
    echo $OUTPUT->header();
    require_login();
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
    ?>
	<script src="../script/jquery/jquery-3.2.1.js"></script>
	<?php
    
	// Dispaly all frameworks
    $rec=$DB->get_records_sql('SELECT * FROM mdl_competency_framework');
	if($rec){
        ?>
        <form method="post" action="plo_selection.php" id="form_check">
        <?php
        $batchID = $_GET['id'];
        $serialno = 0;
        $table = new html_table();
        $table->head = array('S. No.', 'Framework', 'Select');
		foreach ($rec as $records) {
            $serialno++;
			$id = $records->id;
            $shortname = $records->shortname;
		    $table->data[] = array($serialno, $shortname,'<input type="radio" value="'.$id.'" name="frameworkid">');
        }
        if($serialno == 1){
            
            global $SESSION;
            $SESSION->pfid = $id;
            $SESSION->batchID = $batchID;
        
            redirect('plo_selection.php');
        }
		echo html_writer::table($table);
        ?>
        <input type="hidden" name="batchID" value="<?php echo $batchID; ?>">
        <input type='submit' value='NEXT' name='submit' class="btn btn-primary">
        </form>
        <a class="btn btn-default" style="margin-top: 40px" href="./select_batch.php">Go Back</a>
        <br />
        <p id="msg"></p>
		
        <script>
        $('#form_check').on('submit', function (e) {
            if ($("input[type=radio]:checked").length === 0) {
                e.preventDefault();
                $("#msg").html("<font color='red'>Please select a Framework!</font>");
                return false;
            }
        });
        </script>
        
        <?php
        echo $OUTPUT->footer();
		}
	else{
        echo "<h3>No Frameworks found!</h3>";
        echo $OUTPUT->footer();
    }
?>
