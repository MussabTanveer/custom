<?php
	require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("AVision & Mission");
    $PAGE->set_heading("Define Vision & Mission");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/admin/save_vision_mission.php');
    
	$universityVision = trim($_POST["uv"]);
	$universityMission = trim($_POST["um"]);
	$departmentVision = trim($_POST["dv"]);
	$departmentMission = trim($_POST["dm"]);


	/*echo "$universityVision <br>";
	echo "$universityMission <br>";
	echo "$departmentVision <br>";
	echo "$departmentMission <br> ";*/

	
	$sql="UPDATE `mdl_vision_mission` SET description = '$universityVision' WHERE idnumber='uv'";
	$DB->execute($sql);

	$sql="UPDATE `mdl_vision_mission` SET description = '$universityMission' WHERE idnumber='um'";
	$DB->execute($sql);

	$sql="UPDATE `mdl_vision_mission` SET description = '$departmentVision' WHERE idnumber='dv'";
	$DB->execute($sql);

	$sql="UPDATE `mdl_vision_mission` SET description = '$departmentMission' WHERE idnumber='dm'";
		
	$DB->execute($sql);
		
	

?>