<?php
	require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Add OBE CLOs");
    $PAGE->set_heading("Add Course Learning Outcome (CLO)");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/add_clo.php');
    
	
	$coursecode = trim($_POST["idnumber"]); $coursecode=strtoupper($coursecode);
	$frameworkid = $_POST["frameworkid"];

	for ($i=0; $i <count($_POST["shortname"]) ; $i++) {
		# code...
		$shortname=trim($_POST["shortname"][$i]); $shortname=strtoupper($shortname);
		$idnumber=$coursecode."-".$shortname; $idnumber=strtoupper($idnumber);
		$description=trim($_POST["description"][$i]);
		//echo $idnumber. "<br>";
		//echo $shortname . "<br>";
		//echo $description . "<br>";
		$time = time();

		$cloidnumbers=$DB->get_records_sql('SELECT * FROM  `mdl_competency` 
    		WHERE competencyframeworkid = ? AND idnumber = ?',
    		 array($frameworkid,$idnumber));

		if($cloidnumbers == NULL) 
		{
			$sql="INSERT INTO mdl_competency (shortname, description, descriptionformat, idnumber, competencyframeworkid, parentid, path, sortorder, timecreated, timemodified, usermodified) VALUES ('$shortname', '$description', 1, '$idnumber',$frameworkid ,-2, '/0/', 0, '$time', '$time', $USER->id)";
			$DB->execute($sql);
		}
		else
			echo $idnumber . "already exists<br>";
	}

?>