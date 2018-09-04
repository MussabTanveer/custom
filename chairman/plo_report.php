<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("PLO Report");
    $PAGE->set_heading("PLO Report");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/plo_report.php');
    
    echo $OUTPUT->header();
    require_login();
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
    
    if(isset($_POST['plosid'])  )
    {
        $Ploid = $_POST['plosid'];
        echo "$Ploid";
        $batchID = $_POST['batchID'];
        echo "<br/>$batchID";

        // Report Header (Uni. name, Dept. name, course code and title)
        $un=$DB->get_records_sql('SELECT * FROM  `mdl_vision_mission` WHERE idnumber = ?', array("un"));
        if($un){
            foreach($un as $u){
                $uniName = $u->description;
            }
            $uniName = strip_tags($uniName); 
            echo "<h3 style='text-align:center'>".strtoupper($uniName)."</h3>";         
        }
        $dn=$DB->get_records_sql('SELECT * FROM  `mdl_vision_mission` WHERE idnumber = ?', array("dn"));
        if($dn){
            foreach($dn as $d){
                $deptName = $d->description;
            }
            $deptName = strip_tags($deptName); 
            echo "<h3 style='text-align:center'>".strtoupper($deptName)."</h3>";         
        }
         $batch=$DB->get_records_sql('SELECT * FROM  `mdl_batch` WHERE id = ?', array("$batchID"));
        if($batch)
        {
            foreach($batch as $b){
                $batchName = $b->name;
            }
            ?> <h4 style=text-align:center> Batch <?php echo $batchName; ?></h4>         
        <?php
        }

         $Plo=$DB->get_records_sql('SELECT * FROM  `mdl_competency` WHERE id = ?', array("$Ploid"));
        if($Plo){
            foreach($Plo as $p){
                $PloName = $p->idnumber;
            }
            ?> <h4 style=text-align:center> OBE <b><?php echo $PloName; ?></b> Assessment Sheet (Based on CLO Assessment)</h4>         
        <?php
        }
        
        $shortnames = array();
        $idnumbers = array();
        $semesterids = array();
        $courseids = array();

        $clos=$DB->get_records_sql('SELECT * FROM mdl_competency WHERE parentid=?',array("$Ploid")); //all clos of the plo

        if($clos){

        	foreach($clos as $c){


        		$Cloname=$c->idnumber;
        		//echo $Cloname;


        		$course=substr($Cloname,0,6);
        		//echo $course;

                 $courses=$DB->get_records_sql('SELECT * FROM mdl_course WHERE idnumber=?',array($course)); 

                if($courses){

                    foreach($courses as $course){

                        $shortname = $course->shortname;
                        $idnumber = $course->idnumber;
                        $semesterId = $course->semesterid;
                        $courseid = $course->id;
                        

                      //  echo "$shortname $idnumber $semesterId<br/>";

                        $batchCourses=$DB->get_records_sql('SELECT * FROM mdl_semester WHERE id=?',array($semesterId)); 

                        if($batchCourses){
                            foreach($batchCourses as $batchCourse)
                            {
                                $batchid = $batchCourse->batchid;
                                if ($batchid == $batchID)
                                {
                                    echo "displaying batch ones<br/>";
                                    echo "$shortname $idnumber $semesterId $courseid<br/>";
                                    array_push($shortnames, $shortname);
                                    array_push($idnumbers, $idnumber);
                                    array_push($semesterids, $semesterId);
                                    array_push($courseids, $courseid);

                                } 
                            }
                        }

                    }
                 }


        	}
        }
        echo "<br/>";
        echo "<br/>";
        echo "Before Unique";
        echo "<br/>";
        var_dump($shortnames);
        echo "<br/>";
        var_dump($idnumbers);
        echo "<br/>";
        var_dump($semesterids);
        echo "<br/> Course Ids ";
        var_dump($courseids);
         echo "<br/>";
          echo "<br/>";
        $shortnames = array_values(array_unique($shortnames));
        $idnumbers = array_values(array_unique($idnumbers));
        $semesterids = array_values(array_unique($semesterids));
        $courseids = array_values(array_unique($courseids));

        $courseid = $courseids[0];

       // echo "$courseid";



        // Get all students of course
        $recStudents=$DB->get_records_sql("SELECT u.id AS sid, u.username AS seatnum, substring(u.username,4,8) AS seatorder, u.firstname, u.lastname
        FROM mdl_role_assignments ra, mdl_user u, mdl_course c, mdl_context cxt
        WHERE ra.userid = u.id
        AND ra.contextid = cxt.id
        AND cxt.contextlevel = ?
        AND cxt.instanceid = c.id
        AND c.id = ?
        AND (roleid=5) ORDER BY seatorder", array(50, $courseid));

        $stdids = array();
        $seatnos = array();
        foreach($recStudents as $records){
            $id = $records->sid;
            $seatno = $records->seatnum ;
            array_push($stdids,$id);
            array_push($seatnos,$seatno);
        }

        var_dump($stdids);
        echo "<br/>";
        var_dump($seatnos);


       
        var_dump($shortnames);
        echo "<br/>";
        var_dump($idnumbers);
        echo "<br/>";
        var_dump($semesterids);
        echo "<br/>";
        var_dump($courseids);
       // $idnumbers[1]="CS-111";
       // $idnumbers[2]="CS-121";
        ?>
        <table border="2px">
            <?php
                ?>
            <tr>
                    <th>Serial No.</th>

                     <th>Seat No.</th>
                <?php
                foreach($idnumbers as $idn)
                {?> 
                    
                    <th><?php echo $idn;?></th>
                    <?php

                }
            ?>
            <th>PLO Status (pass/fail)</th>
        </tr>
        
            <?php
            $i=1;
                foreach($seatnos as $sn)
                {
            ?>  <tr>
                    <td><?php echo $i; ?></td>
                     <td><?php echo $sn; ?></td>
                    
                     <?php 
                     foreach($idnumbers as $idn)
                     {?> 
                         <td></td>
                     

                     <?php 
                     }
                 ?>
                        <td>   </td>
                </tr>
            <?php
                $i++;
                }
            ?>    
        
        </table>

        <?php

       



        echo $OUTPUT->footer();
    }
    else
    {   
        echo "<font color=red> Something went wrong :( </font>";
        echo $OUTPUT->footer();
    }
