<?php
 require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Add OBE CLOs");
    $PAGE->set_heading("Add Course Learning Outcome (CLO)");
    $PAGE->set_url($CFG->wwwroot.'/custom/add_clo.php');
    
		$time = time();
		$coursecode = $_POST["idnumber"];
		$frameworkid = $_POST["frameworkid"];
		

	for ($i=0; $i <count($_POST["shortname"]) ; $i++) { 
		# code...
		$idnumber=$coursecode."-".$_POST["shortname"][$i];
		//echo $idnumber. "<br>";
		$shortname=$_POST["shortname"][$i];
		$description=trim($_POST["description"][$i]);
		//echo $shortname . "<br>";
		//echo $description . "<br>";

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