<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Edit OBE CLOs");
    $PAGE->set_heading("Edit CLO");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/edit_clo.php');

    echo $OUTPUT->header();
	require_login();
	$rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
?>

<style>
	input[type='number'] {
		-moz-appearance:textfield;
	}
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
	}
</style>
<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/jquery/jquery-2.1.3.js"></script>
<?php


	if(!empty($_GET['edit']) && !empty($_GET['fwid']))
	{
		$cid=$_GET['edit'];
		$fw_id=$_GET['fwid'];


		//Get plo with its name and idnumber
		$plos=$DB->get_records_sql('SELECT * FROM  `mdl_competency` WHERE competencyframeworkid = ? AND idnumber LIKE "plo%" ORDER BY id', array($fw_id));
		
		if($plos){
			$ploNameArray=array(); $ploIdArray=array(); $ploIdnumberArray=array();
			foreach ($plos as $plo) {
				$id =  $plo->id;
				$name = $plo->shortname;
				$idnumber =  $plo->idnumber;
				array_push($ploIdnumberArray,$idnumber);
				array_push($ploNameArray,$name);
				array_push($ploIdArray,$id);
			}
		}


		//Get domains
        $recDomains=$DB->get_records_sql("SELECT * FROM mdl_taxonomy_domain");
		if($recDomains){
			$domid = array(); $domname = array();
			foreach ($recDomains as $recD) {
				$did = $recD->id;
				$dn = $recD->name;
				array_push($domid, $did); // array of dom ids
				array_push($domname, $dn); // array of dom names
			}
		}


		//Get level with its name and domain name
        $recLevels=$DB->get_records_sql("SELECT txl.id, txl.name AS level_name, txl.level, txd.name AS domain_name FROM mdl_taxonomy_levels txl, mdl_taxonomy_domain txd WHERE txl.domainid=txd.id");
		if($recLevels){
			$levelid = array(); $lname = array(); $dname = array(); $lvlshortname = array();
			foreach ($recLevels as $recL) {
				$lid = $recL->id;
				$lvl = $recL->level;
				$ln = $recL->level_name;
				$dn = $recL->domain_name;
				array_push($levelid, $lid); // array of level ids
				array_push($lvlshortname, $lvl); // array of level names
				array_push($lname, $ln); // array of level names
				array_push($dname, $dn); // array of domain names
			}
		}
		//var_dump($levelid);

		
		if(isset($_POST['save']))
		{
			//$shortname=$_POST['shortname'];
			$description=$_POST['description'];
			//$idnumber=$_POST['idnumber']; $idnumber=strtoupper($idnumber);
			$kpi = $_POST['kpi'];
			$cKpi = $_POST['ckpi'];
			$plo = $_POST['plos'];
			$domain = $_POST['domains'];
			$levelid = $_POST['levels'];

			if (isset($_POST['rubrics'][0]))
				$rubricId = $_POST['rubrics'][0];
			//echo "$rubricId";
			//var_dump($_POST['rubrics'][0]);

		//	echo "$plo $domain $level";


			$time = time();
			//echo "$cid";

			$revisions=$DB->get_records_sql('SELECT * FROM `mdl_competency` where id = ? ', array($cid));
			
			if($revisions){
				foreach ($revisions as $revision){
					//$plo = $revision->parentid;
					$fwidd= $revision->competencyframeworkid; 
					$shortname = $revision->shortname;
					$idnumber = $revision->idnumber;
				}
			}
			
			$levels=$DB->get_records_sql('SELECT * FROM `mdl_taxonomy_clo_level` where cloid = ? ', array($id));
			
			//if($levels){
			//	foreach ($levels as $level){
					//$levelid = $level->levelid;
			//	}
			//}
			
			/*$sql="INSERT INTO mdl_competency (shortname, description, descriptionformat, idnumber, competencyframeworkid, parentid, path, sortorder, timecreated, timemodified, usermodified) 
			VALUES ('$shortname', '$description', '1', '$idnumber','$fwidd' ,'$plo', '/0/', '0', '$time', '$time',$USER->id)";
			$DB->execute($sql);*/

			try {
				$transaction = $DB->start_delegated_transaction();
				$record = new stdClass();
				$record->shortname = $shortname;
				$record->description = $description;
				$record->descriptionformat = 1;
				$record->idnumber = $idnumber;
				$record->competencyframeworkid = $fwidd;
				$record->parentid = $plo;
				$record->path = '/0/';
				$record->sortorder = 0;
				$record->timecreated = $time;
				$record->timemodified = $time;
				$record->usermodified = $USER->id;
				
				$cloid = $DB->insert_record('competency', $record);

				/*$query=$DB->get_records_sql("SELECT MAX(id) as id FROM  mdl_competency");

				foreach ($query as $q){
					$lastId = $q->id;
				}*/
						
				/*$sql="INSERT INTO mdl_taxonomy_clo_level (frameworkid, cloid , levelid) VALUES ('$fwidd','$cloid','$levelid')";
				$DB->execute($sql);*/

				$record = new stdClass();
				$record->frameworkid = $fwidd;
				$record->cloid = $cloid;
				$record->levelid = $levelid;
				
				$DB->insert_record('taxonomy_clo_level', $record);

				/*$sql="INSERT INTO mdl_clo_revision (cloid , revision) VALUES ('$id','$cloid')";
				$DB->execute($sql);*/

				$record = new stdClass();
				$record->cloid = $cid;
				$record->revision = $cloid;
				
				$DB->insert_record('clo_revision', $record);
						
				/*$sql="INSERT INTO mdl_clo_kpi (cloid , kpi) VALUES ('$cloid','$kpi')";
				$DB->execute($sql);*/

				$record = new stdClass();
				$record->cloid = $cloid;
				$record->kpi = $kpi;
				
				$DB->insert_record('clo_kpi', $record);

				$record = new stdClass();
				$record->cloid = $cloid;
				$record->kpi = $cKpi;
				
				$DB->insert_record('clo_cohort_kpi', $record);

				if (isset($_POST['rubrics'][0]))
				{
					$record = new stdClass();
					$record->cloid = $cloid;
					$record->rubric = $rubricId;
					
					$DB->insert_record('clo_rubric', $record);
				}



				$transaction->allow_commit();
			
				$msg3 = "<font color='green'><b>CLO successfully updated!</b></font><br />";
			} catch(Exception $e) {
				$transaction->rollback($e);
				$msg3 = "<font color='red'>CLO failed to edit!</font>";
			}
				
		}
		
		if(isset($msg3)){
            echo $msg3;
            goto label;
		}
	?>
	
	<br />
	<h3>Edit CLO</h3>
	<form method='post' action="" class="mform">
		<!--
		<div class="form-group row fitem ">
			<div class="col-md-3">
				<span class="pull-xs-right text-nowrap">
					<abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
				</span>
				<label class="col-form-label d-inline" for="id_shortname">
					Name
				</label>
			</div>
			<div class="col-md-9 form-inline felement" data-fieldtype="text">
				<input type="text"
						class="form-control "
						name="shortname"
						id="id_shortname"
						size=""
						required
						maxlength="100" type="text" >
				<div class="form-control-feedback" id="id_error_shortname">
				<?php/*
				if(isset($msg1)){
					echo $msg1;
				}*/
				?>
				</div>
			</div>
		</div>
		-->

		<div class="form-group row fitem">
			<div class="col-md-3">
				<span class="pull-xs-right text-nowrap">
				</span>
				<label class="col-form-label d-inline" for="id_description">
					Description
				</label>
			</div>
			<div class="col-md-9 form-inline felement" data-fieldtype="editor">
				<div>
					<div>
						<textarea id="id_description" name="description" class="form-control" rows="4" cols="80" spellcheck="true" ></textarea>
					</div>
				</div>
				<div class="form-control-feedback" id="id_error_description"  style="display: none;">
				</div>
			</div>
		</div>


        <!--
		<div class="form-group row fitem">
			<div class="col-md-3">
				<span class="pull-xs-right text-nowrap">
					<abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
				</span>
				<label class="col-form-label d-inline" for="id_idnumber">
				ID number
				</label>
			</div>
			<div class="col-md-9 form-inline felement" data-fieldtype="text">
				<input type="text"
						class="form-control "
						name="idnumber"
						id="id_idnumber"
						size=""
						pattern="[a-zA-Z]{2}-[0-9]{3}-[c/C][l/L][o/O]-[0-9]{1,}"
                        title="eg. CS-304-CLO-1"
						required
						maxlength="100" type="text" >
				<div class="form-control-feedback" id="id_error_idnumber">
				<?php/*
				if(isset($msg2)){
					echo $msg2;
				}*/
				?>
				</div>
			</div>
		</div>
		-->
		<div class="form-group row fitem ">
			<div class="col-md-3">
				<span class="pull-xs-right text-nowrap">
					<abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
				</span>
				<label class="col-form-label d-inline" for="id_kpi">
					Passing Percentage Individual (student)
				</label>
			</div>
			<div class="col-md-9 form-inline felement" data-fieldtype="number">
				<input type="number"
						class="form-control"
						name="kpi"
						id="id_kpi"
						size=""
						required
						placeholder="eg. 50"
						maxlength="100"
						step="0.001"
						min="0" max="100"> %
				<div class="form-control-feedback" id="id_error_kpi">
				
				</div>
			</div>
		</div>

		<div class="form-group row fitem ">
			<div class="col-md-3">
				<span class="pull-xs-right text-nowrap">
					<abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
				</span>
				<label class="col-form-label d-inline" for="id_kpi">
					Passing Percentage Cohort (course)
				</label>
			</div>
			<div class="col-md-9 form-inline felement" data-fieldtype="number">
				<input type="number"
						class="form-control"
						name="ckpi"
						id="id_ckpi"
						size=""
						required
						placeholder="eg. 50"
						maxlength="100"
						step="0.001"
						min="0" max="100"> %
				<div class="form-control-feedback" id="id_error_kpi">
				
				</div>
			</div>
		</div>



		<div class="form-group row fitem ">
				<div class="col-md-3">
					<span class="pull-xs-right text-nowrap">
						<abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
					</span>
					<label class="col-form-label d-inline" for="id_plo">
						Map to PLO
					</label>
				</div>
				<div class="col-md-9 form-inline felement">
					<select  onChange="dropdownPlo(this.value, 0)" name="plos" class="select custom-select" id="id_plo">
						<option value=''>Choose..</option>
						<?php
						foreach ($plos as $plo) {
							$id =  $plo->id;
							$name = $plo->shortname;
							$idnumber = $plo->idnumber;
						?>
						<option value='<?php echo $id; ?>' title="<?php echo $name; ?>"><?php echo $idnumber; ?></option>
						<?php
						}
						?>
					</select>
					<span id="plosidnumber0"></span>
					<div class="form-control-feedback" id="id_error_plo">
					</div>
				</div>
			</div>


		<div class="form-group row fitem ">
				<div class="col-md-3">
					<span class="pull-xs-right text-nowrap">
						<abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
					</span>
					<label class="col-form-label d-inline" for="id_domain">
						Taxonomy Domain
					</label>
				</div>
				<div class="col-md-9 form-inline felement">
					<select id="id_domain" required onChange="dropdownDomain(this.value, 0)" name="domains" class="select custom-select">
						<option value=''>Choose..</option>
						<?php
						foreach ($recDomains as $recD) {
							$did = $recD->id;
							$dn = $recD->name;
							?>
							<option value="<?php echo $did; ?>"><?php echo $dn; ?></option>
						<?php
						}
						?>
					</select>
					<div class="form-control-feedback" id="id_error_level">
					</div>
				</div>
			</div>

			<div class="form-group row fitem ">
				<div class="col-md-3">
					<span class="pull-xs-right text-nowrap">
						<abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
					</span>
					<label class="col-form-label d-inline" for="id_level">
						Taxonomy Level
					</label>
				</div>
				<div class="col-md-9 form-inline felement">
					<select id="id_level" required onChange="dropdownLevel(this.value)" name="levels" class="select custom-select">
						<option value=''>Choose..</option>
					</select>
					<span id="dname0"></span>
					<span id="lname0"></span>
					
					<div class="form-control-feedback" id="id_error_level">
					</div>
				</div>
			</div>


			<div id="rubric_dd"> </div>


		<input class="btn btn-info" type="submit" name="save" value="Save"/>
	</form>
	
	<?php
		if(isset($_GET['edit'])){
		$id=$_GET['edit'];
		$rec=$DB->get_records_sql('SELECT shortname,description,idnumber,parentid FROM mdl_competency WHERE id=?',array($id));
		$recKPI=$DB->get_records_sql('SELECT kpi FROM mdl_clo_kpi WHERE cloid=?',array($id));
		$recCKPI=$DB->get_records_sql('SELECT kpi FROM mdl_clo_cohort_kpi WHERE cloid=?',array($id));
		$description = "";
		$kpi = "";
		$recLevel=$DB->get_records_sql('SELECT * FROM mdl_taxonomy_clo_level WHERE cloid=?',array($id));
		if($rec){
			foreach ($rec as $records){
				$shortname=$records->shortname;
				$description=$records->description;
				$idnumber=$records->idnumber;
				$parentid = $records->parentid;

			}
		}
		if($recKPI){
			foreach ($recKPI as $rKPI){
				$kpi=$rKPI->kpi;
			}
		}

		if($recCKPI){
			foreach ($recCKPI as $rcKPI){
				$ckpi=$rcKPI->kpi;
			}
		}
		if($recLevel){
			foreach ($recLevel as $rcLevel){
				$level=$rcLevel->levelid;
			}
		}
		$recLevel=$DB->get_records_sql('SELECT * FROM mdl_taxonomy_levels WHERE id=?',array($level));
		if($recLevel){
			foreach ($recLevel as $rcLevel){
				$levelName=$rcLevel->name;
				$domainid=$rcLevel->domainid;
				$level=$rcLevel->level;
			}
		}
			//echo "$levelName";

		$recDomain=$DB->get_records_sql('SELECT * FROM mdl_taxonomy_domain WHERE id=?',array($domainid));
		if($recLevel){
			foreach ($recDomain as $rcDomain){
				$DomainName=$rcDomain->name;
				//$domainid=$rcDomain->domainid;
			}
		}
			//echo $DomainName;
		if ($DomainName == "cognitive")
			$DomainNo =1;
		elseif ($DomainName == "psychomotor")
			$DomainNo =2;
		elseif ($DomainName == "affective")
			$DomainNo =3;

		//echo "$level $levelName";
		$LevelInfo = "Current Level: " .substr($level, 1). " ($levelName)";

		$recRubric=$DB->get_records_sql('SELECT * FROM mdl_clo_rubric WHERE cloid=?',array($id));
		if($recRubric){
			foreach ($recRubric as $rcRubric){
				$RubricID=$rcRubric->rubric;
				//$domainid=$rcDomain->domainid;
			}

			$recRubric=$DB->get_records_sql('SELECT * FROM mdl_rubric WHERE id=?',array($RubricID));
		if($recRubric){
			foreach ($recRubric as $rcRubric){
				$RubricName=$rcRubric->name;
				//$domainid=$rcDomain->domainid;
			}
		}
		}

	

	//	echo "$RubricName";

		?>
	<script>
	    document.getElementById("id_description").value = <?php echo json_encode($description); ?>;
        document.getElementById("id_kpi").value = <?php echo json_encode($kpi); ?>;
        document.getElementById("id_ckpi").value = <?php echo json_encode($ckpi); ?>;
        document.getElementById("id_plo").value = <?php echo json_encode($parentid); ?>;
        document.getElementById("lname0").innerHTML = <?php echo json_encode($LevelInfo); ?>;
        document.getElementById("id_domain").value = <?php echo json_encode($DomainNo); ?>;

    </script>


    <script>
		    $(document).ready(function() {
				$("#id_domain").on('change',function() {
					var domain_id = $(this).val();
					//domain_id=1;
					console.log(domain_id);
					if(domain_id != "") {
						$.ajax({
							url:"get-levels.php",
							data:{d_id:domain_id},
							type:'POST',
							success:function(response) {
							var resp = $.trim(response);
							$("#id_level").html(resp);
							}
						});
					}
					else {
						$("#id_level").html("<option value=''>Choose..</option>");
					}
					if(domain_id == 2 ||domain_id == 3) {
						$.ajax({
							url:"get-rubrics.php",
							type:'POST',
							success:function(response) {
							var resp = $.trim(response);
							$("#rubric_dd").html(resp);
							}
						});

					//	document.getElementById("id_rubric").innerHTML = <?php// echo json_encode($RubricName); ?>;
					}
					else {
						$("#rubric_dd").html("");
					}
				});
			});
		</script>




    	<script>
			// display plo name of particular plo
			var ploIdName = <?php echo json_encode($ploNameArray); ?>;
			var ploId = <?php echo json_encode($ploIdArray); ?>;
			//console.log(ploIdName);
			function dropdownPlo(value,id){
				document.getElementById("msg").innerHTML = "";
				var plosidnumber = "plosidnumber" + id;
				if(value == 'NULL'){
					document.getElementById(plosidnumber).innerHTML = "";
				}
				else{
					for(var i=0; i<ploIdName.length ; i++){
						if(ploId[i] == value){
							document.getElementById(plosidnumber).innerHTML = ploIdName[i];
							break;
						}
					}
				}
			}
		</script>


		<script>
			// display level and domain names of particular level 
			var levelid = <?php echo json_encode($levelid); ?>;
			var lnames = <?php echo json_encode($lname); ?>;
			var dnames = <?php echo json_encode($dname); ?>;
			/*alert(closid);
			alert(plos);
			alert(peos);*/
			//console.log(lnames);
			function dropdownLevel(value){
				var lname = "lname0";
				var dname = "dname0";
				//console.log(value);
				//console.log(id);
				if(value == ''){
					document.getElementById(lname).innerHTML = "";
					document.getElementById(dname).innerHTML = "";
					//console.log("Im WOrking");
				}
				else{
					for(var i=0; i<levelid.length ; i++){
						if(levelid[i] == value){
							//console.log("Im WOrking");
							//document.getElementById(dname).innerHTML = dnames[i];
							document.getElementById(lname).innerHTML = "("+lnames[i]+")";
							break;
						}
					}
				}
			}
		</script>



	
	<?php
		}
    ?>
	<br />
	<div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>
	
    <?php 
        label:
        ?>
        <div class="btn-btn-info"><br><a href="./select_frameworktoCLO.php" >Back</a></div>
        <?php
	}
	else
    {?>
        <h3 style="color:red;"> Invalid Selection </h3>
        <a href="./select_frameworktoCLO.php">Back</a>
    	<?php
	}
	echo $OUTPUT->footer();
	?>
	