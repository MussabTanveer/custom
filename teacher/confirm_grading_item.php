<?php
	require_once('../../../config.php');
    $context = context_system::instance();
	$PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Map Grading Items");
    $PAGE->set_heading("Map Grading Items");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/confirm_grading_item.php');
    
	require_login();
	if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
	}
	echo $OUTPUT->header();
	
	if(isset($_POST['submit']))
	{
		$activitycount = $_POST['activitycount'];
		$course_id = $_POST['courseid'];

		$gitemarray = array();
		foreach ($_POST['gitem'] as $gitem)
		{
			array_push($gitemarray,$gitem);	
		}


		$pactivityArray = array();
		foreach ($_POST['pactivity'] as $value) {

			array_push($pactivityArray, $value);
		}

		//var_dump($pactivityArray);
	}
	$activityidsarray=$SESSION->activityids;

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
	

	/*
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

			

			$check=$DB->get_records_sql('SELECT * FROM `mdl_grading_mapping` WHERE courseid = ? AND module = ? AND instance = ? AND gradingitem = ?', array($course_id,$mod,$aid,$gitemarray[$i]));
			if(!$check)	
			{
				$sql="INSERT INTO mdl_grading_mapping (courseid, module, instance, gradingitem) VALUES($course_id, $mod, $aid, $gitemarray[$i])";
				$DB->execute($sql);
			}
		}

		if($pactivityArray[$i] != ''){
			
			

			//$check=$DB->get_records_sql('SELECT * FROM `mdl_parent_mapping` WHERE courseid = ? AND module = ? AND instance = ? AND gradingitem = ?', array($course_id,$mod,$aid,$gitemarray[$i]));
			
			//if(!$check)	
			//{

				$sql="INSERT INTO mdl_parent_mapping (parentid,childid) 
				VALUES ($pactivityArray[$i] , $aid)";
				$DB->execute($sql);
			//}
		}


		$i++;
	}
	
	echo "<font color='green'>Grading items and subactivities successfully mapped!</font>";
	echo "<br>";
?>
<a href="./teacher_courses.php">Back</a>

<?php 	
	echo $OUTPUT->footer();
?>
