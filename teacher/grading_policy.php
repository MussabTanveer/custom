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
echo $course_id;
    ?>

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





<form method='post' action="" class="mform">
<div class="form-group row fitem">
 <label class="col-form-label d-inline" >
                       Select an Activity to choose Grading Policy for  : </label>
 <select
 	  id="activity" name="activity">
 	<option name="mid-terms" value="mid-terms">Mid-terms</option>
 	<option name="finals" value="finals">Finals</option>
 	<option name="others" value="others">Others</option>

 </select>

</br>

 	<label class="col-form-label d-inline" for="id_shortname">
					Percentage
				</label>
			</div>
				
<div class="col-md-9 form-inline felement" data-fieldtype="text">
				<input type="text"
						class="form-control "
						name="percentage"
						id="id_shortname"
						size=""
						required
						maxlength="100" type="text" >
				<div class="form-control-feedback" id="id_error_shortname">
                        
</div>
</form>

<input class="btn btn-info" type="submit" name="save" value="Save and continue"/>
        <input class="btn btn-info" type="submit" name="return" value="Save and return"/>
		<a class="btn btn-default" type="submit" href="./report_teacher.php">Cancel</a>





<?php

    echo $OUTPUT->footer();

?>