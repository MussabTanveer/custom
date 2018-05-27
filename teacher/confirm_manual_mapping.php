<?php
	require_once('../../../config.php');
    $context = context_system::instance();
	$PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Map Manual Activities");
    $PAGE->set_heading("Map Manual Activities");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/confirm_manual_mapping.php');
    
	require_login();
	if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
	}
	echo $OUTPUT->header();
	
	if(isset($_POST['submit']))
	{
		$activitycount = $_POST['activitycount'];
		$course_id = $_POST['courseid'];

	

		$pactivityArray = array();
		foreach ($_POST['pactivity'] as $value) {

			array_push($pactivityArray, $value);
		}

		//var_dump($pactivityArray);
	}
	$activityidsarray=$SESSION->activityids;
	$modules=$SESSION->modules;
	//var_dump($modules); echo "<br/>";

	//var_dump($activityidsarray);
	
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

	echo $pactivityArray[0];
	echo "<br />";
	echo $pactivityArray[1];
	echo "<br />";
	echo $pactivityArray[2];
	echo "<br />";
	echo $pactivityArray[3];
	echo "<br />";
	echo $pactivityArray[4];
	echo "<br />";
	echo $pactivityArray[5];
	echo "<br />";*/
	

	$i=0;
	foreach ($activityidsarray as $aid)
	{

		if($pactivityArray[$i] != ''){
			
			$aid = substr($aid,1);
			

			//$check=$DB->get_records_sql('SELECT * FROM `mdl_parent_mapping` WHERE courseid = ? AND module = ? AND instance = ? AND gradingitem = ?', array($course_id,$mod,$aid,$gitemarray[$i]));
			
			//if(!$check)	
			//{

				$sql="INSERT INTO mdl_parent_mapping (parentid,childid,module) 
				VALUES (?,?,?)";
				$DB->execute($sql,	array($pactivityArray[$i] , $aid, $modules[$i]));
			//}
		}


		$i++;
	}
	
	echo "<font color='green'>subactivities successfully mapped!</font>";
	echo "<br>";
?>
<a href="./teacher_courses.php">Back</a>

<?php 	
	echo $OUTPUT->footer();
?>
