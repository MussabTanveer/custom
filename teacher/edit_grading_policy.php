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
<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/validation/jquery.validate.js"></script>
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
	
	if(!empty($_GET['edit']))
	{
        $id=$_GET['edit'];
        $course_id=$_GET['course'];
        $coursecontext = context_course::instance($course_id);
		is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());

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
	<form method='post' action="" class="mform" id="editgpForm">
		<div class="form-group row fitem ">
            <div class="col-md-4 form-inline felement">
            <?php
            echo strtoupper($name);
            ?>
            </div>
            <div class="col-md-4 form-inline felement" data-fieldtype="number">
                <span class="input-group-addon" style="display: inline;"><i class="fa fa-percent"></i></span>
                <input type="number" value="<?php echo $percent; ?>" required
                        class="form-control"
                        name="percentage"
                        id="id_shortname"
                        size=""
                        maxlength="10"
                        step="0.001"
                        min="0" max="100">
            </div>
            <div class="form-control-feedback" id="id_error_shortname">
            </div>
		</div>
		<input class="btn btn-info" type="submit" name="save" value="Save"/>
	</form>

    <script>
        //form validation
        $(document).ready(function () {
            $('#editgpForm').validate({ // initialize the plugin
                rules: {
                    "percentage": {
                        number: true,
                        required: true,
                        step: 0.001,
                        range: [0, 100],
                        min: 0,
                        max: 100,
                        minlength: 1,
                        maxlength: 7
                    }
                },
                messages: {
                    "percentage": {
                        number: "Only numeric values are allowed.",
                        required: "Please enter percentage.",
                        step: "Please enter nearest percentage value.",
                        range: "Please enter percentage between 0 and 100%.",
                        min: "Please enter percentage greater than or equal to 0%.",
                        max: "Please enter percentage less than or equal to 100%.",
                        minlength: "Please enter more than 1 numbers.",
                        maxlength: "Please enter no more than 6 numbers (including decimal part)."
                    }
                }
            });
        });
    </script>
		
    <?php 
        label:
        ?>
        <div class="btn-btn-info"><br><a <?php echo "href=display_grading_policy.php?course=$course_id" ?> >View Grading Policy</a></div>
        <?php
	}
	else
    {?>
        <h3 style="color:red;"> Invalid Selection </h3>
        <a href="./teacher_courses.php">Back</a>
    	<?php
    }
    echo $OUTPUT->footer();
?>
