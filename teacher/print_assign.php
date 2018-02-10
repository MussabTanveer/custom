 <?php 
  require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Print Assignment");
    $PAGE->set_heading("Print Assignment");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/print_assign.php');

    require('../script/fpdf/fpdf.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }

    if(!empty($_GET['assign']) && !empty($_GET['courseid']))
    {

       $dn=$DB->get_records_sql('SELECT id FROM  `mdl_vision_mission` WHERE idnumber = ?', array("dn"));
        $un=$DB->get_records_sql('SELECT id FROM  `mdl_vision_mission` WHERE idnumber = ?', array("un"));

         if($un){

        foreach($un as $u){
            $id = $u->id;

        }

         $un=$DB->get_records_sql('SELECT description FROM  `mdl_vision_mission` WHERE id = ?', array($id));

        foreach($un as $u){
            $uniName = $u->description;
        }
                       
    }


     if($dn){

        foreach($dn as $u){
            $id = $u->id;

        }

         $dn=$DB->get_records_sql('SELECT description FROM  `mdl_vision_mission` WHERE id = ?', array($id));

        foreach($dn as $u){
            $deptName = $u->description;
        }           
     }

        $deptName = strip_tags($deptName);
        $uniName = strip_tags($uniName);

        $assignid=$_GET['assign'];
      //  echo "$assignid";
        $course_id = $_GET['courseid'];
        $coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die($OUTPUT->header().'<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
       // echo "$course_id";
       
        //query to get Assignment details!
         $assigns= $DB->get_records_sql("SELECT * FROM mdl_manual_assign_pro WHERE id = ?",array($assignid));

         	//query to get course relative stuff!
          $courses= $DB->get_records_sql("SELECT * FROM mdl_course WHERE id = ?",array($course_id));



 			foreach ($courses as $course) 
            {
                # code...
                $courseFullName = $course->fullname;
                $courseIdNumber = $course->idnumber;
            }

   		 $pdf = new FPDF();
  		  $pdf->AddPage();
  		   $pdf->SetFont('Arial','',10);
   		 $pdf->Cell(190,10,"$uniName",0,2,'C');

   		 $pdf->Cell(190,10,"$deptName",0,2,'C');
   		 $pdf->SetFont('Arial','BU',10);
	   	 $pdf->Cell(190,10,"($courseIdNumber) $courseFullName",0,2,'C');

	   	// $pdf->SetFont('Arial','',10);
	   	// $x=$pdf->GetX();
	   	 //$pdf->Cell(80,10,"Name: _____________________________",0,0);
	 	
	   	// $pdf->Cell(110,10,"Marks Obtained: _________",0,2,'R');

	   	//  $y=$pdf->GetY();
	   	  
	    // $pdf->SetXY($x,$y);
	   	// $pdf->Cell(80,10,"RollNo: _____________________________",0,0);

	   	 $closArray=array();
   	
        if($assigns)
        { 

        	$totalMarks=0;
        	foreach ($assigns as $assign) 
            {	 
            	 $totalMarks = $assign->maxmark;
            	 $acloid   = $assign->cloid;
            	 $clos= $DB->get_records_sql("SELECT * FROM mdl_competency WHERE id = ?",array($acloid));
            	 
       		 foreach ($clos as $clo) 
            {

            	$shortname = $clo->shortname;
            	array_push($closArray, $shortname);
            }

            }

           // var_dump($closArray);

           //  $y=$pdf->GetY();
           //  $x=$pdf->GetX();	   	  
	   	//	 $pdf->SetXY($x+64,$y);
           //  $pdf->Cell(50,10,"Max Marks: $totalMarks",0,2);
             $y=$pdf->GetY();
             $x=$pdf->GetX();	   	  
	   		 $pdf->SetXY($x-30,$y);
	   		 $pdf->SetFont('Arial','B',13);

	   		 $i=0;

            foreach ($assigns as $assign) 
            {
            	
                # code...
                $aname = $assign->name;
              	$adesc = $assign->description; $adesc = strip_tags($adesc);
                $amark = $assign->maxmark;
                $aid   = $assign->id;
                $acloid   = $assign->cloid;
                $astartdate  = $assign->startdate;
                $aEnddate   = $assign->enddate;
               // echo "$astartdate";
                 $astartdate = date('d-m-y' ,$astartdate);
                $aEnddate = date('d-m-y' ,$aEnddate);   
                 $x=$pdf->GetX();
         				 $pdf->write(10,"Name: ",0);
                 $pdf->SetFont('Arial','',13);
                 $pdf->write(10,"$aname",0);
                     
                
                 $y=$pdf->GetY();
                 $pdf -> SetXY($x,$y);
                $pdf->SetFont('Arial','B',13);
                 $pdf->write(10,"Description:",0);
                  $pdf->SetFont('Arial','',13);
                 $pdf->write(10,"$adesc",0);
         				 $y=$pdf->GetY();
         				 $pdf->SetXY($x,$y);
                 $pdf->SetFont('Arial','B',13);
                  $pdf->write(10,"Max Marks: ",0);
                  $pdf->SetFont('Arial','',13);
                  $pdf->write(10,"$amark",0);
                   $y=$pdf->GetY();
                 $pdf->SetXY($x,$y);
                 $pdf->SetFont('Arial','B',13);
                  $pdf->write(10,"CLO: ",0);
                  $pdf->SetFont('Arial','',13);
                   $pdf->write(10,"$closArray[$i]",0);
                   $y=$pdf->GetY();
                 $pdf->SetXY($x,$y);
                 $pdf->SetFont('Arial','B',13);
                  $pdf->write(10,"StartDate: ",0);
                   $pdf->SetFont('Arial','',13);
                  $pdf->write(10,"$astartdate ",0);

                   $y=$pdf->GetY();
                 $pdf->SetXY($x,$y);
                   $pdf->SetFont('Arial','B',13);
                  $pdf->write(10,"EndDate: ",0);
                   $pdf->SetFont('Arial','',13);
                  $pdf->write(10,"$aEnddate ",0);
 				 $i++;                   
            }
             $pdf->Output();

        }
    }
    else{
        echo $OUTPUT->header();
        ?>
            <h2 style="color:red;"> Invalid Selection </h2>
            <a href="./teacher_courses.php">Back</a>
        <?php
        echo $OUTPUT->footer();
    }