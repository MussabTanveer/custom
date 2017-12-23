<?php
	require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Map PLOs to PEOs");
    $PAGE->set_heading("Map PLOs to PEOs");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/admin/confirm_plo_peo.php');
    
    echo $OUTPUT->header();

    require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());

    $peosIdArray=array();
 
			foreach ($_POST['peosId'] as $peoId)
			{
				array_push($peosIdArray,$peoId);	
			}

			//var_dump($peosIdArray);

			$ploidarray=$SESSION->ploidarray;
			
			$i=0;

	
			foreach ($ploidarray as $ploId)
			{
				//echo "Question ID -->:" .$qid ;// Displaying Selected Value
				//echo "<br>";
				if($peosIdArray[$i] != 'NULL'){
					$path="/0/".$peosIdArray[$i]."/";
					
					$sql="UPDATE mdl_competency SET parentid = '$peosIdArray[$i]' ,path = '$path'  WHERE id='$ploId'";
					$DB->execute($sql);
				}
				$i++;
			}
	
	echo "<font color='green'>PLOs successfully mapped with the PEOs!</font>";
	echo "<br>";
?>


<a href="./select_framework.php">Back</a>

   <?php
    echo $OUTPUT->footer();
    ?>