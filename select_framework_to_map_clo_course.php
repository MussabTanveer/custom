<script src="./script/jquery/jquery-3.2.1.js"></script>
<?php
	require_once('../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("OBE Frameworks");
    $PAGE->set_heading("OBE Framework Selection");
    $PAGE->set_url($CFG->wwwroot.'/custom/select_framework_to_map_clo_course.php');
    
    echo $OUTPUT->header();
    require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());
    global $SESSION;

	if((isset($_POST['submit']) && isset($_POST['courseid'])) )
    {
		$courseid=$_POST['courseid'];
		
		$SESSION->courseid = $courseid;

		$rec=$DB->get_records_sql('SELECT shortname,id from mdl_competency_framework');
		$serialno = 0;
		if($rec){
			?>
			<form method="post" action="map_clo_course.php" id="form_check">
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
				$SESSION->fid10 = $id;
			
				redirect('map_clo_course.php');
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

	}
	?>
