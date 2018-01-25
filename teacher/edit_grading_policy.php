<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Edit Grading Policy");
    $PAGE->set_heading("Edit Grading Policy");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/edit_grading_policy.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
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
<?php
	
	if(isset($_GET['edit']))
	{
        $id=$_GET['edit'];
        $course_id=$_GET['course'];

        $rec=$DB->get_records_sql('SELECT name, percentage FROM mdl_grading_policy WHERE id=?',array($id));
        foreach ($rec as $records){
            $name=$records->name;
            $percent=$records->percentage;
        }
        
		if(isset($_POST['save']))
		{
            $percentage=$_POST['percentage'];
            $sql_update="UPDATE mdl_grading_policy SET percentage=$percentage WHERE id=$id";
            $DB->execute($sql_update);
            $msg = "<font color='green'><b>Grading policy item updated!</b></font><br />";
		}
	
		if(isset($msg)){
			echo $msg;
			goto label;
		}
		
	?>
	
	<br />
	<h3>Edit Grading Policy Item</h3>
	<form method='post' action="" class="mform">
		<div class="form-group row fitem ">
            <div class="col-md-4 form-inline felement">
            <?php
            echo strtoupper($name);
            ?>
            </div>
            <div class="col-md-4 form-inline felement" data-fieldtype="number">
                <input type="number" value="<?php echo $percent; ?>"
                        class="form-control"
                        name="percentage"
                        id="id_shortname"
                        size=""
                        maxlength="100"
                        step="0.001"
                        min="0" max="100"> %
            </div>
            <div class="form-control-feedback" id="id_error_shortname">
            </div>
		</div>
		<input class="btn btn-info" type="submit" name="save" value="Save"/>
	</form>
		
    <?php 
        label:
        ?>
        <div class="btn-btn-info"><br><a <?php echo "href=display_grading_policy.php?course=$course_id" ?> >View Grading Policy</a></div>
        <?php
	}
	else
    {?>
        <h3 style="color:red;"> Invalid Selection </h3>
        <a href="../index.php">Back</a>
    	<?php
    }
    echo $OUTPUT->footer();
?>
