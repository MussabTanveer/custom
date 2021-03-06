<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Add OBE Framework");
    $PAGE->set_heading("OBE Frameworks");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/add_framework.php');
    
    echo $OUTPUT->header();
	require_login();
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
?>
<script src="../script/sweet-alert/sweetalert.min.js"></script>
<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/validation/jquery.validate.js"></script>
<style>
	label.error {
		color: red;
	}
</style>
<?php
	if(isset($_POST['save']) || isset($_POST['return'])){
		$shortname=$_POST['shortname'];
		$description=$_POST['description'];
		$idnumber=$_POST['idnumber']; $idnumber=strtoupper($idnumber);
		$time = time();
		
		if(empty($shortname) || empty($idnumber) || strlen($shortname)> '30' || strlen($idnumber)>'20' )
		{
			//echo "IN IF";
			if(empty($shortname))
			{
				$msg1="<font color='red'>-Please enter framework name</font>";
			}
			if(empty($idnumber))
			{
				$msg2="<font color='red'>-Please enter ID number</font>";
			}
			if(strlen($shortname)> '30')
			{
				$msg1="<font color='red'>-Length of the Name should be less than 30</font>";
			}
			if(strlen($idnumber)>'20' )
			{
				$msg2="<font color='red'>-Length of the ID Number should be less than 20</font>";
			}
		}
		
		else{
			//echo $shortname;
			//echo $description;
			//echo $idnumber;
			$check=$DB->get_records_sql('SELECT * from mdl_competency_framework WHERE idnumber=?', array($idnumber));
			if(count($check)){
				$msg2="<font color='red'>-Please enter UNIQUE ID number</font>";
			}
			else{
				/*$sql="INSERT INTO mdl_competency_framework (shortname, description, idnumber, contextid, descriptionformat, scaleid, scaleconfiguration, taxonomies, timecreated, timemodified, usermodified) 
				VALUES ('$shortname', '$description', '$idnumber', 1, 1, 2, '[{\"scaleid\":\"2\"},{\"id\":1,\"scaledefault\":1,\"proficient\":1},{\"id\":2,\"scaledefault\":0,\"proficient\":1}]', 'outcome,outcome,outcome,outcome', '$time', '$time', $USER->id)";
				$DB->execute($sql);*/

				$record = new stdClass();
				$record->shortname = $shortname;
				$record->description = $description;
				$record->idnumber = $idnumber;
				$record->contextid = 1;
				$record->descriptionformat = 1;
				$record->scaleid = 2;
				$record->scaleconfiguration = '[{\"scaleid\":\"2\"},{\"id\":1,\"scaledefault\":1,\"proficient\":1},{\"id\":2,\"scaledefault\":0,\"proficient\":1}]';
				$record->taxonomies = 'outcome,outcome,outcome,outcome';
				$record->timecreated = $time;
				$record->timemodified = $time;
				$record->usermodified = $USER->id;
				
				$DB->insert_record('competency_framework', $record);

				$msg3 = "<font color='green'><b>OBE Framework successfully added!</b></font><br />";
				
				if(isset($_POST['return'])){
					$redirect_page1='./report_chairman.php';
					redirect($redirect_page1);
				}
			}
		}
	}
	
	//delete code starts from here
	elseif(isset($_GET['delete'])){
		$id_d=$_GET['delete'];
		$check=$DB->get_records_sql('SELECT * from mdl_competency where competencyframeworkid=?',array($id_d));
		if($check){
			$delmsg = "<font color='red'><b>The OBE framework cannot be deleted! Remove the mapping before framework deletion.</b></font><br />";
			?>
			<script>
			swal("Alert", "The OBE framework cannot be deleted! Remove the mapping before framework deletion.", "info");
			</script>
			<?php
		}
		else{
			$sql_delete="DELETE from mdl_competency_framework where id=$id_d";
			$DB->execute($sql_delete);
			$delmsg = "<font color='green'><b>OBE Framework has been deleted!</b></font><br />";
			?>
			<script>
			swal("OBE Framework has been deleted!", {
					icon: "success",
					});
			</script>
			<?php
		}
	}
	// del code ends

	$rec=$DB->get_records_sql('SELECT id, shortname, idnumber from mdl_competency_framework');
	
	if($rec){
		$i = 1;
		echo "<h3>Already Present Frameworks</h3>";
		foreach ($rec as $records){
			$shortname1 = $records->shortname;
			$idnumber1 = $records->idnumber;
			$id=$records->id;
			echo "<div class='row'>
					<div class='col-md-3 col-sm-4 col-xs-8'>$i. $shortname1 ($idnumber1)</div>
					<div class='col-md-9 col-sm-8 col-xs-4'>
						<a href='edit_framework.php?edit=$id' title='Edit'><i class='icon fa fa-pencil text-info' aria-hidden='true' title='Edit' aria-label='Edit'></i></a>
						<a href='add_framework.php?delete=$id' onClick=\"return confirm('Delete OBE framework?')\" title='Delete'><i class='icon fa fa-trash text-danger' aria-hidden='true' title='Delete' aria-label='Delete'></i></a>
					</div>
				  </div>"; //link to edit_framework.php and delete
			$i++;
		}
	}
	
	if(isset($msg3)){
		echo $msg3;
	}
	/*
	if(isset($delmsg)){
		echo $delmsg;
	}
	*/
	
	?>
	<br />
	<h3>Add New Framework</h3>
	<form method='post' action="" class="mform" id="fwForm">
	<div class="form-group row fitem">
			<div class="col-md-3">
				<span class="pull-xs-right text-nowrap">
					<abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
				</span>
				<label class="col-form-label d-inline" for="id_idnumber">
					ID number
				</label>
			</div>
			<div class="col-md-9 form-inline felement" data-fieldtype="text">
				<input type="text"
						class="form-control "
						name="idnumber"
						id="id_idnumber"
						size=""
						required
						maxlength="20" type="text" >
				<div class="form-control-feedback" id="id_error_idnumber">
				<?php
				if(isset($msg2)){
					echo $msg2;
				}
				?>
				</div>
			</div>
		</div>
		
		<div class="form-group row fitem ">
			<div class="col-md-3">
				<span class="pull-xs-right text-nowrap">
					<abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
				</span>
				<label class="col-form-label d-inline" for="id_shortname">
					Name
				</label>
			</div>
			<div class="col-md-9 form-inline felement" data-fieldtype="text">
				<input type="text"
						class="form-control "
						name="shortname"
						id="id_shortname"
						size=""
						required
						maxlength="30" type="text" >
				<div class="form-control-feedback" id="id_error_shortname">
				<?php
				if(isset($msg1)){
					echo $msg1;
				}
				?>
				</div>
			</div>
		</div>

		<div class="form-group row fitem">
			<div class="col-md-3">
				<span class="pull-xs-right text-nowrap">
				</span>
				<label class="col-form-label d-inline" for="id_description">
					Description
				</label>
			</div>
			<div class="col-md-9 form-inline felement" data-fieldtype="editor">
				<div>
					<div>
						<textarea id="id_description" name="description" class="form-control" rows="4" cols="80" spellcheck="true" ></textarea>
					</div>
				</div>
				<div class="form-control-feedback" id="id_error_description"  style="display: none;">
				</div>
			</div>
		</div>
		
		<input class="btn btn-info" type="submit" name="save" value="Save and continue"/>
        <input class="btn btn-info" type="submit" name="return" value="Save and return"/>
		<a class="btn btn-default" type="submit" href="./report_chairman.php">Cancel</a>

	</form>
	<?php
		if((isset($_POST['save']) || isset($_POST['return'])) && !isset($msg3)){
		?>
		<script>
			document.getElementById("id_shortname").value = <?php echo json_encode($shortname); ?>;
			document.getElementById("id_description").value = <?php echo json_encode($description); ?>;
			document.getElementById("id_idnumber").value = <?php echo json_encode($idnumber); ?>;
		</script>
		<?php
		}
		?>
	<br />
	<div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>
	
	<script>
		//form validation
		$(document).ready(function () {
			$('#fwForm').validate({ // initialize the plugin
				rules: {
					"idnumber": {
						required: true,
						minlength: 1,
						maxlength: 20
					},
					"shortname": {
						required: true,
						minlength: 1,
						maxlength: 30
					}
				},
				messages: {
					"idnumber": {
						required: "Please enter ID number."
					},
					"shortname": {
						required: "Please enter Name."
					}
				}
			});
		});
	</script>
	
	<?php
        echo $OUTPUT->footer();
    ?>
