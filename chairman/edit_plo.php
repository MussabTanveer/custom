<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/validation/jquery.validate.js"></script>
<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Edit OBE PLOs");
    $PAGE->set_heading("Edit PLO");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/edit_plo.php');
    
    echo $OUTPUT->header();
	require_login();	
	$rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
	$rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
?>
<style>
	input[type='number'] {
		-moz-appearance:textfield;
	}
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
	}
	label.error {
		color: red;
	}
</style>
<?php
	if(!empty($_GET['edit']) && !empty($_GET['fwid']))
	{
		$id=$_GET['edit'];
		$fw_id=$_GET['fwid'];
		
		if(isset($_POST['save']))
		{
			$shortname=$_POST['shortname'];
			$description=$_POST['description'];
			$idnumber=$_POST['idnumber']; $idnumber=strtoupper($idnumber);
			$cpkpi=$_POST["kpi_cohort_programme"];
			//$cckpi=$_POST["kpi_cohort_course"];
			$iskpi=$_POST["kpi_individual_student"];
			$peo=$_POST['peo'];
			$time = time();

			if(empty($shortname) || empty($idnumber) || strlen($shortname)> '30' || strlen($idnumber)>'10' || empty($cpkpi) || empty($iskpi) || is_null($peo) || $peo === NULL || empty($peo))
			{
				if(empty($shortname))
				{
					$msg1="<font color='red'>-Please enter PLO name</font>";
				}
				if(empty($idnumber))
				{
					$msg2="<font color='red'>-Please enter ID number</font>";
				}
				if(strlen($shortname)> '30')
				{
					$msg1="<font color='red'>-Length of the Name should be less than 30</font>";
				}
				if(strlen($idnumber)>'10' )
				{
					$msg2="<font color='red'>-Length of the ID Number should be less than 10</font>";
				}
				if(empty($peo) || is_null($peo))
				{
					$msg4="<font color='red'>-Please select PEO</font>";
				}
				if(empty($cpkpi))
				{
					$msg5="<font color='red'>-Please enter PLO Cohort Programme KPI</font>";
				}
				
				if(empty($iskpi))
				{
					$msg7="<font color='red'>-Please enter PLO Individual Student KPI</font>";
				}
			}
			elseif(substr($idnumber,0,4) != 'PLO-')
			{
				$msg2="<font color='red'>-The ID number must start with PLO-</font>";
			}
			else{
		
				$check=$DB->get_records_sql('SELECT * from mdl_competency WHERE idnumber=? AND competencyframeworkid=? AND id!=?', array($idnumber,$fw_id,$id));
				if(count($check)){
					$msg2="<font color='red'>-Please enter UNIQUE ID number</font>";
				}
				else{
					try {
						$transaction = $DB->start_delegated_transaction();
						
						$sql_update1="UPDATE mdl_competency SET shortname=?,description=?,idnumber=?, parentid=?, timemodified=?, usermodified=? WHERE id=?";
						$DB->execute($sql_update1, array($shortname, $description, $idnumber, $peo, $time, $USER->id, $id));

						$sql_update2="UPDATE mdl_plo_kpi_cohort_programme SET kpi=? WHERE ploid=?";
						$DB->execute($sql_update2, array($cpkpi, $id));

					

						$sql_update4="UPDATE mdl_plo_kpi_individual_student SET kpi=? WHERE ploid=?";
						$DB->execute($sql_update4, array($iskpi, $id));
						
						$transaction->allow_commit();

						$msg3 = "<font color='green'><b>PLO successfully updated!</b></font><br />";
					} catch(Exception $e) {
						$transaction->rollback($e);
						$msg3 = "<font color='red'>PLO failed to edit!</font>";
					}
				}
			}
		}
		
		if(isset($msg3)){
			echo $msg3;
			goto label;
		}

		$peos=$DB->get_records_sql('SELECT * FROM `mdl_competency` 
		WHERE competencyframeworkid = ?
		AND parentid = 0 ',
		array($fw_id));

		$peoNameArray=array();
		$peoIdArray=array();

		foreach ($peos as $p) {
			$id =  $p->id;
			$name = $p->shortname;
			//$idnumpeo =  $p->idnumber;
			array_push($peoNameArray,$name);
			array_push($peoIdArray,$id);
		}
		
	?>
	
	<br />
	<h3>Edit PLO</h3>
	<form method='post' action="" class="mform" id="ploForm">
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
						pattern="[p/P][l/L][o/O]-[0-9]{1,}"
						title="eg. PLO-1"
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

		<div class="form-group row fitem ">
			<div class="col-md-3">
				<span class="pull-xs-right text-nowrap">
					<abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
					<a class="btn btn-link p-a-0" role="button" data-container="body" data-toggle="popover" data-placement="right"
					data-content="&lt;div class=&quot;no-overflow&quot;&gt;&lt;p&gt;Cohort (mapping of PLOs to Programme) – At least 50% of the mapped courses should be attaining PLO &lt;/p&gt;&lt;/div&gt; "
					data-html="true" tabindex="0" data-trigger="focus">
					<i class="icon fa fa-question-circle text-info fa-fw " aria-hidden="true" title="Help with Passing Percentage" aria-label="Help with Passing Percentage"></i>
					</a>
				</span>
				<label class="col-form-label d-inline" for="id_kpi_cohort_programme">
					Passing Percentage Cohort (mapping of PLOs to Programme)
				</label>
			</div>
			<div class="col-md-9 form-inline felement" data-fieldtype="number">
				<span class="input-group-addon" style="display: inline;"><i class="fa fa-percent"></i></span>
				<input type="number"
						class="form-control"
						name="kpi_cohort_programme"
						id="id_kpi_cohort_programme"
						size=""
						required
						placeholder="eg. 50"
						maxlength="10"
						step="0.001"
						min="0" max="100"
						value="50">
				<div class="form-control-feedback" id="id_error_kpi_cohort_programme">
				<?php
				if(isset($msg5)){
					echo $msg5;
				}
				?>
				</div>
			</div>
		</div>
<!--
		<div class="form-group row fitem ">
			<div class="col-md-3">
				<span class="pull-xs-right text-nowrap">
					<abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
					<a class="btn btn-link p-a-0" role="button" data-container="body" data-toggle="popover" data-placement="right"
					data-content="&lt;div class=&quot;no-overflow&quot;&gt;&lt;p&gt;Cohort (mapping of a PLO to a Course) – At least 50% of the students in a mapped course should attain PLO &lt;/p&gt;&lt;/div&gt; "
					data-html="true" tabindex="0" data-trigger="focus">
					<i class="icon fa fa-question-circle text-info fa-fw " aria-hidden="true" title="Help with Passing Percentage" aria-label="Help with Passing Percentage"></i>
					</a>
				</span>
				<label class="col-form-label d-inline" for="id_kpi_cohort_course">
					Passing Percentage Cohort (mapping of a PLO to a Course)
				</label>
			</div>
			<div class="col-md-9 form-inline felement" data-fieldtype="number">
				<span class="input-group-addon" style="display: inline;"><i class="fa fa-percent"></i></span>
				<input type="number"
						class="form-control"
						name="kpi_cohort_course"
						id="id_kpi_cohort_course"
						size=""
						required
						placeholder="eg. 50"
						maxlength="10"
						step="0.001"
						min="0" max="100"
						value="50">
				<div class="form-control-feedback" id="id_error_kpi_cohort_course">
				<?php
				if(isset($msg6)){
					echo $msg6;
				}
				?>
				</div>
			</div>
		</div>
-->
		<div class="form-group row fitem ">
			<div class="col-md-3">
				<span class="pull-xs-right text-nowrap">
					<abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
					<a class="btn btn-link p-a-0" role="button" data-container="body" data-toggle="popover" data-placement="right"
					data-content="&lt;div class=&quot;no-overflow&quot;&gt;&lt;p&gt;Individual (mapping of a PLO to a student) – All CLOs mapped to a PLO in a course have been attained&lt;/p&gt;&lt;/div&gt; "
					data-html="true" tabindex="0" data-trigger="focus">
					<i class="icon fa fa-question-circle text-info fa-fw " aria-hidden="true" title="Help with Passing Percentage" aria-label="Help with Passing Percentage"></i>
					</a>
				</span>
				<label class="col-form-label d-inline" for="id_kpi_individual_student">
					Passing Percentage Individual (mapping of a PLO to a student)
				</label>
			</div>
			<div class="col-md-9 form-inline felement" data-fieldtype="number">
				<span class="input-group-addon" style="display: inline;"><i class="fa fa-percent"></i></span>
				<input type="number"
						class="form-control"
						name="kpi_individual_student"
						id="id_kpi_individual_student"
						size=""
						required
						placeholder="eg. 50"
						maxlength="10"
						step="0.001"
						min="0" max="100"
						value="50">
				<div class="form-control-feedback" id="id_error_kpi_individual_student">
				<?php
				if(isset($msg7)){
					echo $msg7;
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
					Map to PEO
				</label>
			</div>
			<div class="col-md-9 form-inline felement">
				<select onChange="dropdownTip(this.value)" name="peo" class="select custom-select" required id="id_select_peo">
					<option value=''>Choose..</option>
					<?php
					foreach ($peos as $p) {
					$id =  $p->id;
					$name = $p->shortname;
					$idnumpeo = $p->idnumber;
					?>
					<option value='<?php echo $id; ?>'><?php echo $idnumpeo; ?></option>
					<?php
					}
					?>
				</select>
				<span id="peosidnumber"></span>
				<div class="form-control-feedback" id="id_error_shortname">
				<?php
				if(isset($msg4)){
					echo $msg4;
				}
				?>
				</div>
			</div>
		</div>
		
		<input class="btn btn-info" type="submit" name="save" value="Save"/>
	</form>

	<script>
		var peoIdNumber = <?php echo json_encode($peoNameArray); ?>;
		var peoId = <?php echo json_encode($peoIdArray); ?>;
		function dropdownTip(value){
			//var peosidnumber = "peosidnumber";
			if(value == ''){
				document.getElementById("peosidnumber").innerHTML = "";
			}
			else{
				for(var i=0; i<peoIdNumber.length ; i++){
					if(peoId[i] == value){
						document.getElementById("peosidnumber").innerHTML = peoIdNumber[i];
						break;
					}
				}
			}
		}
	</script>

	<script>
		//form validation
		$(document).ready(function () {
			$('#ploForm').validate({ // initialize the plugin
				rules: {
					"idnumber": {
						required: true,
						minlength: 1,
						maxlength: 20,
						pattern: /^[p/P][l/L][o/O]-[0-9]{1,}$/
					},
					"shortname": {
						required: true,
						minlength: 1,
						maxlength: 30
					},
					"kpi_cohort_programme": {
						number: true,
						required: true,
						step: 0.001,
						range: [0, 100],
						min: 0,
						max: 100,
						minlength: 1,
						maxlength: 7
					},/*
					"kpi_cohort_course": {
						number: true,
						required: true,
						step: 0.001,
						range: [0, 100],
						min: 0,
						max: 100,
						minlength: 1,
						maxlength: 7
					},*/
					"kpi_individual_student": {
						number: true,
						required: true,
						step: 0.001,
						range: [0, 100],
						min: 0,
						max: 100,
						minlength: 1,
						maxlength: 7
					},
					"peo": {
						required: true
					}
				},
				messages: {
					"idnumber": {
						required: "Please enter ID number.",
						pattern: "Please enter correct format."
					},
					"shortname": {
						required: "Please enter Name."
					},
					"kpi_cohort_programme": {
						number: "Only numeric values are allowed.",
						required: "Please enter percentage.",
						step: "Please enter nearest percentage value.",
						range: "Please enter percentage between 0 and 100%.",
						min: "Please enter percentage greater than or equal to 0%.",
						max: "Please enter percentage less than or equal to 100%.",
						minlength: "Please enter more than 1 numbers.",
						maxlength: "Please enter no more than 6 numbers (including decimal part)."
					},/*
					"kpi_cohort_course": {
						number: "Only numeric values are allowed.",
						required: "Please enter percentage.",
						step: "Please enter nearest percentage value.",
						range: "Please enter percentage between 0 and 100%.",
						min: "Please enter percentage greater than or equal to 0%.",
						max: "Please enter percentage less than or equal to 100%.",
						minlength: "Please enter more than 1 numbers.",
						maxlength: "Please enter no more than 6 numbers (including decimal part)."
					},*/
					"kpi_individual_student": {
						number: "Only numeric values are allowed.",
						required: "Please enter percentage.",
						step: "Please enter nearest percentage value.",
						range: "Please enter percentage between 0 and 100%.",
						min: "Please enter percentage greater than or equal to 0%.",
						max: "Please enter percentage less than or equal to 100%.",
						minlength: "Please enter more than 1 numbers.",
						maxlength: "Please enter no more than 6 numbers (including decimal part)."
					},
					"peo": {
						required: "Please select PEO."
					}
				}
			});
		});
	</script>
	
	<?php
		if(!empty($_GET['edit']) && !isset($_POST['save'])){
			$id=$_GET['edit'];
			$rec=$DB->get_records_sql('SELECT plo.shortname, plo.description, plo.idnumber, plo.parentid, kcp.kpi AS cpkpi, kis.kpi AS iskpi FROM mdl_competency plo, mdl_plo_kpi_cohort_programme kcp, mdl_plo_kpi_individual_student kis WHERE plo.id=? AND kcp.ploid=plo.id AND kis.ploid=plo.id',array($id));
			if($rec){
				foreach ($rec as $records){
					$idnumber=$records->idnumber;
					$shortname=$records->shortname;
					$description=$records->description;
					$peo=$records->parentid;
					$cpkpi=$records->cpkpi;
					//$cckpi=$records->cckpi;
					$iskpi=$records->iskpi;
				}
			}
	?>
	<script>
		document.getElementById("id_idnumber").value = <?php echo json_encode($idnumber); ?>;
		document.getElementById("id_shortname").value = <?php echo json_encode($shortname); ?>;
		document.getElementById("id_description").value = <?php echo json_encode($description); ?>;
		document.getElementById("id_kpi_cohort_programme").value = <?php echo json_encode($cpkpi); ?>;
		
		document.getElementById("id_kpi_individual_student").value = <?php echo json_encode($iskpi); ?>;
		document.getElementById("id_select_peo").value = <?php echo json_encode($peo); ?>;
	</script>

	<?php
		}
		elseif(isset($_POST['save'])){
			?>
		<script>
			document.getElementById("id_idnumber").value = <?php echo json_encode($idnumber); ?>;
			document.getElementById("id_shortname").value = <?php echo json_encode($shortname); ?>;
			document.getElementById("id_description").value = <?php echo json_encode($description); ?>;
			document.getElementById("id_kpi_cohort_programme").value = <?php echo json_encode($cpkpi); ?>;
			
			document.getElementById("id_kpi_individual_student").value = <?php echo json_encode($iskpi); ?>;
			document.getElementById("id_select_peo").value = <?php echo json_encode($peo); ?>;
		</script>

		<?php
		}
	?>

	<br />
	<div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>
	
	<?php 
        label:
        ?>
        <div class="btn-btn-info"><br><a href="./select_frameworktoPLO.php" >Back</a></div>
        <?php
	}
	else
    {?>
        <h3 style="color:red;"> Invalid Selection </h3>
        <a href="./select_frameworktoPLO.php">Back</a>
    	<?php
	}
	echo $OUTPUT->footer();
	?>