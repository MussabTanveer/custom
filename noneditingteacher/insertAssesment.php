<?php
	require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Assessment Marks");
    $PAGE->set_heading("Assessment Marks");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/noneditingteacher/insertAssesment.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

	$mrkobt = $_GET['w1'];
	$mrkobt = explode(",",$mrkobt);
	////echo "Marks";
	//var_dump ($mrkobt);
	////echo "<br>";

	$chunkSize = $_GET['chunkSize'];
	//echo "Chunk: $chunkSize<br>";
	//$chunkSize = explode(",",$chunkSize);

	$stdids = $_GET['sid'];
	//echo "Stud IDS <br>$stdids <br>";
	$stdids = explode(",",$stdids);
	//echo "Std ID:";
	//var_dump ($stdids);
	//echo "<br>";


	$cids = $_GET['cid'];
	$cids = explode(",",$cids);
	//echo "CID";
	//var_dump ($cids);
	//echo "<br>";
	//var_dump($mrkobt);

	//$quizID = $_GET['quizid'];
	$aid = $_GET['aid'];
	//echo "$aid";

	$i=$chunkSize ;
	//echo "<br>$i";
	$i++;
	$qidx=0;
	for ($j=0 ; $j<sizeof($stdids); $j++){
		$flag=0;
		//echo "J =$j";
		//echo "$stdids[0]";
		//echo "$stdids[$j]";
	
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
			echo "Criterion index $cids[$qidx]";
			echo "<br><br>";
			echo "$mrkobt[$i]<br>";*/

			// $mrkobt[$i]=$mrkobt[$i] . " ";
			// echo "$mrkobt[$i]<br>";
			
			if ($mrkobt[$i] > 0)
				$sql="INSERT INTO mdl_assessment_attempt (aid,userid,cid,obtmark) VALUES ('$aid','$stdids[$j]','$cids[$qidx]','$mrkobt[$i]')";
			else
				$sql="INSERT INTO mdl_assessment_attempt (aid,userid,cid,obtmark) VALUES ('$aid','$stdids[$j]','$cids[$qidx]','0')";
			$DB->execute($sql);
			
			$qidx++;
			
			if($flag == $chunkSize+1)
			{   $i++; 
				$qidx =0;
				break; 
			}
		}
	}
	echo "<font color = green > DATA SUBMITTED </font> <br>";
	?>
	<a href="../teacher/teacher_courses.php">Back</a>
	<?php
	echo $OUTPUT->footer();
	?>