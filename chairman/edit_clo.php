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
	if(isset($_GET['edit']) && isset($_GET['fwid']))
	{
		$id=$_GET['edit'];
		$fw_id=$_GET['fwid'];
		
		if(isset($_POST['save']))
		{	
			//$shortname=$_POST['shortname'];
			$description=$_POST['description'];
			//$idnumber=$_POST['idnumber']; $idnumber=strtoupper($idnumber);
			

			$id = $_GET['edit'];
			$fw_id=$_GET['fwid'];
			$kpi = $_POST['kpi'];
		
			
			$time = time();

        			$revisions=$DB->get_records_sql('SELECT * FROM `mdl_competency` where id = ? ', array($id));
					
					if($revisions){
	            		foreach ($revisions as $revision){
							
							$plo = $revision->parentid;
							$fwidd= $revision->competencyframeworkid; 
							$shortname = $revision->shortname;
							$idnumber = $revision->idnumber;

	            		}
        			}
        			
       			
					$sql="INSERT INTO mdl_competency (shortname, description, descriptionformat, idnumber, competencyframeworkid, parentid, path, sortorder, timecreated, timemodified, usermodified) VALUES ('$shortname', '$description', '1', '$idnumber','$fwidd' ,'$plo', '/0/', '0', '$time', '$time',$USER->id)";
					$DB->execute($sql);

					$query=$DB->get_records_sql("SELECT MAX(id) as id FROM  mdl_competency");

							foreach ($query as $q){

								$lastId = $q->id;
							}
							

					$sql="INSERT INTO mdl_clo_revision (cloid , revision) VALUES ('$id','$lastId')";

						$DB->execute($sql);



							
					$sql="INSERT INTO mdl_clo_kpi (cloid , kpi) VALUES ('$lastId','$kpi')";

						$DB->execute($sql);
				
					$msg3 = "<font color='green'><b>CLO successfully updated!</b></font><br />";
				
			
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
					Passing Percentage
				</label>
			</div>
			<div class="col-md-9 form-inline felement" data-fieldtype="number">
				<input type="number"
						class="form-control"
						name="kpi"
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

		<input class="btn btn-info" type="submit" name="save" value="Save"/>
	</form>
	
	<?php
		if(isset($_GET['edit'])){
		?>
		<?php
		$id=$_GET['edit'];
		$rec=$DB->get_records_sql('SELECT shortname,description,idnumber FROM mdl_competency WHERE id=?',array($id));
		$recKPI=$DB->get_records_sql('SELECT kpi FROM mdl_clo_kpi WHERE cloid=?',array($id));
		if($rec){
			foreach ($rec as $records){
				$shortname=$records->shortname;
				$description=$records->description;
				$idnumber=$records->idnumber;

			}
		}
		if($recKPI){
			foreach ($recKPI as $rKPI){
				$kpi=$rKPI->kpi;
			}
		}
		
		?>
	<script>
	    //document.getElementById("id_shortname").value = <?php echo json_encode($shortname); ?>;
        document.getElementById("id_description").value = <?php echo json_encode($description); ?>;
        //document.getElementById("id_idnumber").value = <?php echo json_encode($idnumber); ?>;
		document.getElementById("id_kpi").value = <?php echo json_encode($kpi); ?>;
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
        echo $OUTPUT->footer();
	}
	else
    {?>
        <h3 style="color:red;"> Invalid Selection </h3>
        <a href="./select_frameworktoCLO.php">Back</a>
    	<?php
        echo $OUTPUT->footer();
    }?>
	