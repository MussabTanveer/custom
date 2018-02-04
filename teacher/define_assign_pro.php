<?php
require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Manual Assignment/Project");
    $PAGE->set_heading("Define Manual Assignment/Project");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/define_assign_pro.php');
    
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

<link rel="stylesheet" href="../css/datepicker/wbn-datepicker.css">
<script src="../script/jquery/jquery-3.2.1.js"></script>

<?php
    
    if(!empty($_GET['type']) && !empty($_GET['course']))
    {
        $course_id=$_GET['course'];
        //echo "Course ID : $course_id";
        $type=$_GET['type'];
        //echo "$type";

		/* if user press save */
		if(isset($_POST['save'])) {
            $apname = trim($_POST["name"]);
            $apdesc = trim($_POST["description"]);
            $apmaxmark = trim($_POST["maxmark"]);
			$apclo = trim($_POST["clo"]);
			$startdate=strtotime($_POST['startdate']);
            $enddate=strtotime($_POST['enddate']);

			try {
				$transaction = $DB->start_delegated_transaction();
				$record = new stdClass();
				$record->courseid = $course_id;
				$record->name = $apname;
				$record->description = $apdesc;
				$record->maxmark = $apmaxmark;
				$record->cloid = $apclo;
				$record->startdate = $startdate;
	            $record->enddate = $enddate;
	            if($type=="assign"){
	            	$record->module=-4;
	            }
	            elseif($type=="project"){

	            	$record->module=-5;
	            }

				$assign_pro_id = $DB->insert_record('manual_assign_pro', $record); // get assign/pro id of newly inserted record

				// Insert this assign/pro id in mdl_grading_mapping table according to type (assignment, project) which is in $type variable above

				// Automated mapping code starts from here
				if($type == "assign"){
                  

					$reca=$DB->get_records_sql('SELECT id as assign_id FROM mdl_grading_policy WHERE name="assignment" AND courseid=?',array($course_id));
					if($reca){
						foreach ($reca as $recorda) {
							$assign_id=$recorda->assign_id; 
						}
						echo $assign_id;
						$sql="INSERT INTO mdl_grading_mapping (courseid,module,instance,gradingitem) VALUES 
						('$course_id',-4,'$assign_pro_id','$assign_id') ";
						$DB->execute($sql);
					}
					else{
						$msga="Pls define Assignment in Define Grading Policy tab first";
					}
				}
				elseif($type == "project"){

				

					$recp=$DB->get_records_sql('SELECT id as project_id FROM mdl_grading_policy WHERE name="project" AND courseid=?',array($course_id));
					if($recp){
						foreach ($recp as $recordp) {
							$project_id=$recordp->project_id; 
						}
						$sql="INSERT INTO mdl_grading_mapping (courseid,module,instance,gradingitem) VALUES 
						('$course_id',-5,'$assign_pro_id','$project_id') ";
						$DB->execute($sql);
					}
					else{
						$msgp="Pls define Project in Define Grading Policy tab first";
					}
				}
				$transaction->allow_commit();
				} catch(Exception $e) {
					$transaction->rollback($e);
			}
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
		
		<form method='post' action="" class="mform" id="cloForm">
            
            <?php
            if($type == "assign"){
                ?>
                <h3>Assignment</h3>
                <?php
            }
            elseif($type == "project"){
                ?>
                <h3>Project</h3>
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
							name="maxmark"
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
                    <select required onChange="dropdownTip(this.value, 0)" name="clo" class="select custom-select">
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
            
			<div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                    </span>
                    <label for="id_startdate">
                        Start Date
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
						required
                        class="form-control wbn-datepicker"
                        name="startdate"
                        id="id_startdate"
                        size="27"
                        maxlength="100" >
                    <div class="form-control-feedback" id="id_error_idnumber">
                    </div>
                </div>
            </div>

            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                    </span>
                    <label for="id_enddate">
                        Due Date
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
						required
                        class="form-control wbn-datepicker"
                        name="enddate"
                        id="id_enddate"
                        data-start-src="id_startdate"
                        size="27"
                        maxlength="100" >
                    <div class="form-control-feedback" id="id_error_idnumber">
                    </div>
                </div>
            </div>
            <br />
			
			<button class="btn btn-info" type="submit"  name="save" id="button" /> Save </button>
            <a class="btn btn-default" type="submit" href="./report_teacher.php?course=<?php echo $course_id ?>">Cancel</a>
			<br /><br />
			<div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>
		</form>
		
		<?php
		if(isset($msga)){
			echo $msga;
		}
		elseif(isset($msgp)){
			echo $msgp;
		}
		?>
				
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

		<script src="../script/datepicker/wbn-datepicker.min.js"></script>
		<script type="text/javascript">
			$(function () {
			$('.wbn-datepicker').datepicker()
		
			var $jsDatepicker = $('#value-specified-js').datepicker()
			$jsDatepicker.val('2017-05-30')
			})
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
