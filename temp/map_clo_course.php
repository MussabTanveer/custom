<?php
	require_once('../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("OBE Frameworks");
    $PAGE->set_heading("OBE Framework Selection");
    $PAGE->set_url($CFG->wwwroot.'/custom/select_framework_to_map_clo_course.php');
    
    echo $OUTPUT->header();
    require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());
    global $SESSION;
    $time = time();
    
 
	if((isset($_POST['submit']) && isset($_POST['frameworkid']))  || (isset($SESSION->fid10) && $SESSION->fid10 != "xyz"))
    {
    	if(isset($SESSION->fid10) && $SESSION->fid10 != "xyz")
		{
			$frameworkid=$SESSION->fid10; 
			$SESSION->fid10="xyz";
		}
		else
			$frameworkid=$_POST['frameworkid'];
    		//echo "$frameworkid";

		$courseid=$SESSION->courseid;
 		//echo "$courseid";

		$course=$DB->get_records_sql('SELECT * FROM `mdl_course` 
    		WHERE id = ? ',
    		 array($courseid));

		if ($course != NULL){
 		 	foreach ($course as $rec) {
				$id =  $rec->id;
				$idnumber =  $rec->idnumber;
			}
		}	

		$count=0;
		//echo "$frameworkid";
		//echo "$idnumber";

		$competencies=$DB->get_records_sql("SELECT * FROM `mdl_competency` 
    		WHERE idnumber like '{$idnumber}%' 
    		AND competencyframeworkid =? ",
    		 array($frameworkid));

		$flag=false;

 		if ($competencies != NULL){
 		 	foreach ($competencies as $rec) {
				$id =  $rec->id;
				$idnumber =  $rec->idnumber;
				//echo "$idnumber";

				$check=$DB->get_records_sql("SELECT * FROM `mdl_competency_coursecomp`
							WHERE courseid = ?
							AND competencyid =? ",
							array($courseid,$id));

				if ($check == NULL)
				{	
					$flag=true;
				
					$sql="INSERT INTO mdl_competency_coursecomp (courseid, competencyid,ruleoutcome,timecreated,timemodified,usermodified,sortorder) VALUES ('$courseid', '$id','1','$time','$time', '$USER->id','0')";
					$DB->execute($sql);
					
				}

			}

			if($flag == true)
			{
				echo " <font color='green'>CLOs successfully mapped with the course </font>";
			}
			

		}

		else 
		{	echo " <font color='red'>No CLOs of this course have been added to the framework</font>";

			goto end;
		}


		if ($flag == false)
 		{
 			echo " <font color='green'>CLOs are already mapped with the course </font>";
		}
 		
		end:

 	}
?>
<br> </br>
 	<a href="./select_course.php">Back</a>
