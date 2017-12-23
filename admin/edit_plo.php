<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Edit OBE PLOs");
    $PAGE->set_heading("Edit PLO");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/admin/edit_plo.php');
    
    echo $OUTPUT->header();
	require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());
		
		
	if(isset($_GET['edit']) && isset($_GET['fwid']))
	{
		$id=$_GET['edit'];
		$fw_id=$_GET['fwid'];
		
		if(isset($_POST['save']))
		{	
			$shortname=$_POST['shortname'];
			$description=$_POST['description'];
			$idnumber=$_POST['idnumber']; $idnumber=strtoupper($idnumber);
			$time = time();

			if(empty($shortname) || empty($idnumber))
			{
				if(empty($shortname))
				{
					$msg1="<font color='red'>-Please enter PLO name</font>";
				}
				if(empty($idnumber))
				{
					$msg2="<font color='red'>-Please enter ID number</font>";
				}
			}
			elseif(substr($idnumber,0,4) != 'PLO-')
			{
				$msg2="<font color='red'>-The ID number must start with PLO-</font>";
			}
			else{
		
				$check=$DB->get_records_sql('SELECT * from mdl_competency WHERE idnumber=? AND competencyframeworkid=? AND id!=?', array($idnumber,$fw_id,$id));
				if(count($check)){
					$msg2="<font color='red'>-Please enter UNIQUE ID number</font>";
				}
				else{
					$sql_update="UPDATE mdl_competency SET shortname='$shortname',description='$description',idnumber='$idnumber', timemodified='$time', usermodified=$USER->id WHERE id=$id";
					$DB->execute($sql_update);
					$msg3 = "<font color='green'><b>PLO successfully updated!</b></font><br />";
				}
			}
		}
		
		if(isset($msg3)){
			echo $msg3;
			goto label;
		}
			
			
	?>
	
	<br />
	<h3>Edit PLO</h3>
	<form method='post' action="" class="mform">
		
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
						pattern="[p/P][l/L][o/O]-[0-9]{1,}"
						title="eg. PLO-1"
						required
						maxlength="100" type="text" >
				<div class="form-control-feedback" id="id_error_idnumber">
				<?php
				if(isset($msg2)){
					echo $msg2;
				}
				?>
				</div>
			</div>
		</div>
		
		<input class="btn btn-info" type="submit" name="save" value="Save"/>
	</form>
	
	<?php
		if(isset($_GET['edit'])){
		?>
		<?php
		$id=$_GET['edit'];
		$rec=$DB->get_records_sql('SELECT shortname,description,idnumber FROM mdl_competency WHERE id=?',array($id));
		if($rec){
			foreach ($rec as $records){
				$shortname=$records->shortname;
				$description=$records->description;
				$idnumber=$records->idnumber;
			}
		}
		
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
        label:
        ?>
        <div class="btn-btn-info"><br><a href="./select_frameworktoPLO.php" >Back</a></div>
        <?php
        echo $OUTPUT->footer();
	}
	else
    {?>
        <h3 style="color:red;"> Invalid Selection </h3>
        <a href="./select_frameworktoPLO.php">Back</a>
    	<?php
        echo $OUTPUT->footer();
    }?>