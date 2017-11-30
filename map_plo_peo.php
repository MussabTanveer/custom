<?php
	require_once('../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Map PLOs to PEOs");
    $PAGE->set_heading("Map PLOs to PEOs");
    $PAGE->set_url($CFG->wwwroot.'/custom/map_plo_peo.php');
    
    echo $OUTPUT->header();

    require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());

    if(isset($_POST['submit']) && isset($_POST['frameworkid']))
    {
    	$framework_id=$_POST['frameworkid'];
    	//echo "$framework_id";
    	
    	$plos=$DB->get_records_sql('SELECT * FROM  `mdl_competency` 
    		WHERE competencyframeworkid = ? 
    		AND idnumber LIKE "plo%" ',
    		 array($framework_id));


		$peos=$DB->get_records_sql('SELECT * FROM `mdl_competency` 
			WHERE competencyframeworkid = ?
			AND parentid = 0 ',
	    	array($framework_id));

    	$serialno = 0;
    
	if ($plos != NULL){

	?>

		<form action="confirm_plo_peo.php" method="post" >
    		<table class="generaltable">
				<tr class="table-head">
					<th> S.No</th>
					<th> Name </th>
					<th> PLO's ID Number </th>
					<th> Select PEO </th>
					<th> PEO's ID Number </th>
				</tr>

	<?php
			$ploidarray=array();
			$peoIdNumberArray=array();
			$peoIdArray=array();

			foreach ($peos as $peo) {
				$id =  $peo->id;
				$name = $peo->shortname;
				$idnumber =  $peo->idnumber;
				array_push($peoIdNumberArray,$idnumber);
				array_push($peoIdArray,$id);

			}

			$i = 0;

            foreach ($plos as $plo) {
                $serialno++;
                $id = $plo->id;
                $name = $plo->shortname;
               	$idnumber =  $plo->idnumber;
               	array_push($ploidarray,$id);


               	?>
				<tr>
					<td><?php echo $serialno;?> </td>
					<td><?php echo $name;?></td>
					<td><?php echo 	$idnumber;?></td>
					<td>
						<select  onChange="dropdownTip(this.value, <?php echo $i ?>)"  name="peosId[]" class="select custom-select">
							<option value='NULL'>Choose..</option>
							<?php
							foreach ($peos as $peo) {
							$id =  $peo->id;
							$name = $peo->shortname;
							
							?>
							<option value='<?php echo $id; ?>'><?php echo $name; ?></option>
							<?php
							}
							?>
						</select>
					</td>
					<td > <p id="peosidnumber<?php echo $i ?>" >  </p>  </td> 

				</tr>
				<?php
				$i++;
               
            }
            global $SESSION;
			$SESSION->ploidarray = $ploidarray;  
            ?>  

           </table>

           <input type="submit" value="NEXT" name="ok" class="btn btn-primary">
			
		</form>
    <?php
	}
	else
  	{
	?>
		<h5 style="color:red;"> No PLOS found under selected framework.! </h5>
       		 <a href="./select_framework.php">Back</a>

       		 <?php

  	}
	}
	else
	{?>
		<h2 style="color:red;"> Invalid Selection </h2>
		<a href="./select_framework.php">Back</a>

	<?php
	}
?>

<script>
	var peoIdNumber = <?php echo json_encode($peoIdNumberArray); ?>;
	var peoId = <?php echo json_encode($peoIdArray); ?>;
		function dropdownTip(value,id){
				var peosidnumber = "peosidnumber" + id;
				if(value == 'NULL'){
					document.getElementById(peosidnumber).innerHTML = "";
					
				}
				else{
					for(var i=0; i<peoIdNumber.length ; i++){
						
						if(peoId[i] == value){
							
							document.getElementById(peosidnumber).innerHTML = peoIdNumber[i];			
							break;
						}
					}
				}
			}
</script>

<?php
echo $OUTPUT->footer();
?>
