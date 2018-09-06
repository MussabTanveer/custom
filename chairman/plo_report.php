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
    //    echo "$Ploid";
        $batchID = $_POST['batchID'];
   //     echo "<br/>$batchID";
        $frameworkId = $_POST['fwid'];
    //    echo "<br/> $frameworkId";

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
                                 //   echo "displaying batch ones<br/>";
                                 //   echo "$shortname $idnumber $semesterId $courseid<br/>";
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
     /*   echo "<br/>";
        echo "<br/>";
        echo "Before Unique";
        echo "<br/>";
        var_dump($shortnames);
        echo "<br/>";
        var_dump($idnumbers);
        echo "<br/> semesterids ";
        var_dump($semesterids);
        echo "<br/> Course Ids ";
        var_dump($courseids);
         echo "<br/>";
          echo "<br/>";  */
        $shortnames = array_values(array_unique($shortnames));
        $idnumbers = array_values(array_unique($idnumbers));
        $semesterids = array_values(array_unique($semesterids));
        $courseids = array_values(array_unique($courseids));

        $courseid = $courseids[0];
        $semesterid = $semesterids[0];

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
/*
        var_dump($stdids);
        echo "<br/>";
        var_dump($seatnos);


       
        var_dump($shortnames);
        echo "<br/>";
        var_dump($idnumbers);
        echo "<br/>";
        var_dump($semesterids);
        echo "<br/>";
        var_dump($courseids);  */
       // $idnumbers[1]="CS-111";
       // $idnumbers[2]="CS-121";
        $flag=0;
        $j=1;
        $plosclosid = array();
        $statusArray = array();
        $statusArrayIndex=0;
        $chunksize = 0;

        foreach ($courseids as $c)
         {

             $sql=$DB->get_records_sql('SELECT Distinct cloid FROM  `mdl_clo_wise_result` WHERE courseid = ?', array($c));
            if($sql){
                foreach($sql as $s){
                   // $userid = $s->userid;
                    $cloid = $s->cloid;
                   // $courseid = $s->courseid;
                   // $status  = $s->status;


                    $sql1=$DB->get_records_sql('SELECT * FROM  `mdl_competency` WHERE id = ?', array($cloid));
                    if($sql1)
                    {
                        foreach ($sql1 as $s1)
                        {
                            $parentid = $s1->parentid;
                            if ($parentid == $Ploid)
                                $flag=1;
                        }
                    }
                    if($flag)
                    {
                        /*if ($j =1)
                        {
                            $tempStatus = $status;
                            $tempUserId = $userid;
                            $tempCourseId = $courseid;
                            $statusArray[$statusArrayIndex] = $tempStatus;
                            $statusArrayIndex++
                            $j=2;
                            $chunksize++;
                        }

                    if ($tempUserId == $userid && $tempCourseId == $courseid && $j!=1)
                        {
                            $chunksize++;
                            $tempStatus = $status;
                          
                            $statusArray[$statusArrayIndex] = $tempStatus;
                             $statusArrayIndex++

                        }
                        if( $tempUserId != $userid && $tempCourseId == $courseid && $j!=1)

                        {
                            $chunksize=0;
                             $tempUserId = $userid;   
                             $tempStatus = $status;
                          
                            $statusArray[$statusArrayIndex] = $tempStatus;
                            $statusArrayIndex++

                        }*/
                      //  echo "Printing PLO's Clos";
                    //    echo "<br/>  $cloid  <br/>";
                        array_push($plosclosid, $cloid);
                    }
                    $flag =0;


                }    
        }
    }

   // var_dump($plosclosid);
    $total =0;

        ?>
        <table class="generaltable" border="1">
            <?php
                ?>
            <tr>
                    <th>Serial No.</th>

                     <th>Seat No.</th>
                <?php
                foreach($idnumbers as $idn)
                { $total++;?> 
                    
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
                    $PloStatus=0;

            ?>  <tr>
                    <td><?php echo $i; ?></td>
                     <td><?php echo $sn; ?></td>
                    
                     <?php 
                     foreach($idnumbers as $idn)
                     {?> 
                         <td><?php 
                           // echo "$sn $idn"; 

                            $courses=$DB->get_records_sql('SELECT * FROM mdl_course WHERE idnumber=? AND semesterid =? ',array($idn,$semesterid)); 

                            if($courses){

                                 foreach($courses as $course)
                                 {

                                        $cid = $course->id;
                                       // echo "<br/>$cid";

                                 }
                             }

                             $userids=$DB->get_records_sql('SELECT * FROM mdl_user WHERE username=? ',array($sn)); 


                            if($userids){

                                 foreach($userids as $uid)
                                 {

                                        $usrid = $uid->id;
                                       // echo "<br/>$usrid";

                                 }
                             }


                                $getStatus=$DB->get_records_sql('SELECT * FROM mdl_clo_wise_result WHERE userid =?  AND courseid=? ',array($usrid,$cid)); 

                            $statusValue =1 ;
                            $temp = array();
                            
                            if($getStatus){

                                 foreach($getStatus as $gs)
                                 {

                                        $status = $gs->status;
                                        $scloid = $gs->cloid;
                                        if (in_array($scloid, $plosclosid))
                                         {  
                                           // echo "<br/>$status";
                                            array_push($temp, $status);

                                            
                                          }

                                 }
                                // var_dump($temp);

                                 if(in_array(0, $temp))
                                   echo "<i class='fa fa-square' aria-hidden='true' style='color: #FE3939'>  </i><br>";
                                else
                                  {
                                    echo "<i class='fa fa-square' aria-hidden='true' style='color: #05E177'>  </i><br>";
                                    $PloStatus++;
                                 }
                             }


                         ?></td>
                     

                     <?php 
                     }
                 ?>
                        <td> <?php //echo $PloStatus; echo $total;

                            if(($PloStatus/$total) * 100 >= 50)
                            {
                                echo "<i class='fa fa-square' aria-hidden='true' style='color: #05E177'>  </i><br>";
                            }
                            else
                                echo "<i class='fa fa-square' aria-hidden='true' style='color: #FE3939'>  </i><br>";
                         ?> 
                        </td>
                </tr>
            <?php
                $i++;
                }
            ?>    
        
        </table>

        <form action="plo_selection.php" method="post">

            <input type="hidden" name="batchID" value="<?php echo $batchID; ?>">
             <input type="hidden" name="frameworkid" value="<?php echo $frameworkId; ?>">
             <input style="margin-top: 20px" class="btn btn-default"type="submit" name="submit" value="Go Back">

        </form>

        <?php

        



        echo $OUTPUT->footer();
    }
    else
    {   
        echo "<font color=red> Something went wrong :( </font>";
        echo $OUTPUT->footer();
    }
