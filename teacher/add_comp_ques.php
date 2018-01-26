<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Select CLO");
    $PAGE->set_heading("Select Activity CLO");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/add_comp_ques.php');
    
	require_login();
	if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
	}
	echo $OUTPUT->header();

    if(isset($_POST['submit']) && isset( $_POST['activityid']) && isset( $_POST['courseid']))
    {
        $activity_id=$_POST['activityid'];
        //echo "Quiz ID : $quiz_id";
		$course_id=$_POST['courseid'];
        //echo "Course ID : $course_id";
        ?>
		
		<?php
		if(substr($activity_id,0,1) == 'Q'){
		//Get course comp
		/*$recordsComp=$DB->get_records_sql("SELECT c.shortname, c.id
    
        FROM mdl_competency_coursecomp cc, mdl_competency c

        WHERE cc.courseid = ? AND cc.competencyid = c.id",
        
		array($course_id));*/
		
		//Get course clo with its plo and peo
		//('SELECT clo.shortname AS cname, levels.name AS lname, levels.level AS lvl FROM mdl_competency clo, mdl_taxonomy_levels levels, mdl_taxonomy_clo_level clolevel WHERE clo.id=clolevel.cloid AND levels.id=clolevel.levelid AND clolevel.frameworkid = ?', array($fw_id, $fw_id));
		$recordsComp=$DB->get_records_sql(
		"SELECT clo.id AS cloid, clo.shortname AS cloname, plo.shortname AS ploname, peo.shortname AS peoname, levels.name AS lname, levels.level AS lvl
    
        FROM mdl_competency_coursecomp cc, mdl_competency clo, mdl_competency plo, mdl_competency peo, mdl_taxonomy_levels levels, mdl_taxonomy_clo_level clolevel

		WHERE cc.courseid = ? AND cc.competencyid=clo.id  AND peo.id=plo.parentid AND plo.id=clo.parentid AND 
		clo.id=clolevel.cloid AND levels.id=clolevel.levelid",
		
		array($course_id));
		
		$closid = array(); $plos = array(); $peos = array(); $levels = array(); $lvlno = array();
		foreach ($recordsComp as $recC) {
			$cid = $recC->cloid;
			$clo = $recC->cloname;
			$plo = $recC->ploname;
			$peo = $recC->peoname;
			$lname = $recC->lname;
			$lvl = $recC->lvl;
			array_push($closid, $cid); // array of clo ids
			array_push($plos, $plo); // array of plos
			array_push($peos, $peo); // array of peos
			array_push($levels, $lname); // array of levels
			array_push($lvlno, $lvl); // array of level nos
		}

		/*$arrlength = count($clos);
		for($x = 0; $x < $arrlength; $x++) {
			echo $clos[$x];
			echo $plos[$x];
			echo $peos[$x];
			echo "<br>";
		}*/
		
		// Get Quiz Questions
        $rec=$DB->get_recordset_sql(
        'SELECT
			qu.id,
            qu.name,
            qu.questiontext

        FROM
            mdl_quiz q,
            mdl_quiz_slots qs,
            mdl_question qu
        WHERE
            q.id=? AND q.id=qs.quizid AND qu.id=qs.questionid',
        
        array(substr($activity_id,1)));
		
		?>
		
		<!-- Display Quiz Questions & select competency -->
		<form action="confirm_comp_ques.php" method="post" >
			<table class="generaltable">
				<tr class="table-head">
					<th> Question Name </th>
					<th> Question </th>
					<th> Select CLO </th>
					<th> PLO </th>
					<!--<th> PEO </th>-->
					<th> Taxonomy Level </th>
				</tr>
				<?php
				$qidarray=array();
				$i = 0;
				foreach($rec as $records)
				{
					$qid = $records->id;
					$qname = $records->name;
					$qtext = $records->questiontext;
					
					array_push($qidarray,$qid);
					
				?>
							
				<tr>
					<td><?php echo $qname;?> </td>
					<td><?php echo $qtext;?></td>
					<td>
						<select onChange="dropdownTip(this.value, <?php echo $i ?>)" name="competency[]" class="select custom-select">
							<option value='NULL'>Choose..</option>
							<?php
							foreach ($recordsComp as $recC) {
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
					</td>
					<td id="plo<?php echo $i ?>"></td>
					<!--<td id="peo<?php echo $i ?>"></td>-->
					<td id="tax<?php echo $i ?>"></td>
				</tr>
				<?php
					$i++;
					}
					global $SESSION;
					$SESSION->qidarray = $qidarray;
					
					?>
			</table>
			
			<input type="submit" value="NEXT" name="ok" class="btn btn-primary">
			
		</form>
		<?php
		$rec->close(); // Don't forget to close the recordset!
		echo $OUTPUT->footer();
		?>
		<script>
			var closid = <?php echo json_encode($closid); ?>;
			var plos = <?php echo json_encode($plos); ?>;
			//var peos = <?php echo json_encode($peos); ?>;
			var levels = <?php echo json_encode($levels); ?>;
			var levelnos = <?php echo json_encode($lvlno); ?>;
			/*alert(closid);
			alert(plos);
			alert(peos);*/
			function dropdownTip(value,id){
				var plo = "plo" + id;
				//var peo = "peo" + id;
				var tax = "tax" + id;
				//console.log(value);
				//console.log(id);
				if(value == 'NULL'){
					document.getElementById(plo).innerHTML = "";
					//document.getElementById(peo).innerHTML = "";
					document.getElementById(tax).innerHTML = "";
				}
				else{
					for(var i=0; i<closid.length ; i++){
						if(closid[i] == value){
							document.getElementById(plo).innerHTML = plos[i];
							//document.getElementById(peo).innerHTML = peos[i];
							document.getElementById(tax).innerHTML = levels[i] + " (" + levelnos[i] + ")";
							break;
						}
					}
				}
			}
		</script>
		<?php
		}


		else if(substr($activity_id,0,1) == 'A'){
			echo "in assignment";
			echo $OUTPUT->footer();
		}


    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./display_courses-2.php">Back</a>
    <?php 
        echo $OUTPUT->footer();
    }?>
