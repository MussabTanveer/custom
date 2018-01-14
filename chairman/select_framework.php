<script src="../script/jquery/jquery-3.2.1.js"></script>
<?php
	require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("OBE Frameworks");
    $PAGE->set_heading("OBE Framework Selection");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/select_framework.php');
    
    echo $OUTPUT->header();
    require_login();
    
    $rec=$DB->get_records_sql('SELECT shortname,id from mdl_competency_framework');
    $serialno = 0;
	if($rec){
		?>
		<form method="post" action="map_plo_peo.php" id="form_check">
		<?php
		$table = new html_table();
		$table->head = array('S.No','Framework Name' ,'Select');
		foreach ($rec as $records){
			$serialno++;
			$shortname = $records->shortname;
			$id = $records->id;
			$table->data[] = array( $serialno,$shortname, '<input type="radio" value="'.$id.'" name="frameworkid">'); 
		}
		if($serialno == 1){
            
            global $SESSION;
            $SESSION->fid4 = $id;
        	
            redirect('./map_plo_peo.php');
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
	}
	else
	{
		echo "<h3>No framework found!</h3>";
	}
	echo $OUTPUT->footer();
	?>
