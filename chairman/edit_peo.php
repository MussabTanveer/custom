<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/validation/jquery.validate.js"></script>
<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Edit OBE PEO");
    $PAGE->set_heading("Edit PEO");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/edit_peo.php');
    
    echo $OUTPUT->header();
	require_login();
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
?>
<style>
	label.error {
		color: red;
	}
</style>
<?php
	if(!empty($_GET['edit']) && !empty($_GET['fwid']))
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
                
                $check=$DB->get_records_sql('SELECT * from mdl_competency WHERE idnumber=? AND competencyframeworkid=? AND id!=?', array($idnumber,$fw_id,$id));
                if(count($check)){
                    $msg2="<font color='red'>-Please enter UNIQUE ID number</font>";
                }
                else{
                    $sql_update="UPDATE mdl_competency SET shortname='$shortname',description='$description',idnumber='$idnumber',timemodified='$time',usermodified=$USER->id WHERE id=$id";
                    $DB->execute($sql_update);
                    $msg3 = "<font color='green'><b>PEO successfully updated!</b></font><br />";
                }
            }
		}
	
		if(isset($msg3)){
			echo $msg3;
			goto label;
		}
		
	?>
	
	<br />
	<h3>Edit PEO</h3>
	<form method='post' action="" class="mform" id="peoForm">
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
						title="eg. PEO-1"
						required
						maxlength="20" type="text" >
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
						maxlength="30" type="text" >
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
		
		<input class="btn btn-info" type="submit" name="save" value="Save"/>
	</form>

	<script>
		//form validation
		$(document).ready(function () {
			$('#peoForm').validate({ // initialize the plugin
				rules: {
					"idnumber": {
						required: true,
						minlength: 1,
						maxlength: 20,
						pattern: /^[p/P][e/E][o/O]-[0-9]{1,}$/
					},
					"shortname": {
						required: true,
						minlength: 1,
						maxlength: 30
					}
				},
				messages: {
					"idnumber": {
						required: "Please enter ID number.",
						pattern: "Please enter correct format."
					},
					"shortname": {
						required: "Please enter Name."
					}
				}
			});
		});
	</script>

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
        <div class="btn-btn-info"><br><a href="./select_frameworktoPEO.php" >Back</a></div>
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
