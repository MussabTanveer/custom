<?php
require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Add OBE CLOs");
    $PAGE->set_heading("Add Course Learning Outcome (CLO)");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/add_clo.php');
	echo $OUTPUT->header();
	require_login();
	$rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
    global $CFG;
    $x= $CFG->dbpass;
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

<script type="text/javascript" >
	$(document).ready(function(){
		$("#button").click(function (event) {
			//var formdata = $("form").serialize();
			$.ajax({
				type : "POST",
				url : "save_clo.php",
				data : new FormData($("#cloForm")[0]),
				contentType : false,
				processData : false,
				success : function(feedback){
					$("#msg").html(feedback);
				}
			});
			return false;

		});
	});
</script>

<?php
    
	if((isset($_POST['submit']) && isset( $_POST['frameworkid'])) || (isset($SESSION->fid3) && $SESSION->fid3 != "xyz") || isset($_POST['save']) || isset($_POST['return']) || isset($_GET['delete']))
	{
		if(isset($_POST['submit']) || (isset($SESSION->fid3) && $SESSION->fid3 != "xyz")){
			if(isset($SESSION->fid3) && $SESSION->fid3 != "xyz")
			{
				$frameworkid=$SESSION->fid3;
				$SESSION->fid3 = "xyz";
			}
			else
				$frameworkid=$_POST['frameworkid'];
			$rec=$DB->get_records_sql('SELECT shortname from mdl_competency_framework WHERE id=?', array($frameworkid));
			if($rec){
				foreach ($rec as $records){
				$framework_shortname = $records->shortname;
				}
			}
		}
		/* if user press save and return */
		elseif(isset($_POST['return'])) {

			$coursecode = trim($_POST["idnumber"]); $coursecode=strtoupper($coursecode);
			$frameworkid = $_POST["frameworkid"];
			$plosIdArray=array();
			foreach ($_POST['plos'] as $ploId)
			{
				array_push($plosIdArray,$ploId);	
			}
			$levelsIdArray=array();
			foreach ($_POST['levels'] as $levelId)
			{
				array_push($levelsIdArray,$levelId);	
			}
			for ($i=0; $i < count($_POST["shortname"]) ; $i++) {
				# code...
				$cloid = 0;
				$shortname=trim($_POST["shortname"][$i]);  $shortname=strtoupper($shortname);
				$idnumber=$coursecode."-".$shortname; $idnumber=strtoupper($idnumber);
				$description=trim($_POST["description"][$i]);
				$kpi=$_POST["kpi"][$i];
				$plo=$plosIdArray[$i];
				$level=$levelsIdArray[$i];
				$time = time();

				if($shortname == "")
				{
					goto down;
				}

				//query to check if clo of same name already entered
				$cloidnumbers=$DB->get_records_sql('SELECT * FROM  `mdl_competency` 
					WHERE competencyframeworkid = ? AND idnumber = ?',
					array($frameworkid,$idnumber));
				
				if($cloidnumbers == NULL) 
				{
					$record = new stdClass();
					$record->shortname = $shortname;
					$record->description = $description;
					$record->descriptionformat = 1;
					$record->idnumber = $idnumber;
					$record->competencyframeworkid = $frameworkid;
					$record->parentid = $plo;
					$record->path = '/0/';
					$record->sortorder = 0;
					$record->timecreated = $time;
					$record->timemodified = $time;
					$record->usermodified = $USER->id;
					
					$cloid = $DB->insert_record('competency', $record);
					
					//$sql="INSERT INTO mdl_competency (shortname, description, descriptionformat, idnumber, competencyframeworkid, parentid, path, sortorder, timecreated, timemodified, usermodified) VALUES ('$shortname', '$description', 1, '$idnumber',$frameworkid ,-2, '/0/', 0, '$time', '$time', $USER->id)";
					//$DB->execute($sql);
				}
				else
				{//echo $idnumber . "already exists<br>";
				
				}
				if($cloid){
					$sql="INSERT INTO mdl_taxonomy_clo_level (frameworkid, cloid, levelid) VALUES($frameworkid, $cloid, $level)";
					$DB->execute($sql);
					$sql="INSERT INTO mdl_clo_kpi (cloid, kpi) VALUES($cloid, $kpi)";
					$DB->execute($sql);
				}
				down:
			}

			if($_FILES['myfile']['size'] > 0){
				$revisions=$DB->get_records_sql('SELECT revision FROM `mdl_course_profile` where coursecode = ?', array($coursecode));
				$rev=0;
				if($revisions){
            		foreach ($revisions as $revision){
						$rev = $revision->revision; 
            		}
        		}
        		$rev++;
			    $file = rand(1000,100000)."-".$_FILES['myfile']['name'];
			    $file_loc = $_FILES['myfile']['tmp_name'];
			    $file_size = $_FILES['myfile']['size'];
			    $file_type = $_FILES['myfile']['type'];
			    if ($file_type == "application/pdf"){   
			        $blobObj = new Blob($x);
			        //test insert pdf
			        $blobObj->insertBlob($file_loc,"application/pdf",$coursecode,$rev);
			        echo "<font color = green>Course Profile Updated sucessfully!</font><br>";
			    }
			    else
			        echo "<font color=red>Incorrect File Type. Only PDFs are allowed</font>";
			}

			$redirect_page1='../index.php';
			redirect($redirect_page1);
		}

		/* delete code */
        elseif(isset($_GET['delete']))
        {
			$id_d=$_GET['delete'];
			$frameworkid=$_GET['fwid'];
			$rec=$DB->get_records_sql('SELECT shortname from mdl_competency_framework WHERE id=?', array($frameworkid));
			if($rec){
				foreach ($rec as $records){
				$framework_shortname = $records->shortname;
				}
			}
            $check=$DB->get_records_sql('SELECT * from mdl_competency_coursecomp where competencyid=?',array($id_d));
            if($check){
                $delmsg = "<font color='red'><b>The CLO cannot be deleted! Remove the mapping before CLO deletion.</b></font><br />";
                ?>
				<script>
					swal("Alert", "The CLO cannot be deleted! Remove the mapping before CLO deletion.", "info");
				</script>
				<?php
            }
            else
            {
                $sql_delete="DELETE from mdl_competency where id=$id_d";
                $DB->execute($sql_delete);
                $delmsg = "<font color='green'><b>CLO has been deleted!</b></font><br />";
                ?>
				<script>
				swal("CLO has been deleted!", {
						icon: "success",
						});
				</script>
				<?php
            }
        }
        /* /delete code */

		//Get all clos of selected framework
		$clos=$DB->get_records_sql('SELECT * FROM `mdl_competency` WHERE competencyframeworkid = ? AND idnumber LIKE "%%-%%%-clo%" ORDER BY idnumber', array($frameworkid));
        if($clos){
			$cloids = array(); $clocourses = array(); $clonames = array();
			//$revs = array();
            foreach ($clos as $records){
				$cloid = $records->id;
				$clocourse = $records->idnumber; $clocourse = substr($clocourse,0,6);
				$cloname = $records->shortname;
				$rev = $records->revision;
				array_push($cloids, $cloid); // array of clo ids
				array_push($clocourses, $clocourse); // array of clo course codes
				array_push($clonames, $cloname); // array of clo names
			
            }
         //   var_dump($clonames);
          //  echo "<br>";
           // var_dump($clocourses);
           // echo "<br>";
            //var_dump($revs); 

            //Loop to Filter-out old clos
            for($i=0 ; $i<(sizeof($clonames)*3); $i++)
            {

            	if(($clonames[$i] == $clonames [$i+1]) && ($clocourses[$i] == $clocourses[$i+1])) 
            	{

            		unset($clonames[$i]);
            		unset($clocourses[$i]);
            		 unset($cloids[$i]);
       		
            	} 
            }
         	
         	//Reindexing the arrays!
             $clonames = array_values($clonames);
			  $clocourses = array_values($clocourses);
			   $cloids = array_values($cloids);		 
		}

		//Get plo with its name and idnumber
		$plos=$DB->get_records_sql('SELECT * FROM  `mdl_competency` WHERE competencyframeworkid = ? AND idnumber LIKE "plo%" ', array($frameworkid));
		
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

		if(isset($msg3)){
			echo $msg3;
		}

		//echo "<div class='row'><div class='col-md-6'><a href='view_clos.php?fwid=$frameworkid'><h3>View Already Present CLOs</h3></a></div><div id='list' class='col-md-6'></div></div>";
		//echo "<div class='row'><div class='col-md-6'></div><div id='list' class='col-md-6'></div></div>";
		?>
		<br />

		<p id="msg">
		
		</p>
		
		<h3>Add New CLO</h3>
		<div class='row'><div class='col-md-9'>
		<form method='post' action="" class="mform" id="cloForm" enctype="multipart/form-data" >
			
			<div class="form-group row fitem ">
				<div class="col-md-3">
					<label class="col-form-label d-inline" for="id_clo">
						OBE framework
					</label>
				</div>
				<div class="col-md-9 form-inline felement">
					<?php echo $framework_shortname; ?>
				</div>
			</div>

			<div class="form-group row fitem">
				<div class="col-md-3">
					<span class="pull-xs-right text-nowrap">
						<abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
					</span>
					<label class="col-form-label d-inline" for="id_idnumber">
						Course code
					</label>
				</div>
				<div class="col-md-9 form-inline felement" data-fieldtype="text">
					<input type="text"
							class="form-control"
							name="idnumber"
							id="id_idnumber"
							size=""
							pattern="[a-zA-Z]{2}-[0-9]{3}"
							title="eg. CS-304"
							required
							placeholder="eg. CS-304"
							maxlength="100" type="text" > (eg. CS-304)
					<div class="form-control-feedback" id="id_error_idnumber">
					<?php
					if(isset($msg2)){
						echo $msg2;
					}
					?>
					</div>
				</div>
			</div>

			<div class="form-group row fitem">
				<div class="col-md-3">
					<label class="col-form-label d-inline" for="file">
						Upload Course Profile
					</label>
				</div>
				<div class="col-md-9 form-inline felement">
					<input type="file" name="myfile" id="file" class="form-control">
				</div>
			</div>
			
			<div id="dynamicInput">
			<div class="row">
				<div class="col-md-3"><b>Enter CLO</b></div>
				<div class="col-md-9"></div>
			</div>

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
							class="form-control"
							name="shortname[]"
							id="id_shortname"
							size=""
							pattern="[c/C][l/L][o/O]-[0-9]{1,}"
							title="eg. CLO-12"
							required
							placeholder="eg. CLO-12"
							maxlength="100" type="text" > (eg. CLO-12)
					<div class="form-control-feedback" id="id_error_shortname">
					<?php
					if(isset($msg1)){
						echo $msg1;
					}
					?>
					</div>
				</div>
			</div>
			
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
							<textarea id="id_description" name="description[]" class="form-control" rows="4" cols="80" spellcheck="true" ></textarea>
						</div>
					</div>
					<div class="form-control-feedback" id="id_error_description"  style="display: none;">
					</div>
				</div>
			</div>

			<div class="form-group row fitem ">
				<div class="col-md-3">
					<span class="pull-xs-right text-nowrap">
						<abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
					</span>
					<label class="col-form-label d-inline" for="id_kpi">
						KPI
					</label>
				</div>
				<div class="col-md-9 form-inline felement" data-fieldtype="number">
					<input type="number"
							class="form-control"
							name="kpi[]"
							id="id_kpi"
							size=""
							required
							placeholder="eg. 0.6"
							maxlength="100"
							step="0.001"
							min="0" max="1"> (eg. 0.6)
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
					<select  onChange="dropdownPlo(this.value, 0)" name="plos[]" class="select custom-select">
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
					<label class="col-form-label d-inline" for="id_level">
						Level
					</label>
				</div>
				<div class="col-md-9 form-inline felement">
					<select required onChange="dropdownLevel(this.value, 0)" name="levels[]" class="select custom-select">
						<option value=''>Choose..</option>
						<?php
						foreach ($recLevels as $recL) {
							$lid = $recL->id;
							$lvl = $recL->level;
							?>
							<option value="<?php echo $lid; ?>"><?php echo $lvl; ?></option>
						<?php
						}
						?>
					</select>
					<span id="dname0"></span>
					<span id="lname0"></span>
					<div class="form-control-feedback" id="id_error_level">
					</div>
				</div>
			</div>
			
			</div>

			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-9">
					<input class="btn btn-success" type="button" value="Add another CLO" onClick="addInput('dynamicInput');">
				</div>
			</div>
			<br />
			
			<input type="hidden" name="framework_shortname" value="<?php echo $framework_shortname; ?>"/>
			<input type="hidden" name="frameworkid" value="<?php echo $frameworkid; ?>"/>
			<button class="btn btn-info" type="submit"  name="save" id="button" /> Save and continue </button>
			<input class="btn btn-info" type="submit" name="return" value="Save and return"/>
            <a class="btn btn-default" type="submit" href="./select_frameworktoCLO.php">Cancel</a>
			<br /><br />
			<div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>
		</form>
		</div>
		<div id='list' class='col-md-3'></div>
		</div>

		
		<?php
		if(isset($_POST['save']) && !isset($msg3)){
		?>
		<script>
			document.getElementById("id_shortname").value = <?php echo json_encode($shortname); ?>;
			document.getElementById("id_description").value = <?php echo json_encode($description); ?>;
			document.getElementById("id_idnumber").value = <?php echo json_encode($idnumber); ?>;
		</script>
		<?php
		}
		?>
		
		<script>
		// script to create dynamic list of clos on course code input
		var frameworkid = <?php echo json_encode($frameworkid); ?>;
		var cloids = <?php echo json_encode($cloids); ?>;
		var clonames = <?php echo json_encode($clonames); ?>;
		var clocourses = <?php echo json_encode($clocourses); ?>;
		//var revs = <?php echo json_encode($revs)?>;

		$(document).ready(function(){
			$("#id_idnumber").keyup(function(){
				var n = $('#id_idnumber').val().toUpperCase();
				var cnames = ""; var flag = 0;
				for (var i = 0; i < clonames.length; ++i) {
					if(clocourses[i] == n){
						flag = 1;
						cnames += clonames[i] + " <a href='edit_clo.php?edit="+cloids[i]+"&fwid="+frameworkid+"' title='Edit'><img src='../img/icons/edit.png' /></a> <a href='add_clo.php?delete="+cloids[i]+"&fwid="+frameworkid+"' onClick=\"return confirm('Delete CLO?')\" title='Delete'><img src='../img/icons/delete.png' /></a><br />";
					}
				}
				if(flag == 0){
					cnames = "<font color='red'>-No CLOs found!</font>";
				}
				$("#list").html("<font color='green'><b>Present CLOs for " + n + ":</font></b><br />" + cnames);
				/*
				<a href='edit_clo.php?edit=$id&fwid=$fw_id' title='Edit'><img src='../img/icons/edit.png' /></a>
				<a href='view_clos.php?delete=$id&fwid=$fw_id' onClick=\"return confirm('Delete CLO?')\" title='Delete'><img src='../img/icons/delete.png' /></a>
				*/
			});
		});
		</script>
		
		<script>
			// script to add name, desc, kpi, plo and level fields to form
			var i = 1;
			var levelid = <?php echo json_encode($levelid); ?>;
			var lshortnames = <?php echo json_encode($lvlshortname); ?>;
			var ploId = <?php echo json_encode($ploIdArray); ?>;
			var ploIdNumber = <?php echo json_encode($ploIdnumberArray); ?>;
			
			function addInput(divName){
				var newdiv = document.createElement('div');
				newdiv.innerHTML = '<div class="row"><div class="col-md-3"><b>Enter CLO</b></div><div class="col-md-9"></div></div>';
				document.getElementById(divName).appendChild(newdiv);

				var newdiv1 = document.createElement('div');
				newdiv1.innerHTML = '<div class="form-group row fitem "><div class="col-md-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_shortname">Name</label></div><div class="col-md-9 form-inline felement" data-fieldtype="text"><input type="text" class="form-control" name="shortname[]" id="id_shortname" size="" pattern="[c/C][l/L][o/O]-[0-9]{1,}" title="eg. CLO-12" required placeholder="eg. CLO-12" maxlength="100" type="text" > (eg. CLO-12)<div class="form-control-feedback" id="id_error_shortname"><?php if(isset($msg1)){echo $msg1;} ?></div></div></div>';
				document.getElementById(divName).appendChild(newdiv1);

				var newdiv2 = document.createElement('div');
				newdiv2.innerHTML = '<div class="form-group row fitem"><div class="col-md-3"><span class="pull-xs-right text-nowrap"></span><label class="col-form-label d-inline" for="id_description">Description</label></div><div class="col-md-9 form-inline felement" data-fieldtype="editor"><div><div><textarea id="id_description" name="description[]" class="form-control" rows="4" cols="80" spellcheck="true" ></textarea></div></div><div class="form-control-feedback" id="id_error_description"  style="display: none;"></div></div></div>';
				document.getElementById(divName).appendChild(newdiv2);

				var newdiv3 = document.createElement('div');
				newdiv3.innerHTML = '<div class="form-group row fitem "><div class="col-md-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_kpi">KPI</label></div><div class="col-md-9 form-inline felement" data-fieldtype="number"><input type="number" class="form-control" name="kpi[]" id="id_kpi" size="" required placeholder="eg. 0.6" maxlength="100" step="0.001" min="0" max="1"> (eg. 0.6)<div class="form-control-feedback" id="id_error_kpi"></div></div></div>';
				document.getElementById(divName).appendChild(newdiv3);
				
				//Create select element for PLO selection
				var selectPLO = document.createElement("select");
				selectPLO.className = "select custom-select";
				selectPLO.name = "plos[]";
				jsFuncVal = "dropdownPlo(this.value, "+i+")";
				selectPLO.setAttribute("required", "required");
				selectPLO.setAttribute("onChange", jsFuncVal);

				//Create and append the options
				var option = document.createElement("option");
				option.value = "";
				option.text = "Choose..";
				selectPLO.appendChild(option);
				for (var l = 0; l < ploIdNumber.length; l++) {
					var option = document.createElement("option");
					option.value = ploId[l];
					option.text = ploIdNumber[l];
					selectPLO.appendChild(option);
				}

				var newdivforselectPLO = document.createElement('div');
				newdivforselectPLO.appendChild(selectPLO);

				var newdiv4 = document.createElement('div');
				newdiv4.innerHTML = '<div class="form-group row fitem "><div class="col-md-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_plo">Map to PLO</label></div><div class="col-md-9 form-inline felement">'+newdivforselectPLO.innerHTML+' <span id="plosidnumber'+i+'"></span><div class="form-control-feedback" id="id_error_plo"></div></div></div>';
				document.getElementById(divName).appendChild(newdiv4);
				
				//Create select element for Level selection
				var selectLevel = document.createElement("select");
				selectLevel.className = "select custom-select";
				selectLevel.name = "levels[]";
				jsFuncVal = "dropdownLevel(this.value, "+i+")";
				selectLevel.setAttribute("required", "required");
				selectLevel.setAttribute("onChange", jsFuncVal);

				//Create and append the options
				var option = document.createElement("option");
				option.value = "";
				option.text = "Choose..";
				selectLevel.appendChild(option);
				for (var l = 0; l < lshortnames.length; l++) {
					var option = document.createElement("option");
					option.value = levelid[l];
					option.text = lshortnames[l];
					selectLevel.appendChild(option);
				}

				var newdivforselectLevel = document.createElement('div');
				newdivforselectLevel.appendChild(selectLevel);
				
				var newdiv5 = document.createElement('div');
				newdiv5.innerHTML = '<div class="form-group row fitem "><div class="col-md-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_level">Level</label></div><div class="col-md-9 form-inline felement">'+newdivforselectLevel.innerHTML+' <span id="dname'+i+'"></span> <span id="lname'+i+'"></span><div class="form-control-feedback" id="id_error_level"></div></div></div>';
				document.getElementById(divName).appendChild(newdiv5);
				
				i++;
			}
		</script>

		<script>
			// display plo name of particular plo
			var ploIdName = <?php echo json_encode($ploNameArray); ?>;
			var ploId = <?php echo json_encode($ploIdArray); ?>;
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
			function dropdownLevel(value,id){
				var lname = "lname" + id;
				var dname = "dname" + id;
				//console.log(value);
				//console.log(id);
				if(value == ''){
					document.getElementById(lname).innerHTML = "";
					document.getElementById(dname).innerHTML = "";
				}
				else{
					for(var i=0; i<levelid.length ; i++){
						if(levelid[i] == value){
							document.getElementById(dname).innerHTML = dnames[i];
							document.getElementById(lname).innerHTML = "("+lnames[i]+")";
							break;
						}
					}
				}
			}
		</script>
		
		<?php 
			echo $OUTPUT->footer();
    
	}
	else
	{?>
		<h3 style="color:red;"> Invalid Selection </h3>
    	<a href="./select_frameworktoCLO.php">Back</a>
    	<?php
        echo $OUTPUT->footer();
    }?>


