<?php
require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Online Grading Form");
    $PAGE->set_heading("Online Grading Form");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/grading_form_quiz_selection.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();



$mrkobt = $_GET['w1'];
$mrkobt = explode(",",$mrkobt);
//var_dump ($mrkobt);
echo "<br>";

$chunkSize = $_GET['chunkSize'];
//echo "$chunkSize<br>";
//$chunkSize = explode(",",$chunkSize);

$stdids = $_GET['sid'];
//echo "Stud IDS <br>$stdids <br>";
$stdids = explode(",",$stdids);
//var_dump ($stdids);
echo "<br>";


$qids = $_GET['qid'];
$qids = explode(",",$qids);
//var_dump ($qids);
echo "<br>";
//var_dump($mrkobt);

$i=$chunkSize ;
$qidx=0;
 for ($j=0 ; $j<sizeof($stdids); $j++){
	 $flag=0;
	// echo "$stdids[$j]";
 
 for (; $i<sizeof($mrkobt) ; $i++)
 {	 $flag++;
	//if ($flag%$chunkSize==0)
	//{
		// $stdIndx = $i-$chunkSize;
		//echo "$stdIndx";
	//	$flag=1;
	//}
	
	// echo "$stdids[$stdIndx]<br>";
	if($flag == 1 )
		continue;
	

	/*echo "<br><br>";
	echo "i =$i<br>";
		echo "flag = $flag<br>";
	 echo "$mrkobt[$i]<br>";
	 echo "Ques index $qids[$qidx]";
	 echo "<br><br>";*/
	// echo "$mrkobt[$i]<br>";
	// $mrkobt[$i]=$mrkobt[$i] . " ";
	// echo "$mrkobt[$i]<br>";
	 if ($mrkobt[$i] > 0)
	 
			$sql="INSERT INTO mdl_manual_quiz_attempt (quizid,userid,questionid,obtmark) VALUES ('23','$stdids[$j]','$qids[$qidx]','$mrkobt[$i]')";
	   
	else
		$sql="INSERT INTO mdl_manual_quiz_attempt (quizid,userid,questionid,obtmark) VALUES ('23','$stdids[$j]','$qids[$qidx]','0')";

		$DB->execute($sql);
	 
	  $qidx++;
	  
	if($flag == $chunkSize)
	{   $i++; 
		$qidx =0;
		break; 
		}
 
 
 }
 }
  echo "<font color = green > DATA SUBMITTED </font> <br>";

	?>
<a href="./teacher_courses.php">Back</a>
	<?php

 echo $OUTPUT->footer();

?>