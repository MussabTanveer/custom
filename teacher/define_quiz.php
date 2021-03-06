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
<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/validation/jquery.validate.js"></script>
<script src="../script/validation/additional-methods.min.js"></script>
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
  	global $CFG;
    $x= $CFG->dbpass;
    $dbh = $CFG->dbhost;
    $dbn = $CFG->dbname;
    $dbu = $CFG->dbuser;
    
    if(!empty($_GET['type']) && !empty($_GET['course']))
    {
        $course_id=$_GET['course'];
		//echo "Course ID : $course_id";
		$course_id = (int)$course_id; // convert course id from string to int
		$coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
		//echo gettype($course_id), "\n";
        $type=$_GET['type'];
        //echo " Activity Type : $type";

		/* if user press save */
		if(isset($_POST['save'])) {
            $quizname = trim($_POST["name"]);
            $quizdesc = trim($_POST["description"]);
            //echo "$quizname<br>";
            if(strlen($quizname)>50 || strlen($quizdesc)>500)
            { 	//echo "in IF";
					

					if(strlen($quizname)>50)
            		 $lengthMsg= "<font color = red> Length of the name should be less than 50<br></font>";
  				
  				if(strlen($quizdesc)>500)
            	//echo "in IF";
            	$descMsg= "<font color = red> Length of the description should be less than 500<br></font>";
            	
            	goto down;
           	}

            $quesnames=array();
			foreach ($_POST['quesname'] as $qN)
			{
				//if (strlen($qT)>$qN>30)
				//{ $quesnameMsg= "<font color = red> Length of the Question's should be less than 30<br></font>";
				//// 	goto down;
				// }
				array_push($quesnames,$qN);	
			}
			$questexts=array();
			foreach ($_POST['ques_text'] as $qT)
			{

				array_push($questexts,$qT);
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
			if($type == "finalexam"){
				$separateattempts = array();
				for ($i = 0; $i < count($quesnames); $i++) {
					$separateattempts[$i] = in_array($i, $_POST['separateattempt']) ? 1 : 0;
				}
			}


		    $file = $_FILES['quizQues']['name'];
		    $file_loc = $_FILES['quizQues']['tmp_name'];
		    $file_size = $_FILES['quizQues']['size'];
		    $file_type = $_FILES['quizQues']['type'];

		 	// var_dump($_FILES['quizQues']);

			// Insert manual quiz record
			try {
				$transaction = $DB->start_delegated_transaction();
				$record = new stdClass();
				$record->courseid = $course_id;
				$record->name = $quizname;
				$record->description = $quizdesc;
				if($type=="quiz"){

                      $record->module=-1;

				}

				elseif($type=="midterm"){

                    $record->module=-2;

				}

				elseif($type=="finalexam"){

                    $record->module=-3;

				}
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
						('$course_id',-2,'$quizid','$mid_id') ";
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
						('$course_id',-3,'$quizid','$final_id') ";
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
						$record->questext = $questexts[$i];
						$record->maxmark = $maxmarks[$i];
						$record->cloid = $closid[$i];
						if($type == "finalexam"){
							$record->separateattempt = $separateattempts[$i];
						}
						
						$DB->insert_record('manual_quiz_question', $record);
					}
				}
				// Insert Quiz Questions code ends here
				$transaction->allow_commit();
				} catch(Exception $e) {
					$transaction->rollback($e);
			}

			$redirect_page1="./report_teacher.php?course=$course_id";

			//Upload PDF
			if ($file_size>0 )
			{
				
			if ($file_type == "application/pdf")
			       { 
			           $blobObj = new Blob($x,$dbh,$dbn,$dbu);
			              $blobObj->updateBlob($quizid,$file_loc,"application/pdf");
			              echo "<font color = green> File has been Uploaded successfully! </font>";
			        }
			        else
			            echo "<font color = red >Incorrect File Type. Only PDFs are allowed</font>";
			    }



			redirect($redirect_page1);
		}
		down:
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
		//$editor->use_editor("id_ques_text",$temp);

		?>
		<br />

		<p id="msg"></p>
		
		<?php
		$flag=0;
		if($type == "quiz"){
			$recq=$DB->get_records_sql('SELECT id as quiz_id FROM mdl_grading_policy WHERE name="quiz" AND courseid=?',array($course_id));
			if($recq){
				$flag=1;
			}
			else{
				$msgq="<h4 style='color:red;'>Please define Quiz Grading Policy first.</h4><br /><a href='./grading_policy.php?course=$course_id'>Click here..</a>";
			}
		}
		elseif($type == "midterm"){
			$recm=$DB->get_records_sql('SELECT id as mid_id FROM mdl_grading_policy WHERE name="mid term" AND courseid=?',array($course_id));
			if($recm){
				$flag=1;
			}
			else{
				$msgm="<h4 style='color:red;'>Please define Midterm Grading Policy first.</h4><br /><a href='./grading_policy.php?course=$course_id'>Click here..</a>";
			}
		}
		elseif($type == "finalexam"){
			$recf=$DB->get_records_sql('SELECT id as final_id FROM mdl_grading_policy WHERE name="final exam" AND courseid=?',array($course_id));
			if($recf){
				$flag=1;
			}
			else{
				$msgf="<h4 style='color:red;'>Please define Final Exam Grading Policy first.</h4><br /><a href='./grading_policy.php?course=$course_id'>Click here..</a>";
			}
		}
		if($flag){
		?>
		<form method='post' action="" class="mform" id="quizForm" enctype="multipart/form-data">
			
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
            <?php
            	
            	if(isset($lengthMsg))
            		echo "$lengthMsg";

            ?>

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
							<textarea id="id_description" name="description" class="form-control" rows="4" cols="80" spellcheck="true" maxlength="100"></textarea>
						</div>
					</div>
					<div class="form-control-feedback" id="id_error_description"  style="display: none;">
					</div>
				</div>
			</div>	
			  <?php
            	
            	if(isset($descMsg))
            		echo "$descMsg";

            ?>		

			<div class="form-group row fitem ">
				<div class="col-md-3">
					<label class="col-form-label d-inline" for="quizQues">
						Upload Quiz Paper
					</label>
				</div>
				<div class="col-md-9 form-inline felement">
					<div class="btn btn-default btn-file">
						<input  type="file" name="quizQues" id="quizQues" accept="application/pdf" placeholder="Only PDFs are allowed">
					</div>
					(Only PDFs are allowed)
				</div>
			</div>

			<div id="dynamicInput">
				<div id="div0">
				<h3>Map Question to CLO</h3>
				<div class="form-group row fitem ">
					<div class="col-md-3">
						<span class="pull-xs-right text-nowrap">
							<abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
						</span>
						<label class="col-form-label d-inline" for="id_quesname0">
							Name
						</label>
					</div>
					<div class="col-md-5 form-inline felement" data-fieldtype="text">
						<input type="text"
								class="form-control"
								name="quesname[]"
								id="id_quesname0"
								size=""
								required
								maxlength="100">
						<div class="form-control-feedback" id="id_error_quesname">
						</div>
					</div>
					<div class="col-md-4">
						<i id="cross0" class="fa fa-times" style="font-size:28px;color:red;cursor:pointer" title="Remove"></i>
					</div>
				</div>
				
				<div class="form-group row fitem">
					<div class="col-md-3">
						<span class="pull-xs-right text-nowrap">
							
						</span>
						<label class="col-form-label d-inline" for="id_ques_text0">
							Text
						</label>
					</div>
					<div class="col-md-9 form-inline felement" data-fieldtype="editor">
						<div>
							<div>
								<textarea id="id_ques_text0" name="ques_text[]" class="form-control" rows="4" cols="80" spellcheck="true" maxlength="800"></textarea>
							</div>
						</div>
						<div class="form-control-feedback" id="id_error_ques_text"  style="display: none;">
						</div>
					</div>
				</div>

				<div class="form-group row fitem ">
					<div class="col-md-3">
						<span class="pull-xs-right text-nowrap">
							<abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
						</span>
						<label class="col-form-label d-inline" for="id_maxmark0">
							Max Marks
						</label>
					</div>
					<div class="col-md-9 form-inline felement" data-fieldtype="number">
						<input type="number"
								class="form-control"
								name="maxmark[]"
								id="id_maxmark0"
								maxlength="10"
								size=""
								required
								step="0.001"
								min="0" max="100">
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
						<select required onChange="dropdownTip(this.value, 0)" name="clo[]" class="select custom-select" id="clo0">
							<option value=''>Choose..</option>
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
				
				<?php
				if($type == "finalexam"){
				?>
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
				<?php
				}
				?>

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
            <a class="btn btn-default" type="submit" href="./report_teacher.php?course=<?php echo $course_id ?>">Cancel</a>
			<br /><br />
			<div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>
		</form>
		
		<?php
		}
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
			// script to remove question fields from form
			$(document).ready(function(){
				$("#cross0").click(function(){
					$("#div0").remove();
				});
			});
		</script>
		<script>
			// script to add quiz name & desc & ques name, desc, maxmark, clo & separateattempt fields to form
			var i = 1;
            var type = <?php echo json_encode($type); ?>;
			var closid = <?php echo json_encode($closid); ?>;
            var clonames = <?php echo json_encode($clonames); ?>;
			
			function addInput(divName){
				var divWrap = document.createElement('div');
				var divid = "div"+i;
				divWrap.setAttribute("id", divid);
				divWrap.innerHTML = '<h3>Map Question to CLO</h3>';
				document.getElementById(divName).appendChild(divWrap);

                /*var newh3 = document.createElement('h3');
				newh3.innerHTML = 'Map Question to CLO';
				document.getElementById(divName).appendChild(newh3);*/

				var newdiv = document.createElement('div');
				newdiv.innerHTML = '<div class="form-group row fitem "><div class="col-md-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_quesname'+i+'">Name</label></div><div class="col-md-5 form-inline felement" data-fieldtype="text"><input type="text" class="form-control" name="quesname[]" id="id_quesname'+i+'" size="" required maxlength="100"><div class="form-control-feedback" id="id_error_quesname"></div></div><div class="col-md-4"><i id="cross'+i+'" class="fa fa-times" style="font-size:28px;color:red;cursor:pointer" title="Remove"></i></div></div>';
				divWrap.appendChild(newdiv);

				var newdiv1 = document.createElement('div');
				newdiv1.innerHTML = '<div class="form-group row fitem"><div class="col-md-3"><span class="pull-xs-right text-nowrap"></span><label class="col-form-label d-inline" for="id_ques_text'+i+'">Text</label></div><div class="col-md-9 form-inline felement" data-fieldtype="editor"><div><div><textarea id="id_ques_text'+i+'" name="ques_text[]" class="form-control" rows="4" cols="80" spellcheck="true" maxlength="800"></textarea></div></div><div class="form-control-feedback" id="id_error_ques_text" style="display: none;"></div></div></div>';
				divWrap.appendChild(newdiv1);

				var newdiv2 = document.createElement('div');
				newdiv2.innerHTML = '<div class="form-group row fitem "><div class="col-md-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_maxmark'+i+'">Max Mark</label></div><div class="col-md-9 form-inline felement" data-fieldtype="number"><input type="number" class="form-control" name="maxmark[]" id="id_maxmark'+i+'" maxlength="10" size="" required step="0.001" min="0" max="100"><div class="form-control-feedback" id="id_error_maxmark"></div></div></div>';
				divWrap.appendChild(newdiv2);

				//Create select element for CLO selection
				var selectCLO = document.createElement("select");
				var selectid="clo"+i;
				selectCLO.setAttribute("id", selectid);
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
				divWrap.appendChild(newdiv3);

				if(type == "finalexam"){
					var newdiv4 = document.createElement('div');
					newdiv4.innerHTML = '<div class="form-group row fitem"><div class="col-md-3"><label class="col-form-label d-inline" for="id_sepattempt">Separate Attempt</label></div><div class="col-md-9 form-inline felement"><input type="checkbox" value="'+i+'" name="separateattempt[]" id="id_sepattempt"><div class="form-control-feedback" id="id_error_sepattempt"></div></div></div>';
					divWrap.appendChild(newdiv4);
				}

				var idname = "#cross" + i;
				var divname = "#div" + i;
				$(idname).click(function(){
					$(divname).remove();
				});
                
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

		<script>
			//form validation
			$(document).ready(function () {
				$('#quizForm').validate({ // initialize the plugin
					rules: {
						"name": {
							required: true,
							minlength: 1,
							maxlength: 100
						},
						"description": {
							maxlength: 100
						},
						"quesname[]": {
							required: true,
							minlength: 1,
							maxlength: 100
						},
						"ques_text[]": {
							maxlength: 800
						},
						"maxmark[]": {
							number: true,
							required: true,
							step: 0.001,
							range: [0, 100],
							min: 0,
							max: 100,
							minlength: 1,
							maxlength: 7
						},
						"clo[]": {
							required: true
						}
					},
					messages: {
						"name": {
							required: "Please enter name.",
							minlength: "Please enter more than 1 characters.",
							maxlength: "Please enter no more than 100 characters."
						},
						"description": {
							maxlength: "Please enter no more than 100 characters."
						},
						"quesname[]": {
							required: "Please enter name.",
							minlength: "Please enter more than 1 characters.",
							maxlength: "Please enter no more than 100 characters."
						},
						"ques_text[]": {
							maxlength: "Please enter no more than 800 characters."
						},
						"maxmark[]": {
							number: "Only numeric values are allowed.",
							required: "Please enter max marks.",
							step: "Please enter nearest max marks value.",
							range: "Please enter max marks between 0 and 100%.",
							min: "Please enter max marks greater than or equal to 0.",
							max: "Please enter max marks less than or equal to 100.",
							minlength: "Please enter more than 1 numbers.",
							maxlength: "Please enter no more than 6 numbers (including decimal part)."
						},
						"clo[]": {
							required: "Please select CLO."
						}
					}
				});
			});
		</script>

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

<?php

	class Blob{
  
    protected $DB_HOST = '';
    protected $DB_NAME = '';
    protected $DB_USER = '';
    protected $DB_PASSWORD='';
 
    /**
     * Open the database connection
     */
    public function __construct($x,$dbh,$dbn,$dbu) {
      //echo "$x";
      $DB_HOST=$dbh;
      $DB_NAME = $dbn;
      $DB_USER = $dbu;
      $DB_PASSWORD=$x;
        // open database connection
        $conStr = sprintf("mysql:host=%s;dbname=%s;charset=utf8", $DB_HOST, $DB_NAME);
 
        try {
            $this->pdo = new PDO($conStr, $DB_USER, $DB_PASSWORD);
            //for prior PHP 5.3.6
            //$conn->exec("set names utf8");
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
 

    function updateBlob($id, $filePath, $mime) {
 
        $blob = fopen($filePath, 'rb');
 		//echo "$id";
        $sql = "UPDATE mdl_manual_quiz
                SET mime = :mime,
                    data = :data
                WHERE id = :id";
 
        $stmt = $this->pdo->prepare($sql);
 
        $stmt->bindParam(':mime', $mime);
        $stmt->bindParam(':data', $blob, PDO::PARAM_LOB);
        $stmt->bindParam(':id', $id);
 
        return $stmt->execute();
    }






    /**
     * close the database connection
     */
    public function __destruct() {
        // close the database connection
        $this->pdo = null;
    }
}
