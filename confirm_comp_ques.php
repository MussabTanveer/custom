<?php
	require_once('../config.php');
    $context = context_system::instance();
	$PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Add CLO");
    $PAGE->set_heading("Add Activity CLO");
    $PAGE->set_url($CFG->wwwroot.'/custom/confirm_comp_ques.php');
    
	echo $OUTPUT->header();
	require_login();

	$competencyarray=array();
 
	if(isset($_POST['ok']))
	{
			foreach ($_POST['competency'] as $competency)
			{
				array_push($competencyarray,$competency);	
			}
	}
	/*echo $competencyarray[0];
	echo "<br>";
	echo $competencyarray[1];
	echo "<br>";
	echo $competencyarray[2];
	echo "<br>";
	echo $competencyarray[3];
	echo "<br>";
	echo $competencyarray[4];
	echo "<br>";*/	
	
	$qidarray=$SESSION->qidarray;
	
	$i=0;
	
	foreach ($qidarray as $qid)
	{
		//echo "Question ID -->:" .$qid ;// Displaying Selected Value
		//echo "<br>";
		if($competencyarray[$i] != 'NULL'){
			$sql="UPDATE mdl_question SET competencyid = '$competencyarray[$i]' WHERE id='$qid'";
			$DB->execute($sql);
		}
		$i++;
	}
	
	echo "<font color='green'>CLOs successfully mapped with the activity!</font>";
	echo "<br>";
?>
<a href="./display_courses-2.php">Back</a>

<?php 	
	echo $OUTPUT->footer();
?>
