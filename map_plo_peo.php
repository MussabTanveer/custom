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

?>
<script src="./script/jquery/jquery-2.1.3.js"></script>


<script type="text/javascript">
	$(document).ready( function () {
		$('#myForm').submit( function () {
			var formdata = $(this).serialize();
			$.ajax({
			    type: "POST",
			    url: "confirm_plo_peo.php",
			    data: formdata,
			    success:function(){
           	document.getElementById("msg").innerHTML = "<font color='green'>PLOs successfully mapped with the PEOs!</font>"	
        }

			 });
			return false;
		});
	});
</script>


<?php

    if((isset($_POST['submit']) && isset($_POST['frameworkid'])) || (isset($SESSION->fid4) && $SESSION->fid4 != "xyz"))
    {
		if(isset($SESSION->fid4) && $SESSION->fid4 != "xyz")
        {
            $framework_id=$SESSION->fid4;
            $SESSION->fid4 = "xyz";
        }
		else
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

		<form action="" method="post" id="myForm">
    		<table class="generaltable">
				<tr class="table-head">
					<th> S.No</th>
					<th> Name </th>
					<th> PLO's ID Number </th>
					<th> Select PEO </th>
					<th> PEO's Name </th>
				</tr>

	<?php
			$ploidarray=array();
			$peoNameArray=array();
			$peoIdArray=array();

			foreach ($peos as $peo) {
				$id =  $peo->id;
				$name = $peo->shortname;
				$idnumber =  $peo->idnumber;
				array_push($peoNameArray,$name);
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
							$idnumber = $peo->idnumber;
							
							?>
							<option value='<?php echo $id; ?>'><?php echo $idnumber; ?></option>
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

		<p id="msg">
			
		</p>


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

	var peoIdNumber = <?php echo json_encode($peoNameArray); ?>;
	var peoId = <?php echo json_encode($peoIdArray); ?>;
		function dropdownTip(value,id){
			document.getElementById("msg").innerHTML = "";
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
