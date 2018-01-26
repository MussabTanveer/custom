<script src="../script/jquery/jquery-3.2.1.js"></script>
<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Grading Policy");
    $PAGE->set_heading("Add Grading Policy Items");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/add_mid_and_final.php');
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

$rec1=$DB->get_records_sql('SELECT sum(percentage) as sum1 FROM mdl_grading_policy WHERE name="mid term" AND courseid=?',array($course_id));
$rec2=$DB->get_records_sql('SELECT sum(percentage) as sum2 FROM mdl_grading_policy WHERE name="final exam" AND courseid=?',array($course_id));

foreach ($rec1 as $records1){
                $sum1=$records1->sum1;
               }
foreach ($rec2 as $records2){
                $sum2=$records2->sum2;
               }

$sum=$sum1+$sum2;

               if($sum >= 80){

              //goto end;
              redirect("to_editing_deleting.php?courseid=$course_id");
              //$msg1="<font color = green>Grading Policy is already 80% or greater<br />Cannot Grade anymore.<br />Either <a href=display_grading_policy.php?course=$course_id>delete</a> or <a href=display_grading_policy.php?course=$course_id>edit</a> grading policy.</font>";
            }

		if(isset($_POST['save'])){


$rec1=$DB->get_records_sql('SELECT sum(percentage) as sum1 FROM mdl_grading_policy WHERE name="mid term" AND courseid=?',array($course_id));
$rec2=$DB->get_records_sql('SELECT sum(percentage) as sum2 FROM mdl_grading_policy WHERE name="final exam" AND courseid=?',array($course_id));

foreach ($rec1 as $records1){
                $sum1=$records1->sum1;
               }
foreach ($rec2 as $records2){
                $sum2=$records2->sum2;
               }

$sum=$sum1+$sum2;

//echo $sum;

               if($sum >= 80){
              
              //goto end;

               	redirect('to_editing_deleting.php');

              //$msg1="<font color = green>Grading Policy is already 80% or greater<br />Cannot Grade anymore.<br />Either <a href=display_grading_policy.php?course=$course_id>delete</a> or <a href=display_grading_policy.php?course=$course_id>edit</a> grading policy.</font>";
            }


else{



			/*$sum = 0;
			$rec=$DB->get_records_sql('SELECT percentage FROM mdl_grading_policy WHERE courseid=?',array($course_id));
			foreach ($rec as $records){
                $percentage=$records->percentage;
                $sum+=$percentage;
            }
			*/
			for ($i=0; $i < count($_POST["activity"]); $i++) {
				$sum+=trim($_POST["percentage"][$i]);
			}
			//echo $sum;
			/*
			if($sum != 60){
				$msgP = "<font color = red>Total percentage of all activities should be 60%</font><br />";
			}
			else{*/
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
			//}
		}
	}
		elseif(isset($_POST['return'])) {









			/*$sum = 0;
			$rec=$DB->get_records_sql('SELECT percentage FROM mdl_grading_policy WHERE courseid=?',array($course_id));
			foreach ($rec as $records){
                $percentage=$records->percentage;
                $sum+=$percentage;
			}*/
			
			for ($i=0; $i < count($_POST["activity"]); $i++) {
				$sum+=trim($_POST["percentage"][$i]);
			}
			//echo $sum;
			/*if($sum != 60){
				$msgP = "<font color = red>Total percentage of all evaluation methods should be 60%</font><br />";
			}
			else{*/
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
				$redirect_page="./report_chairman.php";
				redirect($redirect_page);
			//}
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
						
						<option value="mid term">Mid Term</option>
						<option value="final exam">Final Exam</option>
						
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
			
		</div>
		<br />
		<input class="btn btn-info" type="submit" name="save" value="Save and continue"/>
		<input class="btn btn-info" type="submit" name="return" value="Save and return"/>
		<a class="btn btn-default" type="submit" <?php echo "href='./report_chairman.php'" ?>>Cancel</a>
		</form>

		<script>
			// script to add activity and percent fields to form
			//var counter = 1;
			//function addInput(divName){
				//var newdiv = document.createElement('div');
				//newdiv.innerHTML = '<div class="form-group row fitem"><div class="col-md-4 form-inline felement"><select id="activity" class="select custom-select" name="activity[]"><option value="">Choose<option value="mid term">Mid Term</option><option value="final exam">Final Exam</option></select></div><div class="col-md-4 form-inline felement" data-fieldtype="number"><input type="number" class="form-control" name="percentage[]" id="id_shortname" size="" maxlength="100" step="0.001" min="0" max="100"> %</div><div class="form-control-feedback" id="id_error_shortname"></div></div>';
				//document.getElementById(divName).appendChild(newdiv);
				//counter++;
			//}
		</script>
		<?php
		}
       // if(isset($msg1)){


        	//echo $msg1;
        //}     
		//echo "<font color = green>Grading Policy is already 80% or greater<br />Cannot add another evaluation method.<br />Either <a href=display_grading_policy.php?course=$course_id>delete</a> or <a href=display_grading_policy.php?course=$course_id>edit</a> grading policy.</font>";
		//else{
			//echo "<font color = green>Grading Policy is already 100%<br />Cannot add another evaluation method.<br />Either <a href=display_grading_policy.php?course=$course_id>delete</a> or <a href=display_grading_policy.php?course=$course_id>edit</a> grading policy.</font>";
		//}
	}
	else{
		?>
		<h3 style="color:red;"> Invalid Selection </h3>
    	<a href="../index.php">Back</a>
    	<?php
	}

	echo $OUTPUT->footer();

?>
