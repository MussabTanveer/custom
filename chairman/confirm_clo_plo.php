<?php

 require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Map CLOS to PLOS");
    $PAGE->set_heading("Map CLOs to PLOs");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/confirm_clo_plo.php');
    
    echo $OUTPUT->header();

    require_login();
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
    $plosIdArray=array();
 
	
			foreach ($_POST['plosID'] as $ploId)
			{
				array_push($plosIdArray,$ploId);	
			}

		
			$cloidarray=$SESSION->cloidarray;
			
			$i=0;
	
			foreach ($cloidarray as $cloId)
			{
				//echo "Question ID -->:" .$qid ;// Displaying Selected Value
				//echo "<br>";
				if($plosIdArray[$i] != 'NULL'){
					$sql="UPDATE mdl_competency SET parentid = '$plosIdArray[$i]' WHERE id='$cloId'";
					$DB->execute($sql);
				}
				$i++;
			}
	
	echo "<font color='green'>CLOs successfully mapped with the PLOs!</font>";
	echo "<br>";

	
?>


<a href="./select_framework-2.php">Back</a>

   <?php
    echo $OUTPUT->footer();
    ?>