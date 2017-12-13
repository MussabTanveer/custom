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
	
		if(isset($_POST['save'])){
			$shortname=trim($_POST['shortname']);
			$description=trim($_POST['description']);
			$idnumber=trim($_POST['idnumber']); $idnumber=strtoupper($idnumber);
			$frameworkid=$_POST['frameworkid'];
			$framework_shortname=$_POST['framework_shortname'];
			$time = time();
			if(empty($shortname) || empty($idnumber))
			{
				if(empty($shortname))
				{
					$msg1="<font color='red'>-Please enter CLO name</font>";
				}
				if(empty($idnumber))
				{
					$msg2="<font color='red'>-Please enter ID number</font>";
				}
			}
			/*elseif(preg_match('/^[a-zA-Z]{2}-\d{3}-(c|C)(l|L)(o|O)-\d{1,}$/',$idnumber))
			{
				$msg2="<font color='red'>-Please match the format eg. CS-304-CLO-1</font>";
			}*/
			else{
				//echo $shortname;
				//echo $description;
				//echo $idnumber;
				$check=$DB->get_records_sql('SELECT * from mdl_competency WHERE idnumber=? AND competencyframeworkid=?', array($idnumber, $frameworkid));
				if(count($check)){
					$msg2="<font color='red'>-Please enter UNIQUE ID number</font>";
				}
				
				else{
					$sql="INSERT INTO mdl_competency (shortname, description, descriptionformat, idnumber, competencyframeworkid, parentid, path, sortorder, timecreated, timemodified, usermodified) VALUES ('$shortname', '$description', 1, '$idnumber',$frameworkid ,-2, '/0/', 0, '$time', '$time', $USER->id)";
					$DB->execute($sql);
					$msg3 = "<font color='green'><b>CLO successfully defined!</b></font><br /><p><b>Add another below.</b></p>";
				}
			}
		}
		

		elseif(isset($_POST['return'])){
			$shortname=trim($_POST['shortname']);
			$description=trim($_POST['description']);
			$idnumber=trim($_POST['idnumber']); $idnumber=strtoupper($idnumber);
			$frameworkid=$_POST['frameworkid'];
			$framework_shortname=$_POST['framework_shortname'];
			$time = time();
			if(empty($shortname) || empty($idnumber))
			{
				if(empty($shortname))
				{
					$msg1="<font color='red'>-Please enter CLO name</font>";
				}
				if(empty($idnumber))
				{
					$msg2="<font color='red'>-Please enter ID number</font>";
				}
			}
			/*elseif(preg_match('/^[a-zA-Z]{2}-\d{3}-(c|C)(l|L)(o|O)-\d{1,}$/',$idnumber))
			{
				$msg2="<font color='red'>-Please match the format eg. CS-304-CLO-1</font>";
			}*/
			else{
				//echo $shortname;
				//echo $description;
				//echo $idnumber;
				$check=$DB->get_records_sql('SELECT * from mdl_competency WHERE idnumber=? AND competencyframeworkid=?', array($idnumber, $frameworkid));
				if(count($check)){
					$msg2="<font color='red'>-Please enter UNIQUE ID number</font>";
				}
				
				else{
					$sql="INSERT INTO mdl_competency (shortname, description, descriptionformat, idnumber, competencyframeworkid, parentid, path, sortorder, timecreated, timemodified, usermodified) VALUES ('$shortname', '$description', 1, '$idnumber',$frameworkid ,-2, '/0/', 0, '$time', '$time', $USER->id)";
					$DB->execute($sql);
					$msg3 = "<font color='green'><b>CLO successfully defined!</b></font><br /><p><b>Add another below.</b></p>";
				}
			}

			  $redirect_page1='./report_main.php';
              redirect($redirect_page1); 
		}

		if(isset($msg3)){
			echo $msg3;
		}
		echo "<a href='view_clos.php?fwid=$frameworkid'><h3>View Already Present CLOs</h3></a>";
		?>
		<br />
		<h3>Add New CLO</h3>
		<form method='post' action="" class="mform">
			
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
						ID number
					</label>
				</div>
				<div class="col-md-9 form-inline felement" data-fieldtype="text">
					<input type="text"
							class="form-control"
							name="idnumber"
							id="id_idnumber"
							size=""
							pattern="[a-zA-Z]{2}-[0-9]{3}-[c/C][l/L][o/O]-[0-9]{1,}"
							title="eg. CS-304-CLO-12"
							required
							placeholder="eg. CS-304-CLO-12"
							maxlength="100" type="text" > (eg. CS-304-CLO-12)
					<div class="form-control-feedback" id="id_error_idnumber">
					<?php
					if(isset($msg2)){
						echo $msg2;
					}
					?>
					</div>
				</div>
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
							name="shortname"
							id="id_shortname"
							size=""
							required
							maxlength="100" type="text" >
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
							<textarea id="id_description" name="description" class="form-control" rows="4" cols="80" spellcheck="true" ></textarea>
						</div>
					</div>
					<div class="form-control-feedback" id="id_error_description"  style="display: none;">
					</div>
				</div>
			</div>
			
			<input type="hidden" name="framework_shortname" value="<?php echo $framework_shortname; ?>"/>
			<input type="hidden" name="frameworkid" value="<?php echo $frameworkid; ?>"/>
			<input class="btn btn-info" type="submit" name="save" value="Save and continue"/>
			<input class="btn btn-info" type="submit" name="return" value="Save and return"/>
            <a class="btn btn-default" type="submit" href="./select_frameworktoCLO.php">Cancel</a>

		</form>
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
		<br />
		<div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>
					
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
