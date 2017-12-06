<?php
    require_once('../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Add OBE Framework");
    $PAGE->set_heading("OBE Frameworks");
    $PAGE->set_url($CFG->wwwroot.'/custom/add_framework.php');
    
    echo $OUTPUT->header();
	require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());
	
	if(isset($_POST['save'])){
		$shortname=$_POST['shortname'];
		$description=$_POST['description'];
		$idnumber=$_POST['idnumber']; $idnumber=strtoupper($idnumber);
		$time = time();
		
		if(empty($shortname) || empty($idnumber))
		{
			if(empty($shortname))
			{
				$msg1="<font color='red'>-Please enter framework name</font>";
			}
			if(empty($idnumber))
			{
				$msg2="<font color='red'>-Please enter ID number</font>";
			}
		}
		else{
			//echo $shortname;
			//echo $description;
			//echo $idnumber;
			$check=$DB->get_records_sql('SELECT * from mdl_competency_framework WHERE idnumber=?', array($idnumber));
			if(count($check)){
				$msg2="<font color='red'>-Please enter UNIQUE ID number</font>";
			}
			else{
				$sql="INSERT INTO mdl_competency_framework (shortname, description, idnumber, contextid, descriptionformat, scaleid, scaleconfiguration, taxonomies, timecreated, timemodified, usermodified) VALUES ('$shortname', '$description', '$idnumber', 1, 1, 2, '[{\"scaleid\":\"2\"},{\"id\":1,\"scaledefault\":1,\"proficient\":1},{\"id\":2,\"scaledefault\":0,\"proficient\":1}]', 'outcome,outcome,outcome,outcome', '$time', '$time', $USER->id)";
				$DB->execute($sql);
				$msg3 = "<font color='green'><b>Framework successfully added!</b></font><br />";
			}
		}
	}

elseif(isset($_POST['return'])){
		$shortname=$_POST['shortname'];
		$description=$_POST['description'];
		$idnumber=$_POST['idnumber']; $idnumber=strtoupper($idnumber);
		$time = time();
		
		if(empty($shortname) || empty($idnumber))
		{
			if(empty($shortname))
			{
				$msg1="<font color='red'>-Please enter framework name</font>";
			}
			if(empty($idnumber))
			{
				$msg2="<font color='red'>-Please enter ID number</font>";
			}
		}
		else{
			//echo $shortname;
			//echo $description;
			//echo $idnumber;
			$check=$DB->get_records_sql('SELECT * from mdl_competency_framework WHERE idnumber=?', array($idnumber));
			if(count($check)){
				$msg2="<font color='red'>-Please enter UNIQUE ID number</font>";
			}
			else{
				$sql="INSERT INTO mdl_competency_framework (shortname, description, idnumber, contextid, descriptionformat, scaleid, scaleconfiguration, taxonomies, timecreated, timemodified, usermodified) VALUES ('$shortname', '$description', '$idnumber', 1, 1, 2, '[{\"scaleid\":\"2\"},{\"id\":1,\"scaledefault\":1,\"proficient\":1},{\"id\":2,\"scaledefault\":0,\"proficient\":1}]', 'outcome,outcome,outcome,outcome', '$time', '$time', $USER->id)";
				$DB->execute($sql);
				$msg3 = "<font color='green'><b>Framework successfully added!</b></font><br />";
			}
		}


		 $redirect_page1='./report_main.php';
              redirect($redirect_page1); 
	
}






	$rec=$DB->get_records_sql('SELECT id,shortname from mdl_competency_framework');
	
	if($rec){
		$i = 1;
		echo "<h3>Already Present Frameworks</h3>";
		foreach ($rec as $records){
			$shortname1 = $records->shortname;
			$id=$records->id;
			echo "<div class='row'><div class='col-md-2 col-sm-4 col-xs-8'>$i. $shortname1</div> <div class='col-md-10 col-sm-8 col-xs-4'><a href='edit_framework.php?edit=$id' title='Edit'><img src='./img/icons/edit.png' /></a> <a href='delete_framework.php?delete=$id' title='Delete'><img src='./img/icons/delete.png' /></a></div></div>"; //link to edit_framework.php
			$i++;
		}
	}

	if(isset($msg3)){
		echo $msg3;
	}
	
	?>
	<br />
	<h3>Add New Framework</h3>
	<form method='post' action="" class="mform">
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

		
		<input class="btn btn-info" type="submit" name="save" value="Save and continue"/>
        <input class="btn btn-info" type="submit" name="return" value="Save and return"/>
            <a     class="btn btn-info"   type="submit"   href="./report_admin.php">Cancel</a>


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
    ?>
