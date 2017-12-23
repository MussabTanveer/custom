<?php

 require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Map CLOS to PLOS");
    $PAGE->set_heading("Map CLOs to PLOs");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/admin/confirm_clo_plo.php');
    
    echo $OUTPUT->header();

    require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());

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