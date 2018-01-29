<?php
require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Manual Quiz");
    $PAGE->set_heading("Define Manual Quiz/Midterm/Final Exam");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/define_quiz.php');
    
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

<script src="../script/jquery/jquery-3.2.1.js"></script>

<?php
    
    if(!empty($_GET['type']) && !empty($_GET['course']))
    {
        $course_id=$_GET['course'];
        //echo "Course ID : $course_id";
        $type=$_GET['type'];
        //echo " Activity Type : $type";

		/* if user press save */
		if(isset($_POST['save'])) {
            $quizname = trim($_POST["name"]);
			$quizdesc = trim($_POST["description"]);
            $quesnames=array();
			foreach ($_POST['quesname'] as $qN)
			{
				array_push($quesnames,$qN);	
            }
            $maxmarks=array();
			foreach ($_POST['maxmark'] as $qMM)
			{
				array_push($maxmarks,$qMM);	
			}
            $closid=array();
			foreach ($_POST['clo'] as $cid)
			{
				array_push($closid,$cid);	
			}
			$separateattempts = array();
			for ($i = 0; $i < count($quesnames); $i++) {
				$separateattempts[$i] = in_array($i, $_POST['separateattempt']) ? 1 : 0;
			}

			// Insert manual quiz record
            $record = new stdClass();
            $record->name = $quizname;
            $record->description = $quizdesc;
            $quizid = $DB->insert_record('manual_quiz', $record); // get quiz id of newly inserted quiz

            // Insert this quiz id in mdl_grading_mapping table according to type (quiz, mid term, final exam) which is in $type variable above

            // Automated Mapping of Quiz, Mid-terms and Finals
			if($type == "quiz"){

				$recq=$DB->get_records_sql('SELECT id as quiz_id FROM mdl_grading_policy WHERE name="quiz" AND courseid=?',array($course_id));

				if($recq){
					foreach ($recq as $recordq) {
						$quiz_id=$recordq->quiz_id; 
					}
					$sql="INSERT INTO mdl_grading_mapping (courseid,module,instance,gradingitem) VALUES 
					('$course_id',-1,'$quizid','$quiz_id') ";
					$DB->execute($sql);
				}
				else{
					$msgq="Pls define Quiz in Define Grading Policy tab first";
				}
			}
           	elseif($type == "midterm"){
	        	$recm=$DB->get_records_sql('SELECT id as mid_id FROM mdl_grading_policy WHERE name="mid term" AND courseid=?',array($course_id));

				if($recm){
					foreach ($recm as $recordm) {
						$mid_id=$recordm->mid_id; 
					}
					$sql="INSERT INTO mdl_grading_mapping (courseid,module,instance,gradingitem) VALUES 
					('$course_id',-1,'$quizid','$mid_id') ";
					$DB->execute($sql);
				}
		        else{
        			$msgm="Pls. define Mid term in Define Grading Policy tab first";
				}
            }
            elseif($type == "finalexam"){
				$recf=$DB->get_records_sql('SELECT id as final_id FROM mdl_grading_policy WHERE name="final exam" AND courseid=?',array($course_id));
				if($recf){
					foreach ($recf as $recordf) {
						$final_id=$recordf->final_id; 
					}
					$sql="INSERT INTO mdl_grading_mapping (courseid,module,instance,gradingitem) VALUES 
					('$course_id',-1,'$quizid','$final_id_id') ";
					$DB->execute($sql);
				}
				else{
					$msgf="Pls. define Final Exam in Define Grading Policy tab first";
				}
            }
           	//  Automated mapping code ends here 

			// Insert Quiz Questions
            if($quizid){
                for ($i=0; $i < count($quesnames) ; $i++) {
                    # code...
                    $record = new stdClass();
                    $record->mquizid = $quizid;
                    $record->quesname = $quesnames[$i];
                    $record->maxmark = $maxmarks[$i];
                    $record->cloid = $closid[$i];
                    $record->separateattempt = $separateattempts[$i];
                    
                    $DB->insert_record('manual_quiz_question', $record);
                }
			}
			// Insert Quiz Questions code ends here 

			$redirect_page1="./report_teacher.php?course=$course_id";
			redirect($redirect_page1);
		}

		//Get course clo with its level, plo and peo
		$courseclos=$DB->get_records_sql(
        "SELECT clo.id AS cloid, clo.shortname AS cloname, plo.shortname AS ploname, peo.shortname AS peoname, levels.name AS lname, levels.level AS lvl
    
        FROM mdl_competency_coursecomp cc, mdl_competency clo, mdl_competency plo, mdl_competency peo, mdl_taxonomy_levels levels, mdl_taxonomy_clo_level clolevel

        WHERE cc.courseid = ? AND cc.competencyid=clo.id  AND peo.id=plo.parentid AND plo.id=clo.parentid AND 
        clo.id=clolevel.cloid AND levels.id=clolevel.levelid",
        
        array($course_id));
            
        $clonames = array(); $closid = array(); $plos = array(); $peos = array(); $levels = array(); $lvlno = array();
        foreach ($courseclos as $recC) {
            $cid = $recC->cloid;
            $clo = $recC->cloname;
            $plo = $recC->ploname;
            $peo = $recC->peoname;
            $lname = $recC->lname;
            $lvl = $recC->lvl;
            array_push($closid, $cid); // array of clo ids
            array_push($clonames, $clo); // array of clo names
            array_push($plos, $plo); // array of plos
            array_push($peos, $peo); // array of peos
            array_push($levels, $lname); // array of levels
            array_push($lvlno, $lvl); // array of level nos
		}
		
		$temp = array();
		$editor = \editors_get_preferred_editor();
		$editor->use_editor("id_description",$temp);

		?>
		<br />

		<p id="msg">
		
		</p>
		
		<form method='post' action="" class="mform" id="cloForm">
			
			<?php
            if($type == "quiz"){
                ?>
                <h3>Quiz</h3>
                <?php
            }
            elseif($type == "midterm"){
                ?>
                <h3>Midterm</h3>
                <?php
			}
			elseif($type == "finalexam"){
                ?>
                <h3>Final Exam</h3>
                <?php
            }
            ?>

            <div class="form-group row fitem ">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                    </span>
                    <label class="col-form-label d-inline" for="id_name">
                        Name
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
                            class="form-control"
                            name="name"
                            id="id_name"
                            size=""
                            required
                            maxlength="100">
                    <div class="form-control-feedback" id="id_error_name">
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

			<div id="dynamicInput">
            <h3>Question</h3>
            <div class="form-group row fitem ">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                    </span>
                    <label class="col-form-label d-inline" for="id_quesname">
                        Name
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
                            class="form-control"
                            name="quesname[]"
                            id="id_quesname"
                            size=""
                            required
                            maxlength="50">
                    <div class="form-control-feedback" id="id_error_quesname">
                    </div>
                </div>
            </div>

			<div class="form-group row fitem ">
				<div class="col-md-3">
					<span class="pull-xs-right text-nowrap">
						<abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
					</span>
					<label class="col-form-label d-inline" for="id_maxmark">
						Max Mark
					</label>
				</div>
				<div class="col-md-9 form-inline felement" data-fieldtype="number">
					<input type="number"
							class="form-control"
							name="maxmark[]"
							id="id_maxmark"
							size=""
							required
							step="0.001">
					<div class="form-control-feedback" id="id_error_maxmark">
					</div>
				</div>
			</div>
			
			<div class="form-group row fitem ">
				<div class="col-md-3">
					<span class="pull-xs-right text-nowrap">
						<abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
					</span>
					<label class="col-form-label d-inline" for="id_clo">
						CLO
					</label>
				</div>
				<div class="col-md-9 form-inline felement">
                    <select onChange="dropdownTip(this.value, 0)" name="clo[]" class="select custom-select">
                        <option value='NULL'>Choose..</option>
                        <?php
                        foreach ($courseclos as $recC) {
                        $cid =  $recC->cloid;
                        $cname = $recC->cloname;
                        $plname = $recC->ploname;
                        $pename = $recC->peoname;
                        ?>
                        <option value='<?php echo $cid; ?>'><?php echo $cname; ?></option>
                        <?php
                        }
                        ?>
                    </select>
					<span id="plo0"></span>
                    <span id="tax0"></span>
					<div class="form-control-feedback" id="id_error_clo">
					</div>
				</div>
			</div>
            
            <div class="form-group row fitem ">
                <div class="col-md-3">
					<label class="col-form-label d-inline" for="id_sepatmpt">
                        Separate Attempt
					</label>
				</div>
                <div class="col-md-9 form-inline felement">
                    <input type="checkbox" value="0" name="separateattempt[]" id="id_sepatmpt">
					<div class="form-control-feedback" id="id_error_sepatmpt">
					</div>
				</div>
            </div>

			</div>

			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-9">
					<input class="btn btn-success" type="button" value="Add another question" onClick="addInput('dynamicInput');">
				</div>
			</div>
			<br />

			<button class="btn btn-info" type="submit"  name="save" id="button" /> Save </button>
            <a class="btn btn-default" type="submit" href="./report_teacher.php?course=<?php $course_id ?>">Cancel</a>
			<br /><br />
			<div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>
		</form>
		
		<?php
		if(isset($msgq)){
			echo $msgq;
		}
		elseif(isset($msgm)){
			echo $msgm;
		}
		elseif(isset($msgf)){
			echo $msgf;
		}
		?>
				
		<script>
			// script to add name, desc, kpi, plo and level fields to form
			var i = 1;
            var closid = <?php echo json_encode($closid); ?>;
            var clonames = <?php echo json_encode($clonames); ?>;
			
			function addInput(divName){
                var newh3 = document.createElement('h3');
				newh3.innerHTML = 'Question';
				document.getElementById(divName).appendChild(newh3);

				var newdiv1 = document.createElement('div');
				newdiv1.innerHTML = '<div class="form-group row fitem "><div class="col-md-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_quesname">Name</label></div><div class="col-md-9 form-inline felement" data-fieldtype="text"><input type="text" class="form-control" name="quesname[]" id="id_quesname" size="" required maxlength="50"><div class="form-control-feedback" id="id_error_quesname"></div></div></div>';
				document.getElementById(divName).appendChild(newdiv1);

				var newdiv2 = document.createElement('div');
				newdiv2.innerHTML = '<div class="form-group row fitem "><div class="col-md-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_maxmark">Max Mark</label></div><div class="col-md-9 form-inline felement" data-fieldtype="number"><input type="number" class="form-control" name="maxmark[]" id="id_maxmark" size="" required step="0.001"><div class="form-control-feedback" id="id_error_maxmark"></div></div></div>';
				document.getElementById(divName).appendChild(newdiv2);

				//Create select element for CLO selection
				var selectCLO = document.createElement("select");
				selectCLO.className = "select custom-select";
				selectCLO.name = "clo[]";
				jsFuncVal = "dropdownTip(this.value, "+i+")";
				selectCLO.setAttribute("required", "required");
				selectCLO.setAttribute("onChange", jsFuncVal);

				//Create and append the options
				var option = document.createElement("option");
				option.value = "";
				option.text = "Choose..";
				selectCLO.appendChild(option);
				for (var l = 0; l < closid.length; l++) {
					var option = document.createElement("option");
					option.value = closid[l];
					option.text = clonames[l];
					selectCLO.appendChild(option);
				}

				var newdivforselectCLO = document.createElement('div');
				newdivforselectCLO.appendChild(selectCLO);

				var newdiv3 = document.createElement('div');
                newdiv3.innerHTML = '<div class="form-group row fitem "><div class="col-md-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_clo">CLO</label></div><div class="col-md-9 form-inline felement">'+newdivforselectCLO.innerHTML+' <span id="plo'+i+'"></span> <span id="tax'+i+'"></span><div class="form-control-feedback" id="id_error_plo"></div></div></div>';
                document.getElementById(divName).appendChild(newdiv3);

                var newdiv4 = document.createElement('div');
                newdiv4.innerHTML = '<div class="form-group row fitem"><div class="col-md-3"><label class="col-form-label d-inline" for="id_sepattempt">Separate Attempt</label></div><div class="col-md-9 form-inline felement"><input type="checkbox" value="'+i+'" name="separateattempt[]" id="id_sepattempt"><div class="form-control-feedback" id="id_error_sepattempt"></div></div></div>';
                document.getElementById(divName).appendChild(newdiv4);
                
				i++;
			}
		</script>

		<script>
            var closid = <?php echo json_encode($closid); ?>;
			var plos = <?php echo json_encode($plos); ?>;
			//var peos = <?php echo json_encode($peos); ?>;
			var levels = <?php echo json_encode($levels); ?>;
			var levelnos = <?php echo json_encode($lvlno); ?>;

			function dropdownTip(value,id){
				var plo = "plo" + id;
				//var peo = "peo" + id;
				var tax = "tax" + id;
				if(value == 'NULL'){
					document.getElementById(plo).innerHTML = "";
					//document.getElementById(peo).innerHTML = "";
					document.getElementById(tax).innerHTML = "";
				}
				else{
					for(var i=0; i<closid.length ; i++){
						if(closid[i] == value){
							document.getElementById(plo).innerHTML = "PLO: " + plos[i];
							//document.getElementById(peo).innerHTML = peos[i];
							document.getElementById(tax).innerHTML = "LEVEL: " + levels[i] + " (" + levelnos[i] + ")";
							break;
						}
					}
				}
			}
			
		</script>

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
