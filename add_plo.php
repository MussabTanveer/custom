<?php
    require_once('../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Add OBE PLOs");
    $PAGE->set_heading("Add Program Learning Outcome (PLO)");
    $PAGE->set_url($CFG->wwwroot.'/custom/add_plo.php');
    
    echo $OUTPUT->header();
	require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());
	
	if((isset($_POST['submit']) && isset( $_POST['frameworkid'])) || (isset($SESSION->fid2) && $SESSION->fid2 != "xyz") || isset($_POST['save']))
	{
		if(isset($_POST['submit']) || (isset($SESSION->fid2) && $SESSION->fid2 != "xyz")){
			if(isset($SESSION->fid2) && $SESSION->fid2 != "xyz")
			{
				$frameworkid=$SESSION->fid2;
				$SESSION->fid2 = "xyz";
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
             echo $shortname;
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
				$msg2="<font color='red'>-The ID number must start with PLO</font>";
			}
			else{
				//echo $shortname;
				//echo $description;
				//echo $idnumber;
				$check=$DB->get_records_sql('SELECT * from mdl_competency WHERE idnumber=? AND competencyframeworkid=?', array($idnumber, $frameworkid));
				echo $idnumber;
				if(count($check)){
					$msg2="<font color='red'>-Please enter UNIQUE ID number</font>";
				}
				else{
					$sql="INSERT INTO mdl_competency (shortname, description, descriptionformat, idnumber,competencyframeworkid, parentid, path, sortorder, timecreated, timemodified, usermodified) VALUES ('$shortname', '$description', 1, '$idnumber',$frameworkid ,-1, '/0/', 0, '$time', '$time', $USER->id)";
					$DB->execute($sql);
					$msg3 = "<font color='green'><b>PLO successfully defined!</b></font><br /><p><b>Add another below.</b></p>";
				}
			}
		}
		
			$plos=$DB->get_records_sql('SELECT id,shortname FROM  `mdl_competency` 
    		WHERE competencyframeworkid = ? 
    		AND idnumber LIKE "plo%" ',
    		 array($frameworkid));
			 
			 if($plos){
				 $i = 1;
				 echo "<h3>Already Present PLOs In Framework</h3>";
				 foreach ($plos as $records){
				 $shortname1 = $records->shortname;
			     $id=$records->id;
				echo "<div class='row'><div class='col-md-2 col-sm-4 col-xs-8'>$i. $shortname1</div> <div class='col-md-10 col-sm-8 col-xs-4'><a href='edit_plo.php?edit=$id&fwid=$frameworkid' title='Edit'><img src='./img/icons/edit.png' /></a> <a href='delete_plo.php?delete=$id&fwid=$frameworkid' title='Delete'><img src='./img/icons/delete.png' /></a></div></div>";//link to edit_plo.php 
			    $i++;
								
				}
				 
			}
		
		if(isset($msg3)){
			echo $msg3;
		}
		
		?>
		<br />
		<h3>Add New PLO</h3>
		<form method='post' action="" class="mform">
			
			<div class="form-group row fitem ">
				<div class="col-md-3">
					<label class="col-form-label d-inline" for="id_plo">
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
							class="form-control "
							name="idnumber"
							id="id_idnumber"
							size=""
							pattern="[p/P][l/L][o/O]-[0-9]{1,}"
							title="eg. PLO-1"
							required
							placeholder="eg. PLO-1"
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
			
			<input type="hidden" name="framework_shortname" value="<?php echo $framework_shortname; ?>"/>
			<input type="hidden" name="frameworkid" value="<?php echo $frameworkid; ?>"/>
			<input class="btn btn-info" type="submit" name="save" value="Save"/>
		</form>
		<?php
		//echo $shortname;
		if(isset($_POST['save']) && !isset($msg3)){
          // echo $shortname;
				//echo $description;
				//echo $idnumber;

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
    	<a href="./select_frameworktoPLO.php">Back</a>
    	<?php
        echo $OUTPUT->footer();
    }?>