<?php

class Blob{
	
    const DB_HOST = 'localhost';
    const DB_NAME = 'bitnami_moodle';
    const DB_USER = 'bn_moodle';
    protected $DB_PASSWORD='';
 
    /**
     * Open the database connection
     */
    public function __construct($x) {
    	//echo "$x";
    	$DB_PASSWORD=$x;
        // open database connection
        $conStr = sprintf("mysql:host=%s;dbname=%s;charset=utf8", self::DB_HOST, self::DB_NAME);
 
        try {
            $this->pdo = new PDO($conStr, self::DB_USER, $DB_PASSWORD);
            //for prior PHP 5.3.6
            //$conn->exec("set names utf8");
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
 
 /**
     * insert blob into the files table
     * @param string $filePath
     * @param string $mime mimetype
     * @return bool
     */
    public function insertBlob($filePath, $mime,$coursecode,$rev) {
        $blob = fopen($filePath, 'rb');
       // $coursecode=$SESSION->coursecode;
        //echo "$coursecode";
			
 
        $sql = "INSERT INTO mdl_course_profile (coursecode,mime,data,revision) VALUES('$coursecode',:mime,:data,'$rev')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':mime', $mime);
        $stmt->bindParam(':data', $blob, PDO::PARAM_LOB);
 
        return $stmt->execute();
    }



	public function selectBlob($id) {
 
        $sql = "SELECT mime,
                data
                FROM mdl_course_profile
                WHERE id = :id;";
 
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array(":id" => $id));
        $stmt->bindColumn(1, $mime);
        $stmt->bindColumn(2, $data, PDO::PARAM_LOB);
 
        $stmt->fetch(PDO::FETCH_BOUND);
 
        return array("mime" => $mime,
            "data" => $data);
    }

    /**
     * close the database connection
     */
    public function __destruct() {
        // close the database connection
        $this->pdo = null;
    }
 
}
?>
