<?php
    require_once('../../../config.php');
    require_once('../script/spout-2.4.3/spout-2.4.3/src/Spout/Autoloader/autoload.php');
    //require_once('../script/class/Classes/PHPExcel.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Upload Results");
    $PAGE->set_heading("Upload Results");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/noneditingteacher/upload_results.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <?php
    use Box\Spout\Reader\ReaderFactory;
    use Box\Spout\Common\Type;
    
    if(!empty($_GET['assessmentid']) && !empty($_GET['course']))
    {
        $course_id=$_GET['course'];
        $coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
    ?>

    

    <?php


      $rec=$DB->get_recordset_sql('SELECT
            clo.id AS cloid,
            clo.shortname,
            clo.idnumber,
            clo.description,
            plo.shortname as ploname,
            plo.idnumber,
            taxlvl.id AS lvlid,
            taxlvl.name,
            taxlvl.level,
            taxdom.name as taxname
            FROM
            mdl_competency clo,
            mdl_competency plo, 
            mdl_competency_coursecomp compcour,
            mdl_taxonomy_clo_level taxclolvl,
            mdl_taxonomy_levels taxlvl,
            mdl_taxonomy_domain taxdom
            WHERE clo.id=compcour.competencyid and clo.id=taxclolvl.cloid and taxclolvl.levelid=taxlvl.id and taxlvl.domainid=taxdom.id and plo.id=clo.parentid and courseid=?'
            ,array($course_id));
        
        if($rec){
            $cloid = 0; $levelid = 0;
            $flag=0;
            foreach ($rec as $records) {
                $flag++;
                $cloid = $records->cloid;
                $levelid = $records->lvlid;
                if($levelid>=7) // level belongs to psychomotor or affective domain
                    break;
            }

            $rec->close();

            if($flag){
                //echo "CLOID: $cloid LEVELID: $levelid";
                $flagR=0; $rubric_id=0;
                $recR=$DB->get_recordset_sql('SELECT rubric FROM mdl_clo_rubric WHERE cloid=?', array($cloid));
                foreach ($recR as $R) {
                    $flagR++;
                    $rubric_id = $R->rubric;
                }
            }
        }

        $assessmentid=$_GET['assessmentid'];     
        $criterionMaxMarks = $DB->get_records_sql('SELECT * From mdl_rubric_scale Where rubric =?',array($rubric_id));
        $checkcount = 0;
        //$maxmarks[$checkcount] = 0;
        foreach ($criterionMaxMarks as $marks){
            $checkcount++;
            $maxmarks[$checkcount] = $marks->score ; 
            $criterion[$checkcount] = $marks->criterion; 
            
        } 
       // var_dump($maxmarks);
       // echo "<br/>";
        //var_dump($criterion);
      //  echo count($criterion);
        

        $criterionCount = count(array_unique($criterion));
        $criterionUnique = array_values(array_unique($criterion));
        //echo $criterionCount;
        $criterionIndex = 1;
        $k = 0;
        $maxmarksNew = array();
        array_push($maxmarksNew, 0);
        //var_dump($criterionUnique); echo "<br>";
        //var_dump($criterion); echo "<br>";
        //var_dump($maxmarks); echo "<br>";

        for ($i=0 ; $i<$criterionCount; $i++)
        {
            $c = $criterionUnique[$i];
            //echo "C: $c<br>";
           // if (in_array($c, $k) )
               // continue;

            for ($j=0; $j<=count($criterion); $j++)
            {
                if($criterion[$j] == $c) {
                        //echo "M: $maxmarks[$j]";
                        //echo "K: $k<br>";
                    if($k < $maxmarks[$j]) {
                        $k = $maxmarks[$j];
                        
                        //$maxmarks[$criterionIndex]=$maxmarks[$j];
                        //$criterionIndex++;
                    }
                    
                }
            }
            //echo "K: $k";
            array_push($maxmarksNew, $k);
            $k = 0;
            //  echo " $k <br/>";
            // var_dump($k);
            
        }
        //echo "$k";
        
        //var_dump(array_values(array_unique($maxmarks)));
        $useridsupdate = array();
        //$maxmarks=(array_values(array_unique($maxmarks)));
        //echo "MM: <br>";
        //var_dump($maxmarksNew);
        $check=$DB->get_records_sql('SELECT *  FROM mdl_assessment_attempt WHERE aid = ?', array($assessmentid));
        $checkbit=0;
        $edit=0;
        if($check){
            $edit=1;
            //echo "<p style='color:red'>Sorry, cannot upload marks because they have already been uploaded!</p>";

            foreach($check as $c){
                $userid = $c->userid;
                array_push($useridsupdate, $userid);
            }
            $useridsupdate = array_unique($useridsupdate);
            $useridsupdate = array_values($useridsupdate);

          //var_dump($useridsupdate);

            //goto end;
        }

?>
        <form id="uploadMarks" method="POST" enctype="multipart/form-data" class="mform">

         <div class="btn btn-default btn-file">
            <input  type="file" name="assessmentmarks" id="assessmentmarks" placeholder="Only XLSX files are allowed!">
        </div>
        <input class="btn btn-info" type="submit" name="Upload" value="Upload" >
        
    </form>
<?php
        // check file name is not empty
        if (!empty($_FILES['assessmentmarks']['name'])) {
            
            // Get File extension eg. 'XLSX' to check file is XLSX sheet
            $pathinfo = pathinfo($_FILES["assessmentmarks"]["name"]);
            
            // check file has extension XLSX, XLSX and also check 
            // file is not empty
            if (($pathinfo['extension'] == 'XLSX' || $pathinfo['extension'] == 'xlsx') 
            && $_FILES['assessmentmarks']['size'] > 0 ) {
            
                // Temporary file name
                $inputFileName = $_FILES['assessmentmarks']['tmp_name']; 
            
                // Read XLSX file by using ReadFactory object.
                $reader = ReaderFactory::create(Type::XLSX);
        
                // Open file
                $tempfile=$reader->open($inputFileName);
                $count = 1;
                $abc=1;
                    
                //Number of sheet in XLSX file
                foreach ($reader->getSheetIterator() as $sheet) {
                    if($abc>=1){
                    
                    // Number of Rows in XLSX sheet
                    foreach ($sheet->getRowIterator() as $row) {
                        if($count==1){
                            $c1=count($row);
                            for($x=1;$x<$c1;$x++){
                            //$prefix = "pre";
                            //${$prefix.strtolower($x)}=$row[$x];
                            $criterionIds= utf8_encode($row[$x]);
                            //echo "$criterionIds <br/>";
                            $rec1=$DB->get_records_sql('SELECT id  FROM mdl_rubric_criterion WHERE description = ? AND rubric = ?', array($criterionIds, $rubric_id));
                            if($rec1){
                                $a="question";
                                foreach ($rec1 as $record1){
                                    ${$a.strtolower($x)}=$record1->id; 
                                    //echo "${$a.strtolower($x)} <br/>";
                                }
                            }
                            // echo ${$prefix.strtolower($x)};
                            // $a=$row[1];
                            // $b=$row[2];
                            // $c=$row[3];
                            // $d=$row[4];
                            }
                        }
    
                        // It reads data after header. In the my XLSX sheet, 
                        // header is in the first row. 
                        if ($count > 1) {
                        
                        //$arri = array_map('strval', $arr);
                        
                        // Data of XLSX sheet
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
                            //var_dump(${$pfix.strtolower($x)});
                        
                            //echo ${$pfix.strtolower($x)};
                            // $sn1=$row[1];
                            // $sn2=$row[2];
                            // $sn3=$row[3];
                            // $sn4=$row[4];
                             // echo "${$pfix.strtolower($x)}<br/>";
                           // if (${$pfix.strtolower($x)} == 0 && ${$pfix.strtolower($x)} !=/ "") 
                             //   goto below;
                            
                            if (${$pfix.strtolower($x)} === '' )
                                continue;
                          //  below:
                            // echo "${$pfix.strtolower($x)}<br/>";
                            if (!$edit){
                                if (${$pfix.strtolower($x)} <> "" && $uid <> "A" && ${$pfix.strtolower($x)} <= $maxmarksNew[$x] ){
                                    $sql1="INSERT INTO mdl_assessment_attempt (aid,userid,cid,obtmark) VALUES('$assessmentid','$uid','${$a.strtolower($x)}','${$pfix.strtolower($x)}')";

                                    $DB->execute($sql1);   
                                }
                                elseif (${$pfix.strtolower($x)} == "" && $uid <> "A" ){
                                    $sql1="INSERT INTO mdl_assessment_attempt (aid,userid,cid,obtmark) VALUES('$assessmentid','$uid','${$a.strtolower($x)}',0)";
                                    $DB->execute($sql1);
                                }
                                elseif (${$pfix.strtolower($x)} > $maxmarksNew[$x] && $uid <> "A"){
                                    $sql1="INSERT INTO mdl_assessment_attempt (aid,userid,cid,obtmark) VALUES('$assessmentid','$uid','${$a.strtolower($x)}',0)";
                                    $DB->execute($sql1);
                                    $checkbit=1;
                              }
                            }
                            else
                            {
                                //echo "UID: $uid<br>";
                               // echo "${$pfix.strtolower($x)}<br/>";
                                if(in_array($uid, $useridsupdate) && ${$pfix.strtolower($x)} <= $maxmarksNew[$x]){
                                    //echo "CM: ${$pfix.strtolower($x)}<br/>";
                                    $sql1="UPDATE mdl_assessment_attempt SET obtmark=? WHERE aid=? AND userid=? AND cid=?";
                                    $DB->execute($sql1, array(${$pfix.strtolower($x)}, $assessmentid, $uid, ${$a.strtolower($x)}));
                                }
                                elseif (in_array($uid, $useridsupdate) && ${$pfix.strtolower($x)} > $maxmarksNew[$x]) {
                                     //echo "CM: ${$pfix.strtolower($x)}<br/>";
                                     $sql1="UPDATE mdl_assessment_attempt SET obtmark=? WHERE aid=? AND userid=? AND cid=?";
                                 $DB->execute($sql1, array(0, $assessmentid, $uid, ${$a.strtolower($x)}));
                                 $checkbit=1;
                                }
                                else{
                                     $record = new stdClass();
                                     $record->aid = $assessmentid;
                                    $record->userid = $uid;
                                     $record->cid = ${$a.strtolower($x)};
                                     $record->obtmark = ${$pfix.strtolower($x)};
                                     $DB->insert_record('assessment_attempt', $record);
                                     }
                            }
                        }
                    }
                    $count++;
                }
                
            }
                $abc++;
            }
            if($sql1){
                echo "<h3 style='color:green;'>Result has been uploaded!</h3>";
            }
            if($checkbit){
                echo "<h3 style='color:red;'>Some students with obtained marks greater than maximum marks are assigned 0.</h3>";
            }
            //Close XLSX file
            $reader->close();
        } else {
            echo "<p style='color:red;'>Please Select Valid XLSX File</p>";
        }
    } else {
        echo "<p style='color:red;'>Please Select XLSX File</p>";
    }
    end:
    ?>
    <a class="btn btn-default" href="./report_teacher?course=<?php echo $course_id ?>">Go Back</a>
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