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
		echo $course_id;
		if(isset($_POST['save'])){
			$activity=$_POST['activity'];
			$percentage=$_POST['percentage'];

			echo $activity;
			echo $percentage;
			$sql="INSERT INTO mdl_grading_policy (courseid,name,percentage) VALUES ('$course_id','$activity','$percentage')";

			$DB->execute($sql);
		}
	}

	?>
	<h3>Select an Activity to choose Grading Policy for:</h3>
	<form method='post' action="" class="mform">
	<div class="form-group row fitem">
		<div class="col-md-4 form-inline felement">
			<select
				id="activity" class="custom-select singleselect" name="activity">
				<option name="quiz" value="mid-terms">Quiz</option>
				<option name="assignment" value="mid-terms">Assignment</option>
				<option name="project" value="mid-terms">Project</option>
				<option name="mid term" value="mid-terms">Mid Term</option>
				<option name="final exam" value="finals">Final Exam</option>
				<option name="others" value="others">Others</option>
			</select>
		</div>
		<div class="col-md-4 form-inline felement" data-fieldtype="number">
			<input type="number"
					class="form-control "
					name="percentage"
					id="id_shortname"
					size=""
					required
					maxlength="100"
					min="0" max="100"> %
		</div>
		<div class="form-control-feedback" id="id_error_shortname">
		</div>
	</div>
	</form>

	<input class="btn btn-info" type="submit" name="save" value="Save and continue"/>
	<input class="btn btn-info" type="submit" name="return" value="Save and return"/>
	<a class="btn btn-default" type="submit" href="./report_teacher.php">Cancel</a>

	<?php

		echo $OUTPUT->footer();

	?>
