<script src="../script/jquery/jquery-3.2.1.js"></script>

<?php
    require_once('../../../config.php');
    require_once('../script/spout-2.4.3/spout-2.4.3/src/Spout/Autoloader/autoload.php');
    //require_once('../script/class/Classes/PHPExcel.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Upload Marks");
    $PAGE->set_heading("Upload Marks");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/noneditingteacher/upload_marks.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    use Box\Spout\Reader\ReaderFactory;
    use Box\Spout\Common\Type;
    
    if(!empty($_GET['assessmentid']) && !empty($_GET['course']))
    {
        $course_id=$_GET['course'];
        $coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
    ?>

    <form id="uploadMarks" method="POST" enctype="multipart/form-data" class="mform">

         <div class="btn btn-default btn-file">
            <input  type="file" name="assessmentmarks" id="assessmentmarks" placeholder="Only excel files are allowed!">
        </div>
        <input class="btn btn-info" type="submit" name="Upload" value="Upload" >
        
    </form>

    <?php
        $aid=$_GET['assessmentid'];    

        $check=$DB->get_records_sql('SELECT *  FROM mdl_assessment_attempt WHERE aid = ?', array($aid));
        
        if($check){
            echo "<font color=red>Sorry, cannot upload marks because they have already been uploaded!</font>";
            goto end;
        }

        // check file name is not empty
        if (!empty($_FILES['assessmentmarks']['name'])) {
            
            // Get File extension eg. 'xlsx' to check file is excel sheet
            $pathinfo = pathinfo($_FILES["assessmentmarks"]["name"]);
            
            // check file has extension xlsx, xls and also check 
            // file is not empty
            if (($pathinfo['extension'] == 'xlsx' || $pathinfo['extension'] == 'xls') 
            && $_FILES['assessmentmarks']['size'] > 0 ) {
            
                // Temporary file name
                $inputFileName = $_FILES['assessmentmarks']['tmp_name']; 
            
                // Read excel file by using ReadFactory object.
                $reader = ReaderFactory::create(Type::XLSX);
        
                // Open file
                $tempfile=$reader->open($inputFileName);
                $count = 1;
                $abc=1;
                    
                //Number of sheet in excel file
                foreach ($reader->getSheetIterator() as $sheet) {
                    if($abc>=1){
                    
                    // Number of Rows in Excel sheet
                    foreach ($sheet->getRowIterator() as $row) {
                        if($count==1){
                            $c1=count($row);
                            for($x=1;$x<$c1;$x++){
                            //$prefix = "pre";
                            //${$prefix.strtolower($x)}=$row[$x];
                            $quesids=$row[$x];
                            $rec1=$DB->get_records_sql('SELECT id  FROM mdl_manual_quiz_question WHERE quesname = ? AND mquizid = ?', array($quesids, $aid));
                            if($rec1){
                                $a="question";
                                foreach ($rec1 as $record1){
                                ${$a.strtolower($x)}=$record1->id;}
                                
                            }
                            // echo ${$prefix.strtolower($x)};
                            // $a=$row[1];
                            // $b=$row[2];
                            // $c=$row[3];
                            // $d=$row[4];
                            }
                        }
    
                        // It reads data after header. In the my excel sheet, 
                        // header is in the first row. 
                        if ($count > 1) {
                        
                        //$arri = array_map('strval', $arr);
                        
                        // Data of excel sheet
                        $c1=count($row);
                        $sn=$row[0];
                        $rec=$DB->get_records_sql('SELECT id  FROM mdl_user WHERE username = ?', array($sn));
                        if ($rec){
                            foreach($rec as $records){
                                $uid=$records->id;
                            }
                        }
                        else{
                            $uid="A";
                        }
                        for($x=1;$x<$c1;$x++){                    
                
                            $pfix="sn";
                            ${$pfix.strtolower($x)}=$row[$x];
                        
                            //echo ${$pfix.strtolower($x)};
                            // $sn1=$row[1];
                            // $sn2=$row[2];
                            // $sn3=$row[3];
                            // $sn4=$row[4];
                            if (${$pfix.strtolower($x)} <> "" && $uid <> "A" ){
                                $sql1="INSERT INTO mdl_manual_quiz_attempt (quizid,userid,questionid,obtmark) VALUES('$qid','$uid','${$a.strtolower($x)}','${$pfix.strtolower($x)}')";
                                $DB->execute($sql1);
                            }
                            elseif (${$pfix.strtolower($x)} == "" && $uid <> "A" ){
                                $sql1="INSERT INTO mdl_manual_quiz_attempt (quizid,userid,questionid,obtmark) VALUES('$qid','$uid','${$a.strtolower($x)}',0)";
                                $DB->execute($sql1);
                            }
                        }
                    }
                    $count++;
                }}
                $abc++;
            }
            if($sql1){
                echo "<h3 style='color:green;'>Result has been uploaded!</h3>";
            }
            //Close excel file
            $reader->close();
        } else {
            echo "<font color=red>Please Select Valid Excel File</font>";
        }
    } else {
        echo "<font color=red>Please Select Excel File</font>";
    }
    }




    else
    {?>
        <h3 style="color:red;"> Invalid Selection </h3>
        <a href="./teacher_courses.php">Back</a>
        <?php
    }
    end:
    
    echo $OUTPUT->footer();
?>