<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/validation/jquery.validate.js"></script>
<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Edit Rubric");
    $PAGE->set_heading("Edit Rubric");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/edit_rubric.php');
    
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
	label.error {
		color: red;
	}
</style>
<?php
	// EDIT RUBRIC
	if(!empty($_GET['edit']) && !empty($_GET['rubric']) && $_GET['edit']=="rubric")
	{
		$edit=$_GET['edit'];
		$rubric_id=$_GET['rubric'];

		$rec=$DB->get_records_sql('SELECT name, description FROM mdl_rubric WHERE id=?',array($rubric_id));
		if($rec){
			foreach ($rec as $records){
				$name=$records->name;
				$description=$records->description;
			}
		}
		
		if(isset($_POST['save']))
		{
			$name=$_POST['rubricname'];
			$description=$_POST['rubricdesc'];
            
			if(empty($name))
			{
				$msgRName="<font color='red'>-Please enter rubric's name</font>";
			}
            else{
				$sql="UPDATE mdl_rubric SET name=?, description=? WHERE id=?";
				$DB->execute($sql, array($name, $description, $rubric_id));
				$msgSuces = "<font color='green'><b>Rubric successfully updated!</b></font><br />";
            }
		}
	
		if(isset($msgSuces)){
			echo $msgSuces;
			goto label1;
		}

	echo "<br />";
	echo "<h3>Edit Rubric</h3>";
	?>
	
	<form method='post' action="" class="mform" id="rubricForm">
		<div class="form-group row fitem ">
            <div class="col-md-3">
                <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                </span>
                <label class="col-form-label d-inline" for="id_rubricname">
                    Name
                </label>
            </div>
            <div class="col-md-9 form-inline felement" data-fieldtype="text">
                <input type="text"
                        class="form-control "
                        name="rubricname"
                        id="id_rubricname"
                        size=""
                        required
                        maxlength="100" >
                <div class="form-control-feedback" id="id_error_rubricname">
				<?php
				if(isset($msgRName)){
					echo $msgRName;
				}
				?>
                </div>
            </div>
        </div>
        
        <div class="form-group row fitem">
            <div class="col-md-3">
                <span class="pull-xs-right text-nowrap">
                </span>
                <label class="col-form-label d-inline" for="id_rubricdesc">
                    Description
                </label>
            </div>
            <div class="col-md-9 form-inline felement" data-fieldtype="editor">
                <div>
                    <div>
                        <textarea id="id_rubricdesc" name="rubricdesc" class="form-control" rows="4" cols="80" spellcheck="true" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="form-control-feedback" id="id_error_rubricdesc"  style="display: none;">
                </div>
            </div>
        </div>
		
		<input class="btn btn-info" type="submit" name="save" value="Save"/>
	</form>

	<script>
		document.getElementById("id_rubricname").value = <?php echo json_encode($name); ?>;
		document.getElementById("id_rubricdesc").value = <?php echo json_encode($description); ?>;
	</script>
	
	<br />
	<div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>
		
	<?php 
        label1:
        ?>
        <div class="btn-btn-info"><br><a href="./view_rubric.php?rubric=<?php echo $rubric_id; ?>" >Back</a></div>
        <?php
	}
	// EDIT CRITERION
	elseif(!empty($_GET['edit']) && !empty($_GET['rubric']) && !empty($_GET['criterion']) && !empty($_GET['num']) && $_GET['edit']=="criterion")
	{
		$edit=$_GET['edit'];
		$rubric_id=$_GET['rubric'];
		$criterion_id=$_GET['criterion'];
		$num=$_GET['num'];

		$rec=$DB->get_records_sql('SELECT description FROM mdl_rubric_criterion WHERE id=?',array($criterion_id));
		if($rec){
			foreach ($rec as $records){
				$description=$records->description;
			}
		}
		
		if(isset($_POST['save']))
		{
			$description=$_POST['criteriondesc'];
			
			if(empty($description))
			{
				$msgCDesc="<font color='red'>-Please enter criterion's description</font>";
			}
            else{
				$sql="UPDATE mdl_rubric_criterion SET description=? WHERE id=?";
				$DB->execute($sql, array($description, $criterion_id));
				$msgSuces = "<font color='green'><b>Rubric's criterion successfully updated!</b></font><br />";
			}
		}
	
		if(isset($msgSuces)){
			echo $msgSuces;
			goto label2;
		}

	echo "<br />";
	echo "<h3>Edit Criterion $num</h3>";
	?>
	
	<form method='post' action="" class="mform" id="rubricForm">
		<div class="form-group row fitem">
            <div class="col-md-3">
                <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                </span>
                <label class="col-form-label d-inline" for="id_criteriondesc">
                    Description
                </label>
            </div>
            <div class="col-md-9 form-inline felement" data-fieldtype="editor">
                <div>
                    <div>
                        <textarea required id="id_criteriondesc" name="criteriondesc" class="form-control" rows="4" cols="80" spellcheck="true" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="form-control-feedback" id="id_error_criteriondesc">
				<?php
				if(isset($msgCDesc)){
					echo $msgCDesc;
				}
				?>
                </div>
            </div>
        </div>
		
		<input class="btn btn-info" type="submit" name="save" value="Save"/>
	</form>

	<script>
		document.getElementById("id_criteriondesc").value = <?php echo json_encode($description); ?>;
	</script>
	
	<br />
	<div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>
		
	<?php 
        label2:
        ?>
        <div class="btn-btn-info"><br><a href="./view_rubric.php?rubric=<?php echo $rubric_id; ?>" >Back</a></div>
        <?php
	}
	// EDIT SCALE
	elseif(!empty($_GET['edit']) && !empty($_GET['rubric']) && !empty($_GET['scale']) && !empty($_GET['snum']) && !empty($_GET['cnum']) && $_GET['edit']=="scale")
	{
		$edit=$_GET['edit'];
		$rubric_id=$_GET['rubric'];
		$scale_id=$_GET['scale'];
		$snum=$_GET['snum'];
		$cnum=$_GET['cnum'];

		$rec=$DB->get_records_sql('SELECT description, score FROM mdl_rubric_scale WHERE id=?',array($scale_id));
		if($rec){
			foreach ($rec as $records){
				$description=$records->description;
				$score=$records->score;
			}
		}
		
		if(isset($_POST['save']))
		{
			$description=$_POST['scaledesc'];
			$score=$_POST['scalescore'];
			
			if(empty($description))
			{
				$msgSDesc="<font color='red'>-Please enter scale's description</font>";
			}
			elseif(empty($score))
			{
				$msgSScore="<font color='red'>-Please enter scale's score</font>";
			}
            else{
				$sql="UPDATE mdl_rubric_scale SET description=?, score=? WHERE id=?";
				$DB->execute($sql, array($description, $score, $scale_id));
				$msgSuces = "<font color='green'><b>Rubric criterion's scale successfully updated!</b></font><br />";
			}
		}
	
		if(isset($msgSuces)){
			echo $msgSuces;
			goto label3;
		}

	echo "<br />";
	echo "<h3>Edit Scale $snum of Criterion $cnum</h3>";
	?>
	
	<form method='post' action="" class="mform" id="rubricForm">
		<div class="form-group row fitem">
            <div class="col-md-3">
                <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                </span>
                <label class="col-form-label d-inline" for="id_scaledesc">
                    Description
                </label>
            </div>
            <div class="col-md-9 form-inline felement" data-fieldtype="editor">
                <div>
                    <div>
                        <textarea required id="id_scaledesc" name="scaledesc" class="form-control" rows="3" cols="40" spellcheck="true" maxlength="200"></textarea>
                    </div>
                </div>
                <div class="form-control-feedback" id="id_error_scaledesc">
				<?php
				if(isset($msgSDesc)){
					echo $msgSDesc;
				}
				?>
                </div>
            </div>
		</div>
		<div class="form-group row fitem">
            <div class="col-md-3">
                <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                </span>
                <label class="col-form-label d-inline" for="id_scalescore">
                    Score
                </label>
            </div>
            <div class="col-md-9 form-inline felement" data-fieldtype="number">
                <input type="number"
                        class="form-control"
                        name="scalescore"
                        id="id_scalescore"
                        size=""
                        required
                        placeholder="eg. 1"
                        maxlength="7"
                        step="0.001"
                        min="0" max="100">
                <div class="form-control-feedback" id="id_error_scalescore">
				<?php
				if(isset($msgSScore)){
					echo $msgSScore;
				}
				?>
                </div>
            </div>
        </div>
		
		<input class="btn btn-info" type="submit" name="save" value="Save"/>
	</form>

	<script>
		document.getElementById("id_scaledesc").value = <?php echo json_encode($description); ?>;
		document.getElementById("id_scalescore").value = <?php echo json_encode($score); ?>;
	</script>
	
	<br />
	<div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>
		
	<?php
        label3:
        ?>
        <div class="btn-btn-info"><br><a href="./view_rubric.php?rubric=<?php echo $rubric_id; ?>" >Back</a></div>
        <?php
	}
	else
    {?>
        <h3 style="color:red;"> Invalid Selection </h3>
        <a href="./select_rubric.php">Back</a>
    	<?php
	}
	echo $OUTPUT->footer();
	?>
