<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Assign Weightage");
    $PAGE->set_heading("Assign Weightage");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/assign_weightage.php');
    
	require_login();
    if($SESSION->oberole != "chairman"){
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
    if(isset($_POST['save'])){
        $midterm = trim($_POST["midterm"]);
        $final = trim($_POST["final"]);
        $sessional=trim($_POST["sessional"]);
        $sum=$midterm+$final+$sessional;
        if($sum < 100 || $sum > 100){
            $error_msg = "<font color = red>The sum of the below entered weightage should be 100%!</font><br />";
        }
        else{
            $revisions=$DB->get_records_sql('SELECT DISTINCT revision FROM `mdl_grading_policy_chairman` ORDER BY revision');
            $rev=0;
            if($revisions){
                foreach ($revisions as $revision){
                    $rev = $revision->revision; 
                }
            }
            $rev++;

            $record1 = new stdClass();
            $record1->name = "activities";
            $record1->percentage = $sessional;
            $record1->revision = $rev;
            $record2 = new stdClass();
            $record2->name = "mid term";
            $record2->percentage = $midterm;
            $record2->revision = $rev;
            $record3 = new stdClass();
            $record3->name = "final exam";
            $record3->percentage = $final;
            $record3->revision = $rev;

            $records = array($record1, $record2, $record3);
            $DB->insert_records('grading_policy_chairman', $records);

            redirect('./report_chairman.php');
        }
    }
    
    /*elseif(isset($_POST['return'])){

    redirect('report_chairman.php');

    }*/

    if(isset($error_msg)){
        echo $error_msg;
    }

    ?>

    <form method='post' action="" class="mform" id="cloForm">
        <div class="form-group row fitem ">
            <div class="col-md-3">
                <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                </span>
                <label class="col-form-label d-inline" for="id_act">
                    Quiz, Assignment, Project, Other
                </label>
            </div>
            <div class="col-md-9 form-inline felement" data-fieldtype="number">
                <input type="number"
                        class="form-control"
                        name="sessional"
                        id="id_act"
                        size=""
                        required
                        maxlength="100"
                        step="0.001"
                        min="0" max="100">
                        %
                        (Note: To be assigned individually by the teacher)
                <div class="form-control-feedback" id="id_error_name">
                </div>
            </div>
        </div>
        
        <div class="form-group row fitem ">
            <div class="col-md-3">
                <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                </span>
                <label class="col-form-label d-inline" for="id_mt">
                    Midterm
                </label>
            </div>
            <div class="col-md-9 form-inline felement" data-fieldtype="number">
                <input type="number"
                        class="form-control"
                        name="midterm"
                        id="id_mt"
                        size=""
                        required
                        maxlength="100"
                        step="0.001"
                        min="0" max="100">
                        %
                <div class="form-control-feedback" id="id_error_name">
                </div>
            </div>
        </div>

        <div class="form-group row fitem ">
            <div class="col-md-3">
                <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                </span>
                <label class="col-form-label d-inline" for="id_fe">
                    Final Exam
                </label>
            </div>
            <div class="col-md-9 form-inline felement" data-fieldtype="number">
                <input type="number"
                        class="form-control"
                        name="final"
                        id="id_fe"
                        size=""
                        required
                        maxlength="100"
                        step="0.001"
                        min="0" max="100">
                        %
                <div class="form-control-feedback" id="id_error_name">
                </div>
            </div>
        </div>
        
        <!--<input class="btn btn-info" type="submit" name="save" value="Save and continue"/>
        <input class="btn btn-info" type="submit" name="return" value="Save and return"/>-->
        <input class="btn btn-info" type="submit" name="save" value="Save"/>
        <a class="btn btn-default" type="submit" <?php echo "href='./report_chairman.php'" ?>>Cancel</a>
    </form>
    <?php
		if(isset($_POST['save']) && isset($error_msg)){
		?>
		<script>
			document.getElementById("id_act").value = <?php echo json_encode($sessional); ?>;
			document.getElementById("id_mt").value = <?php echo json_encode($midterm); ?>;
			document.getElementById("id_fe").value = <?php echo json_encode($final); ?>;
		</script>
		<?php
		}
    ?>

<?php
echo $OUTPUT->footer();
?>
