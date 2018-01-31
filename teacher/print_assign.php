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

 if(isset($_GET['assign']))
    {
        $assignid=$_GET['assign'];
      //  echo "$assignid";
        $courseid = $_GET['courseid'];
       // echo "$courseid";
       
        //query to get Assignment details!
         $assigns= $DB->get_records_sql("SELECT * FROM mdl_manual_assign_pro WHERE id = ?",array($assignid));

         	//query to get course relative stuff!
          $courses= $DB->get_records_sql("SELECT * FROM mdl_course WHERE id = ?",array($courseid));



 			foreach ($courses as $course) 
            {
                # code...
                $courseFullName = $course->fullname;
                $courseIdNumber = $course->idnumber;
            }

   		 $pdf = new FPDF();
  		  $pdf->AddPage();
  		   $pdf->SetFont('Arial','',10);
   		 $pdf->Cell(190,10,"NED University of Engineering and Technology",0,2,'C');

   		 $pdf->Cell(190,10,"Computer & Information Systems Engineering Department",0,2,'C');
   		 $pdf->SetFont('Arial','BU',10);
	   	 $pdf->Cell(190,10,"($courseIdNumber) $courseFullName",0,2,'C');

	   	 $pdf->SetFont('Arial','',10);
	   	 $x=$pdf->GetX();
	   	 $pdf->Cell(80,10,"Name: _____________________________",0,0);
	 	
	   	 $pdf->Cell(110,10,"Marks Obtained: _________",0,2,'R');

	   	  $y=$pdf->GetY();
	   	  
	     $pdf->SetXY($x,$y);
	   	 $pdf->Cell(80,10,"RollNo: _____________________________",0,0);

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

             $y=$pdf->GetY();
             $x=$pdf->GetX();	   	  
	   		 $pdf->SetXY($x+64,$y);
             $pdf->Cell(50,10,"Max Marks: $totalMarks",0,2);
             $y=$pdf->GetY();
             $x=$pdf->GetX();	   	  
	   		 $pdf->SetXY($x+64,$y);
	   		 $pdf->SetFont('Arial','',13);

	   		 $i=0;

            foreach ($assigns as $assign) 
            {
            	
                # code...
                $aname = $assign->name;
              	$adesc = $assign->description;
                $amark = $assign->maxmark;
                $aid   = $assign->id;
                $acloid   = $assign->cloid;
                $astartdate  = $assign->startdate;
                $aEnddate   = $assign->enddate;
                
               
 				 
 				 $x=$pdf->GetX();
 				 $pdf->write(10,"Name: $aname",0);
                 
                
                 $y=$pdf->GetY();
                 $pdf -> SetXY($x,$y+5);
                 $pdf->write(10,"Description: $adesc",0);
 				 $y=$pdf->GetY();
 				 $pdf->SetXY($x,$y);
                  $pdf->write(10,"Marks: $amark",0);
                   $y=$pdf->GetY();
                 $pdf->SetXY($x,$y);
                  $pdf->write(10,"Competency: $closArray[$i]",0);
                   $y=$pdf->GetY();
                 $pdf->SetXY($x,$y);
                  $pdf->write(10,"StartDate: $astartdate",0);
                   $y=$pdf->GetY();
                 $pdf->SetXY($x,$y);
                  $pdf->write(10,"EndDate: $aEnddate",0);
 				 $i++;                   
            }
             $pdf->Output();

        }
    }