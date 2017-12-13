<?php
	require_once('../../../config.php');
    $context = context_system::instance();
	$PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Add Level");
    $PAGE->set_heading("Add CLO Level");
    $PAGE->set_url($CFG->wwwroot.'/custom/confirm_level_clo.php');
    
	echo $OUTPUT->header();
	require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());
 
	if(isset($_POST['submit']))
	{
		$clocount = $_POST['clocount'];
		$fw_id = $_POST['fwid'];

		$levelarray = array();
		foreach ($_POST['level'] as $level)
		{
			array_push($levelarray,$level);	
		}
	}
	$cloidsarray=$SESSION->cloids;
	/*
	echo $cloidsarray[0];
	echo "<br>";
	echo $cloidsarray[1];
	echo "<br>";
	echo $cloidsarray[2];
	echo "<br>";
	echo $cloidsarray[3];
	echo "<br>";
	echo $cloidsarray[4];
	echo "<br>";
	echo $cloidsarray[5];
	echo "<br>";echo "<br>";
	
	echo $levelarray[0];
	echo "<br>";
	echo $levelarray[1];
	echo "<br>";
	echo $levelarray[2];
	echo "<br>";
	echo $levelarray[3];
	echo "<br>";
	echo $levelarray[4];
	echo "<br>";
	echo $levelarray[5];
	echo "<br>";echo "<br>";
	*/
	$i=0;
	foreach ($cloidsarray as $cid)
	{
		if($levelarray[$i] != ''){
			$sql="INSERT INTO mdl_taxonomy_clo_level (frameworkid, cloid, levelid) VALUES($fw_id, $cid, $levelarray[$i])";
			$DB->execute($sql);
		}
		$i++;
	}
	
	echo "<font color='green'>Levels successfully mapped with the CLOs!</font>";
	echo "<br>";
?>
<a href="./display_outcome_framework-3.php">Back</a>

<?php 	
	echo $OUTPUT->footer();
?>
