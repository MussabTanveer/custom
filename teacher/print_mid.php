 <?php 
  require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Print Quiz");
    $PAGE->set_heading("Print Quiz");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/print_quiz.php');

    require('../script/fpdf/fpdf.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }

    if(isset($_GET['quiz']))
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


        $quizid=$_GET['quiz'];
        //echo "$quizid";
        $courseid = $_GET['courseid'];
       // echo "$courseid";
       
        	//query to get all question
         $questions= $DB->get_records_sql("SELECT * FROM mdl_manual_quiz_question WHERE mquizid = ?",array($quizid));

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
   		 $pdf->Cell(190,10,"$uniName",0,2,'C');

   		 $pdf->Cell(190,10,"$deptName",0,2,'C');
   		 $pdf->SetFont('Arial','BU',10);
             $pdf->Cell(190,10,"Midterm Examination",0,2,'C');
	   	 $pdf->Cell(190,10,"($courseIdNumber) $courseFullName",0,2,'C');

	   	 $pdf->SetFont('Arial','',10);
	   	 $x=$pdf->GetX();
	   	 $pdf->Cell(80,10,"Name: _____________________________",0,0);
	 	
	   	 $pdf->Cell(110,10,"Marks Obtained: _________",0,2,'R');

	   	  $y=$pdf->GetY();
	   	  
	     $pdf->SetXY($x,$y);
	   	 $pdf->Cell(80,10,"RollNo: _____________________________",0,0);

	   	 $closArray=array();
   	
        if($questions)
        { 

        	$totalMarks=0;
        	foreach ($questions as $ques) 
            {	 
            	
            	$qmark = $ques->maxmark;
            	$totalMarks += $qmark;
            	$cloid = $ques->cloid;

            	 $clos= $DB->get_records_sql("SELECT * FROM mdl_competency WHERE id = ?",array($cloid));
            	 
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
            $pdf->Cell(50,10,"Time: 60 Mins",0,2);
             $y=$pdf->GetY();
             $x=$pdf->GetX();	   	  
	   		 $pdf->SetXY($x+64,$y);
	   		 $pdf->SetFont('Arial','',13);

	   		 $i=0;

            foreach ($questions as $ques) 
            {
            	
                # code...
                $qname = $ques->quesname;
              	$qmark = $ques->maxmark;
                $qtext = $ques->questext;
                $qid   = $ques->id;
               
 				 
 				 $x=$pdf->GetX();
 				 $pdf->write(10,"$qname. $qtext     [$qmark] ($closArray[$i])");
 				 $y=$pdf->GetY();
 				 $pdf->SetXY($x,$y);
 				 $i++;                   
            }
             $pdf->Output();

        }
    }