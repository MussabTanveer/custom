<?php
	require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Map CLOs to PLOs");
    $PAGE->set_heading("Map CLOs to PLOs");
    $PAGE->set_url($CFG->wwwroot.'/custom/map_clo_plo.php');
    
    echo $OUTPUT->header();

    require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());


?>
<script src="../script/jquery/jquery-2.1.3.js"></script>


<script type="text/javascript">
	$(document).ready( function () {
		$('#myForm').submit( function () {
			var formdata = $(this).serialize();
			$.ajax({
			    type: "POST",
			    url: "confirm_clo_plo.php",
			    data: formdata,
			    success:function(){
           	document.getElementById("msg").innerHTML ="<font color='green'>CLOs successfully mapped with the PLOs!</font>"
        }

			 });
			return false;
		});
	});
</script>


<?php
   
    if((isset($_POST['submit']) && isset($_POST['frameworkid'])) || (isset($SESSION->fid5) && $SESSION->fid5 != "xyz"))
    {	
    	if(isset($SESSION->fid5) && $SESSION->fid5 != "xyz")
        {
            $framework_id=$SESSION->fid5;
            $SESSION->fid5 = "xyz";
        }
		else
			$framework_id=$_POST['frameworkid'];
    	//echo "$framework_id";
    	
    	$clos=$DB->get_records_sql('SELECT * FROM `mdl_competency` 
    		WHERE competencyframeworkid = ? 
    		AND idnumber LIKE "%%-%%%-clo%"',
    		 array($framework_id));


    	$plos=$DB->get_records_sql('SELECT * FROM  `mdl_competency` 
    		WHERE competencyframeworkid = ? 
    		AND idnumber LIKE "plo%" ',
    		 array($framework_id));

		
    	$serialno = 0;
    	if ($clos != NULL){

	?>

		<form action="" method="post" id="myForm">
    		<table class="generaltable">
				<tr class="table-head">
					<th> S.No</th>
					<th> CLOs </th>
					<th> CLO's ID Number </th>
					<th> Select PLO </th>
					<th> PLO's Name </th>
					
					
				</tr>

<?php 		$cloidarray=array();
			$ploNameArray=array();
			$ploIdArray=array();

			foreach ($plos as $plo) {
							$id =  $plo->id;
							$name = $plo->shortname;
							$idnumber =  $plo->idnumber;
							array_push($ploNameArray,$name);
							array_push($ploIdArray,$id);

							}
					
			$i = 0;

            foreach ($clos as $clo) {
                $serialno++;
                $id = $clo->id;
                $name = $clo->shortname;
               	$idnumber =  $clo->idnumber;
               	array_push($cloidarray,$id);
               	?>
               		<tr>
					<td><?php echo $serialno;?> </td>
					<td><?php echo $name;?></td>
					<td><?php echo 	$idnumber;?></td>
					<td>
						<select  onChange="dropdownTip(this.value, <?php echo $i ?>)"  name="plosID[]" class="select custom-select">
							<option value='NULL'>Choose..</option>
							<?php
							foreach ($plos as $plo) {
							$id =  $plo->id;
							$name = $plo->shortname;
							$idnumber = $plo->idnumber;
							
							?>
							<option value='<?php echo $id; ?>'><?php echo $idnumber; ?></option>
							<?php
							}
							?>
						</select>
					</td>
					<td > <p id="plosidnumber<?php echo $i ?>" >  </p>  </td> 					
				</tr>
					<?php
					$i++;              
            } 

            global $SESSION;
					$SESSION->cloidarray = $cloidarray;  
            ?>  

           </table>

           <input type="submit" value="NEXT" name="ok" class="btn btn-primary">
			
		</form>
		
		<p id="msg">
		

		</p>

<?php

}else
  {  
  	?>
		<h5 style="color:red;"> No CLOs found under selected framework.! </h5>
       		 <a href="./select_framework-2.php">Back</a>

       		 <?php

  }

    }
     else
        { ?>

        	 <h2 style="color:red;"> Invalid Selection </h2>
       		 <a href="./select_framework-2.php">Back</a>

        <?php
        }
?>
<script>
	//alert("heelo");
	var ploIdNumber = <?php echo json_encode($ploNameArray); ?>;
	var ploId = <?php echo json_encode($ploIdArray); ?>;
		function dropdownTip(value,id){
			document.getElementById("msg").innerHTML = "";
				var plosidnumber = "plosidnumber" + id;
				if(value == 'NULL'){
					document.getElementById(plosidnumber).innerHTML = "";
					
				}
				else{
					for(var i=0; i<ploIdNumber.length ; i++){
						//alert("in FOR");
						
						if(ploId[i] == value){
							//alert("IN IF");
							
							document.getElementById(plosidnumber).innerHTML = ploIdNumber[i];			
							break;
						}
					}
				}
			}

</script>

<?php
echo $OUTPUT->footer();
?>
