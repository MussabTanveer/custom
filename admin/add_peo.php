<script src="../script/sweet-alert/sweetalert.min.js"></script>
<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Add OBE PEOs");
    $PAGE->set_heading("Add Program Educational Objective (PEO)");
    $PAGE->set_url($CFG->wwwroot.'/custom/add_peo.php');
    
    echo $OUTPUT->header();
	require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());
	
	if((isset($_POST['submit']) && isset( $_POST['fwid'])) || (isset($SESSION->fid1) && $SESSION->fid1 != "xyz") || isset($_POST['save']) || isset($_POST['return']) || isset($_GET['fwid']))
    {
		if(isset($_POST['submit']) || (isset($SESSION->fid1) && $SESSION->fid1 != "xyz") || isset($_GET['fwid'])){
			if(isset($SESSION->fid1) && $SESSION->fid1 != "xyz")
			{
				$fw_id=$SESSION->fid1;
				$SESSION->fid1 = "xyz";
			}
			elseif(isset( $_POST['frameworkid']))
			{
				$fw_id=$_POST['fwid'];
				//echo "$frameworkid";
			}
			else
			{
				$fw_id=$_GET['fwid'];
				//echo "$frameworkid";
			}

			$rec=$DB->get_records_sql('SELECT shortname from mdl_competency_framework WHERE id=?', array($fw_id));
			if($rec){
				foreach ($rec as $records){
					$fw_shortname = $records->shortname;
				}
			}
		}
	
		if(isset($_POST['return'])){
			$shortname=trim($_POST['shortname']);
			$description=trim($_POST['description']);
			$idnumber=trim($_POST['idnumber']); $idnumber=strtoupper($idnumber);
			$fw_id=$_POST['fid'];
			$fw_shortname=$_POST['fname'];
			$time = time();
			
			if(empty($shortname) || empty($idnumber))
			{
				if(empty($shortname))
				{
					$msg1="<font color='red'>-Please enter PEO name</font>";
				}
				if(empty($idnumber))
				{
					$msg2="<font color='red'>-Please enter ID number</font>";
				}
			}
			elseif(substr($idnumber,0,4) != 'PEO-')
			{
				$msg2="<font color='red'>-The ID number must start with PEO-</font>";
			}
			else{
				//echo $shortname;
				//echo $description;
				//echo $idnumber;
				$check=$DB->get_records_sql('SELECT * from mdl_competency WHERE idnumber=? AND competencyframeworkid=?', array($idnumber, $fw_id));
				if(count($check)){
					$msg2="<font color='red'>-Please enter UNIQUE ID number</font>";
				}
				else{
					$sql="INSERT INTO mdl_competency (shortname, description, descriptionformat, idnumber, competencyframeworkid, path, sortorder, timecreated, timemodified, usermodified) VALUES ('$shortname', '$description', 1, '$idnumber', '$fw_id', '/0/', 0, '$time', '$time', $USER->id)";
					$DB->execute($sql);
					$msg3 = "<font color='green'><b>PEO successfully defined!</b></font><br /><p><b>Add another below.</b></p>";
				}
			}
            $redirect_page1='./report_main.php';
			redirect($redirect_page1); 
		}

        elseif(isset($_POST['save'])){
			$shortname=trim($_POST['shortname']);
			$description=trim($_POST['description']);
			$idnumber=trim($_POST['idnumber']); $idnumber=strtoupper($idnumber);
			$fw_id=$_POST['fid'];
			$fw_shortname=$_POST['fname'];
			$time = time();
			
			if(empty($shortname) || empty($idnumber))
			{
				if(empty($shortname))
				{
					$msg1="<font color='red'>-Please enter PEO name</font>";
				}
				if(empty($idnumber))
				{
					$msg2="<font color='red'>-Please enter ID number</font>";
				}
			}
			elseif(substr($idnumber,0,4) != 'PEO-')
			{
				$msg2="<font color='red'>-The ID number must start with PEO-</font>";
			}
			else{
				//echo $shortname;
				//echo $description;
				//echo $idnumber;
				$check=$DB->get_records_sql('SELECT * from mdl_competency WHERE idnumber=? AND competencyframeworkid=?', array($idnumber, $fw_id));
				if(count($check)){
					$msg2="<font color='red'>-Please enter UNIQUE ID number</font>";
				}
				else{
					$sql="INSERT INTO mdl_competency (shortname, description, descriptionformat, idnumber, competencyframeworkid, path, sortorder, timecreated, timemodified, usermodified) VALUES ('$shortname', '$description', 1, '$idnumber', '$fw_id', '/0/', 0, '$time', '$time', $USER->id)";
					$DB->execute($sql);
					$msg3 = "<font color='green'><b>PEO successfully defined!</b></font><br /><p><b>Add another below.</b></p>";
				}
			}
		}
		/* delete code */
		elseif(isset($_GET['delete']) && isset($_GET['fwid'])){
			$id_d=$_GET['delete'];
			$fw_id=$_GET['fwid'];
			$check=$DB->get_records_sql('SELECT * FROM mdl_competency WHERE parentid=? and competencyframeworkid=?',array($id_d,$fw_id));
			if($check){
				$delmsg = "<font color='red'><b>The PEO cannot be deleted! Remove the mapping before PEO deletion.</b></font><br />";
				?>
				<script>
				swal("Alert", "The PEO cannot be deleted! Remove the mapping before PEO deletion.", "info");
				</script>
				<?php
			}
			else{
				$sql_delete="DELETE from mdl_competency where id=$id_d";
				$DB->execute($sql_delete);
				$delmsg = "<font color='green'><b>PEO has been deleted!</b></font><br />";
				?>
				<script>
				swal("PEO has been deleted!", {
						icon: "success",
						});
				</script>
				<?php
			}
		}
		/* /delete code */

		$peos=$DB->get_records_sql('SELECT * FROM `mdl_competency` WHERE competencyframeworkid = ? AND parentid = 0', array($fw_id));
		
		if($peos){
			$i = 1;
			echo "<h3>Already Present PEOs</h3>";
			foreach ($peos as $records){
				$shortname1 = $records->shortname;
				$id=$records->id;
				echo "<div class='row'>
						<div class='col-md-2 col-sm-4 col-xs-8'>$i. $shortname1</div>
						<div class='col-md-10 col-sm-8 col-xs-4'>
							<a href='edit_peo.php?edit=$id&fwid=$fw_id' title='Edit'><img src='../img/icons/edit.png' /></a>
							<a href='add_peo.php?delete=$id&fwid=$fw_id' onClick=\"return confirm('Delete PEO?')\" title='Delete'><img src='../img/icons/delete.png' /></a>
		                </div>
        	          </div>";
				$i++;
			}
		}

		if(isset($msg3)){
			echo $msg3;
		}
		/*
		if(isset($delmsg)){
			echo $delmsg;
		}
		*/
		
		?>
		<br />
		<h3>Add New PEO</h3>
		<form method='post' action="" class="mform">
			
			<div class="form-group row fitem ">
				<div class="col-md-3">
					<label class="col-form-label d-inline" for="id_framework">
						OBE framework
					</label>
				</div>
				<div class="col-md-9 form-inline felement">
					<?php echo $fw_shortname; ?>
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
							class="form-control "
							name="idnumber"
							id="id_idnumber"
							size=""
							pattern="[p/P][e/E][o/O]-[0-9]{1,}"
							title="eg. PEO-3"
							placeholder="eg. PEO-3"
							required
							maxlength="100" type="text" > (eg. PEO-3)
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
							class="form-control "
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

			<input type="hidden" name="fname" value="<?php echo $fw_shortname; ?>"/>
			<input type="hidden" name="fid" value="<?php echo $fw_id; ?>"/>
			<input class="btn btn-info" type="submit" name="save" value="Save and continue"/>
			<input class="btn btn-info" type="submit" name="return" value="Save and return"/>
            <a class="btn btn-default" type="submit" href="./select_frameworktoPEO.php">Cancel</a>
			
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
        <a href="./select_frameworktoPEO.php">Back</a>
    	<?php
        echo $OUTPUT->footer();
    }?>
