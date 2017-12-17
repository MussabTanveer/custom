<?php
require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Add OBE CLOs");
    $PAGE->set_heading("Add Course Learning Outcome (CLO)");
    $PAGE->set_url($CFG->wwwroot.'/custom/add_clo.php');
   echo $OUTPUT->header();
	require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());
	

    ?>

<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/jquery/jquery-2.1.3.js"></script>
<script type="text/javascript" >

	$(document).ready(function(){
    $("button").click(function(){
        var formdata = $("form").serialize();
			$.ajax({
			    type: "POST",
			    url: "save_clo.php",
			    data: formdata,
			    success:function(){
           	document.getElementById("msg").innerHTML ="<font color='green'>CLO successfully defined!</font>"
        }

			 });
			return false;

    });
});
</script>


<?php
    
    
	if((isset($_POST['submit']) && isset( $_POST['frameworkid'])) || (isset($SESSION->fid3) && $SESSION->fid3 != "xyz") || isset($_POST['save']) || isset($_POST['return']))
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

		elseif(isset($_POST['return'])) {


		$coursecode = trim($_POST["idnumber"]); $coursecode=strtoupper($coursecode);
		$frameworkid = $_POST["frameworkid"];

		

		
	for ($i=0; $i <count($_POST["shortname"]) ; $i++) { 
		# code...
		$idnumber=$coursecode."-".$_POST["shortname"][$i]; $idnumber=strtoupper($idnumber);
		//echo $idnumber. "<br>";
		$shortname=$_POST["shortname"][$i];  $shortname=strtoupper($shortname);
		$description=trim($_POST["description"][$i]);

		$time = time();
		$cloidnumbers=$DB->get_records_sql('SELECT * FROM  `mdl_competency` 
    		WHERE competencyframeworkid = ? AND idnumber = ?',
    		 array($frameworkid,$idnumber));

		if($cloidnumbers == NULL) 
			{


			$sql="INSERT INTO mdl_competency (shortname, description, descriptionformat, idnumber, competencyframeworkid, parentid, path, sortorder, timecreated, timemodified, usermodified) VALUES ('$shortname', '$description', 1,     '$idnumber',$frameworkid ,-2, '/0/', 0, '$time', '$time', $USER->id)";
		
		$DB->execute($sql);

		}
		else 
		{//echo $idnumber . "already exists<br>";
		
			}

			}

		$redirect_page1='../index.php';
        redirect($redirect_page1); 
		}

		$clos=$DB->get_records_sql('SELECT * FROM `mdl_competency` WHERE competencyframeworkid = ? AND idnumber LIKE "%%-%%%-clo%" ORDER BY idnumber', array($frameworkid));
        
        if($clos){
			$clocourses = array(); $clonames = array();
            foreach ($clos as $records){
				$clocourse = $records->idnumber; $clocourse = substr($clocourse,0,6);
				$cloname = $records->shortname;
				array_push($clocourses, $clocourse); // array of clo course codes
				array_push($clonames, $cloname); // array of clo names
				//echo "$clocourse   $cloname <br>";
            }
        }

		if(isset($msg3)){
			echo $msg3;
		}

		echo "<div class='row'><div class='col-md-6'><a href='view_clos.php?fwid=$frameworkid'><h3>View Already Present CLOs</h3></a></div><div id='list' class='col-md-6'></div></div>";
		?>
		<br />

		<p id="msg">
		

		</p>
		
		<h3>Add New CLO</h3>
		<form method='post' action="" class="mform" id="cloForm" >
			
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
			<button class="btn btn-info" type="submit"  name="save" /> Save and continue </button>
			<input class="btn btn-info" type="submit" name="return" value="Save and return"/>
            <a class="btn btn-default" type="submit" href="./select_frameworktoCLO.php">Cancel</a>

		</form>
		<br />
		<div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>



		
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
		var clonames = <?php echo json_encode($clonames); ?>;
		var clocourses = <?php echo json_encode($clocourses); ?>;
		$(document).ready(function(){
			$("#id_idnumber").change(function(){
				var n = $('#id_idnumber').val().toUpperCase();
				var cnames = ""; var flag = 0;
				for (var i = 0; i < clonames.length; ++i) {
					if(clocourses[i] == n){
						flag = 1;
						cnames += clonames[i] + "<br />";
					}
				}
				if(flag == 0){
					cnames = "<font color='red'>-No CLOs found!</font>";
				}
				$("#list").html("<font color='green'><b>Present CLOs for " + n + ":</font></b><br />" + cnames);
			});
		});
		</script>
		
		<script>
			// script to add name and desc fields to form
			//var counter = 1;
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
				counter++;
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
