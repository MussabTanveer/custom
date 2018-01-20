<?php
	require_once('../../../config.php');
    $context = context_system::instance();
	$PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Map Grading Items");
    $PAGE->set_heading("Map Grading Items");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/confirm_grading_item.php');
    
	echo $OUTPUT->header();
	require_login();
	
	if(isset($_POST['submit']))
	{
		$activitycount = $_POST['activitycount'];
		$course_id = $_POST['courseid'];

		$gitemarray = array();
		foreach ($_POST['gitem'] as $gitem)
		{
			array_push($gitemarray,$gitem);	
		}
	}
	$activityidsarray=$SESSION->activityids;
	/*
	echo $activityidsarray[0];
	echo "<br>";
	echo $activityidsarray[1];
	echo "<br>";
	echo $activityidsarray[2];
	echo "<br>";
	echo $activityidsarray[3];
	echo "<br>";
	echo $activityidsarray[4];
	echo "<br>";
	echo $activityidsarray[5];
	echo "<br>";echo "<br>";
	
	echo $gitemarray[0];
	echo "<br>";
	echo $gitemarray[1];
	echo "<br>";
	echo $gitemarray[2];
	echo "<br>";
	echo $gitemarray[3];
	echo "<br>";
	echo $gitemarray[4];
	echo "<br>";
	echo $gitemarray[5];
	echo "<br>";echo "<br>";
	*/
	$i=0;
	foreach ($activityidsarray as $aid)
	{
		if($gitemarray[$i] != ''){
			if(substr($aid,0,1) == 'Q')
				$mod = 16;
			else
				$mod = 1;
			$aid = substr($aid,1);
			$sql="INSERT INTO mdl_grading_mapping (courseid, module, instance, gradingitem) VALUES($course_id, $mod, $aid, $gitemarray[$i])";
			$DB->execute($sql);
		}
		$i++;
	}
	
	echo "<font color='green'>Grading items successfully mapped with activities!</font>";
	echo "<br>";
?>
<a href="./teacher_courses.php">Back</a>

<?php 	
	echo $OUTPUT->footer();
?>
