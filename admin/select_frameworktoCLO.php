<script src="../script/jquery/jquery-3.2.1.js"></script>
<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("OBE Frameworks");
    $PAGE->set_heading("OBE Framework Selection");
    $PAGE->set_url($CFG->wwwroot.'/custom/select_frameworktoCLO.php');
    
    echo $OUTPUT->header();
    require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());
	
	// Dispaly all frameworks
    $rec=$DB->get_records_sql('SELECT * FROM mdl_competency_framework');
	if($rec){
        ?>
        <form method="post" action="add_clo.php" id="form_check">
        <?php
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
            $SESSION->fid3 = $id;
        
            redirect('add_clo.php');
        }
		echo html_writer::table($table);
        ?>
        <input type='submit' value='NEXT' name='submit' class="btn btn-primary">
        </form>
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
