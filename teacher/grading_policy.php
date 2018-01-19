<script src="../script/jquery/jquery-3.2.1.js"></script>
<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Grading policy");
    $PAGE->set_heading("Grading policy");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/grading_policy.php');
    echo $OUTPUT->header();
	require_login();
?>
<style>
	input[type='number'] {
		-moz-appearance:textfield;
	}
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
	}
</style>
<?php

	if(isset($_GET['course'])){
		$course_id=$_GET['course'];
		//echo $course_id;
		if(isset($_POST['save'])){
			$sum = 0;
			for ($i=0; $i < count($_POST["activity"]); $i++) {
				$sum+=trim($_POST["percentage"][$i]);
			}
			//echo $sum;
			if($sum != 100){
				$msgP = "<font color = red>Total percentage of all activities should be 100%</font><br />";
			}
			else{
				for ($i=0; $i < count($_POST["activity"]); $i++) {
					# code...
					$activity=trim($_POST["activity"][$i]);
					$percentage=trim($_POST["percentage"][$i]);
					//echo $activity;
					//echo $percentage;
					if($percentage != '') 
					{
						$sql="INSERT INTO mdl_grading_policy (courseid,name,percentage) VALUES ('$course_id','$activity','$percentage')";
						$DB->execute($sql);
					}
					else 
					{
						// percentage not entered for activity
					}
				}
				$msgP = "<font color = green>Grading Policy saved successfully!</font><br />";
			}
		}
		elseif(isset($_POST['return'])) {
			$sum = 0;
			for ($i=0; $i < count($_POST["activity"]); $i++) {
				$sum+=trim($_POST["percentage"][$i]);
			}
			//echo $sum;
			if($sum != 100){
				$msgP = "<font color = red>Total percentage of all evaluation methods should be 100%</font><br />";
			}
			else{
				for ($i=0; $i < count($_POST["activity"]); $i++) {
					# code...
					$activity=trim($_POST["activity"][$i]);
					$percentage=trim($_POST["percentage"][$i]);
					//echo $activity;
					//echo $percentage;
					if($percentage != '') 
					{
						$sql="INSERT INTO mdl_grading_policy (courseid,name,percentage) VALUES ('$course_id','$activity','$percentage')";
						$DB->execute($sql);
					}
					else 
					{
						// percentage not entered for activity
					}
				}
				$msgP = "<font color = green>Grading Policy saved successfully!</font><br />";
				$redirect_page="./report_teacher.php?course=$course_id";
				redirect($redirect_page);
			}
		}
		if(isset($msgP)){
			echo $msgP;
		}
		$gps=$DB->get_records_sql('SELECT SUM(percentage) AS sum FROM `mdl_grading_policy` WHERE courseid = ?', array($course_id));
		foreach($gps as $gp){
			$sum = $gp->sum;
		}
		if($sum < 100){
		?>
		<h3>Select an Activity to choose Grading Policy for:</h3>
		<form method='post' action="" class="mform" id="gpForm">
		<div id="dynamicInput">
			<div class="form-group row fitem">
				<div class="col-md-4 form-inline felement">
					<select
						id="activity" class="select custom-select" name="activity[]">
						<option value="">Choose</option>
						<option value="quiz">Quiz</option>
						<option value="assignment">Assignment</option>
						<option value="project">Project</option>
						<option value="mid term">Mid Term</option>
						<option value="final exam">Final Exam</option>
						<option value="other">Other</option>
					</select>
				</div>
				<div class="col-md-4 form-inline felement" data-fieldtype="number">
					<input type="number"
							class="form-control"
							name="percentage[]"
							id="id_shortname"
							size=""
							maxlength="100"
							step="0.001"
							min="0" max="100"> %
				</div>
				<div class="form-control-feedback" id="id_error_shortname">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<input class="btn btn-success" type="button" value="Add another" onClick="addInput('dynamicInput');">
			</div>
		</div>
		<br />
		<input class="btn btn-info" type="submit" name="save" value="Save and continue"/>
		<input class="btn btn-info" type="submit" name="return" value="Save and return"/>
		<a class="btn btn-default" type="submit" <?php echo "href='./report_teacher.php?course=$course_id'" ?>>Cancel</a>
		</form>

		<script>
			// script to add activity and percent fields to form
			//var counter = 1;
			function addInput(divName){
				var newdiv = document.createElement('div');
				newdiv.innerHTML = '<div class="form-group row fitem"><div class="col-md-4 form-inline felement"><select id="activity" class="select custom-select" name="activity[]"><option value="">Choose</option><option value="quiz">Quiz</option><option value="assignment">Assignment</option><option value="project">Project</option><option value="mid term">Mid Term</option><option value="final exam">Final Exam</option><option value="other">Other</option></select></div><div class="col-md-4 form-inline felement" data-fieldtype="number"><input type="number" class="form-control" name="percentage[]" id="id_shortname" size="" maxlength="100" step="0.001" min="0" max="100"> %</div><div class="form-control-feedback" id="id_error_shortname"></div></div>';
				document.getElementById(divName).appendChild(newdiv);
				//counter++;
			}
		</script>
		<?php
		}
		else{
			echo "<font color = green>Grading Policy is already 100%<br />Cannot add another evaluation method.<br />Either <a href=display_grading_policy.php?course=$course_id>delete</a> or <a href=display_grading_policy.php?course=$course_id>edit</a> grading policy.</font>";
		}
	}
	else{
		?>
		<h3 style="color:red;"> Invalid Selection </h3>
    	<a href="../index.php">Back</a>
    	<?php
	}

	echo $OUTPUT->footer();

?>
