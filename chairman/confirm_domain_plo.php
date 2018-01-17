<?php
	require_once('../../../config.php');
    $context = context_system::instance();
	$PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Add Domain");
    $PAGE->set_heading("Add PLO Domain");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/confirm_domain_plo.php');
    
	echo $OUTPUT->header();
	require_login();
	$rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
     
	if(isset($_POST['submit']))
	{
		$plocount = $_POST['plocount'];
		$fw_id = $_POST['fwid'];
		//echo $fw_id;

		$cognitivearray = array();
		for ($i = 0; $i < $plocount; $i++) {
			$cognitivearray[$i] = in_array($i, $_POST['cognitive']) ? 1 : 0;
		}

		$psychomotorarray = array();
		for ($i = 0; $i < $plocount; $i++) {
			$psychomotorarray[$i] = in_array($i, $_POST['psychomotor']) ? 1 : 0;
		}

		$affectivearray = array();
		for ($i = 0; $i < $plocount; $i++) {
			$affectivearray[$i] = in_array($i, $_POST['affective']) ? 1 : 0;
		}
	}
	$ploidsarray=$SESSION->ploids;
	/*
	echo $ploidsarray[0];
	echo "<br>";
	echo $ploidsarray[1];
	echo "<br>";
	echo $ploidsarray[2];
	echo "<br>";
	echo $ploidsarray[3];
	echo "<br>";
	echo $ploidsarray[4];
	echo "<br>";echo "<br>";
	
	echo $cognitivearray[0];
	echo "<br>";
	echo $cognitivearray[1];
	echo "<br>";
	echo $cognitivearray[2];
	echo "<br>";
	echo $cognitivearray[3];
	echo "<br>";
	echo $cognitivearray[4];
	echo "<br>";echo "<br>";

	echo $psychomotorarray[0];
	echo "<br>";
	echo $psychomotorarray[1];
	echo "<br>";
	echo $psychomotorarray[2];
	echo "<br>";
	echo $psychomotorarray[3];
	echo "<br>";
	echo $psychomotorarray[4];
	echo "<br>";echo "<br>";

	echo $affectivearray[0];
	echo "<br>";
	echo $affectivearray[1];
	echo "<br>";
	echo $affectivearray[2];
	echo "<br>";
	echo $affectivearray[3];
	echo "<br>";
	echo $affectivearray[4];
	echo "<br>";
	*/
	$i=0;
	foreach ($ploidsarray as $pid)
	{
		if($cognitivearray[$i] != 0){
			$sql="INSERT INTO mdl_taxonomy_plo_domain (frameworkid, ploid, domainid) VALUES($fw_id, $pid, 1)";
			$DB->execute($sql);
		}
		if($psychomotorarray[$i] != 0){
			$sql="INSERT INTO mdl_taxonomy_plo_domain (frameworkid, ploid, domainid) VALUES($fw_id, $pid, 2)";
			$DB->execute($sql);
		}
		if($affectivearray[$i] != 0){
			$sql="INSERT INTO mdl_taxonomy_plo_domain (frameworkid, ploid, domainid) VALUES($fw_id, $pid, 3)";
			$DB->execute($sql);
		}
		$i++;
	}
	
	echo "<font color='green'>Domains successfully mapped with the PLOs!</font>";
	echo "<br>";
?>
<a href="./display_outcome_framework-2.php">Back</a>

<?php 	
	echo $OUTPUT->footer();
?>
