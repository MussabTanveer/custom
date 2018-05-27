<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Map Manual Activity");
    $PAGE->set_heading("Map Manual Activity");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/map_manual_activity.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

?>
<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/validation/jquery.validate.js"></script>
<style>
label.error {
    color: red;
}
</style>

<?php    
    if(!empty($_GET['course']))
    {
        $course_id=$_GET['course'];
        $coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
        //echo "Course ID : $course_id";

        $ParentActivites = $DB->get_records_sql("SELECT * FROM mdl_parent_activity WHERE courseid =?",array($course_id));
    
        
       $recQ=$DB->get_records_sql('SELECT * FROM  `mdl_manual_quiz` WHERE courseid = ?', array($course_id));
       $recA=$DB->get_records_sql('SELECT * FROM `mdl_manual_assign_pro` WHERE courseid = ?', array($course_id));
       $recO=$DB->get_records_sql('SELECT * FROM `mdl_manual_other` WHERE courseid = ?', array($course_id));
            
            if($recQ || $recA || $recO){
            $i = 0;
            $activityids = array();
            $modules = array();
            ?>
           
            <a href="./define_parent_activity.php?course=<?php echo $course_id ?>&flag=1" style="float:right; margin-bottom: 25px" class="btn btn-primary">Define Parent Activity</a>
            
            
            <form action="confirm_manual_mapping.php" method="post" id="mapForm">
                <table class="generaltable">
                    <tr class="table-head">
                        <th> Activities </th>
                        
                        <th> Select Parent Activity </th>
                    </tr>
                    <?php
                    $i = 0;
                    foreach($recQ as $records)
                    {
                        $qid = $records->id;
                        $childid = $qid;
                        $qname = $records->name;
                        $module = $records->module;
                        

                //Flag to check mapped activites
                $flagQ = $DB->get_records_sql("SELECT * FROM mdl_parent_mapping WHERE childid =? AND module = ?",array($qid,$module));
                   
                   if (!$flagQ)
                   {

                        array_push($activityids,"Q".$qid);
                        array_push($modules, $module);
                    ?>

                                
                    <tr>
                        <td><?php echo $qname;?> </td>
                        


                      
                        <td>
                            <select class="select custom-select" name="pactivity[]" id="pact<?php echo $i ?>">
                                <option value=''>Choose..</option>
                                <?php

                                $SelectedParentActivity = $DB->get_records_sql("SELECT * FROM mdl_parent_mapping WHERE childid =?",array($childid));

                                    foreach ($SelectedParentActivity as $spa)
                                    {
                                        $parentidq = $spa->parentid;
                                       // break;

                                    }

                                  /*  $SelectedParentActivityName = $DB->get_records_sql("SELECT * FROM mdl_parent_activity WHERE id =?",array($parentid));


                                     foreach ($SelectedParentActivityName as $span)
                                    {
                                        $pname = $span->name;
                                        ?>
                                        <option value="hello"> <?php echo $pname ?></option>
                                        <?php
                                        

                                    }*/

                                     foreach ($ParentActivites as $parentActivity) {
                                       
                                        $id = $parentActivity->id;
                                        $name = $parentActivity->name;
                                        echo "id = $id pid= $parentid <br>";
                                        
                                        if($id == $parentidq )
                                        {

                                       ?>
                                       
                                       <option required selected value="<?php echo $id; ?>">
                                                <?php echo $name; ?>
                                           
                                       </option>
                                
                                       <?php
                                        }
                                        else
                                        {
                                            ?>
                                                 <option  value="<?php echo $id; ?>">
                                                <?php echo $name; ?>
                                           
                                       </option>
                                            <?php
                                        }
                                 }

                                ?>

                            </select>

                        </td>


                    </tr>
                    <?php
                        $i++;
                        }
                    }
                        foreach($recA as $records)
                        {
                            $aid = $records->id;
                            $childid = $aid;
                            $aname = $records->name;
                            $module = $records->module;
                            

                        $flagA = $DB->get_records_sql("SELECT * FROM mdl_parent_mapping WHERE childid =? AND module = ?",array($aid,$module));

                        if (!$flagA)
                        {
                            array_push($activityids,"A".$aid);
                            array_push($modules, $module);
                        ?>
                                    
                        <tr>
                            <td><?php echo $aname;?> </td>
                            

                              <td>
                            <select required class="select custom-select" name="pactivity[]" id="pact<?php echo $i ?>">
                                <option value=''>Choose..</option>
                                <?php

                                    $SelectedParentActivity = $DB->get_records_sql("SELECT * FROM mdl_parent_mapping WHERE childid =?",array($childid));

                                    foreach ($SelectedParentActivity as $spa)
                                    {
                                        $parentida = $spa->parentid;
                                       // break;

                                    }



                                     foreach ($ParentActivites as $parentActivity)
                                    {
                                       
                                        $id = $parentActivity->id;
                                        $name = $parentActivity->name;

                                        if($id == $parentida)
                                        {
                                       ?>
                                       <option selected value="<?php echo $id; ?>">
                                                <?php echo $name; ?>
                                           
                                       </option>
                                       <?php
                                         }
                                         else
                                           {
                                            ?>
                                            <option value="<?php echo $id; ?>">
                                                        <?php echo $name; ?>
                                                   
                                               </option>
                                            <?php
                                           }
                                    }

                                ?>

                             </select>

                        </td>


                            </tr>
                            <?php
                                $i++;
                            }
                        }



                        foreach($recO as $records)
                        {
                            $oid = $records->id;
                            $childid = $oid;
                            $oname = $records->name;
                            $module = $records->module;
                            

                        $flagO = $DB->get_records_sql("SELECT * FROM mdl_parent_mapping WHERE childid =? AND module = ?",array($oid,$module));

                        if (!$flagO)
                        {
                            array_push($activityids,"O".$oid);
                            array_push($modules, $module);
                        ?>
                                    
                        <tr>
                            <td><?php echo $oname;?> </td>
                            

                              <td>
                            <select required class="select custom-select" name="pactivity[]" id="pact<?php echo $i ?>">
                                <option value=''>Choose..</option>
                                <?php

                                    $SelectedParentActivity = $DB->get_records_sql("SELECT * FROM mdl_parent_mapping WHERE childid =?",array($childid));

                                    foreach ($SelectedParentActivity as $spa)
                                    {
                                        $parentida = $spa->parentid;
                                       // break;

                                    }



                                     foreach ($ParentActivites as $parentActivity)
                                    {
                                       
                                        $id = $parentActivity->id;
                                        $name = $parentActivity->name;

                                        if($id == $parentida)
                                        {
                                       ?>
                                       <option selected value="<?php echo $id; ?>">
                                                <?php echo $name; ?>
                                           
                                       </option>
                                       <?php
                                         }
                                         else
                                           {
                                            ?>
                                            <option value="<?php echo $id; ?>">
                                                        <?php echo $name; ?>
                                                   
                                               </option>
                                            <?php
                                           }
                                    }

                                ?>

                             </select>

                        </td>


                            </tr>
                            <?php
                                $i++;
                            }
                        }

                        global $SESSION;
                        $SESSION->activityids = $activityids;
                       // var_dump($modules);
                        $SESSION->modules=$modules;
                        
                        ?>
                </table>
            
                <input type="hidden" value='<?php echo $i; ?>' name="activitycount">
                <input type="hidden" value='<?php echo $course_id; ?>' name="courseid">
                <input type="submit" value="NEXT" name="submit" class="btn btn-primary">
            </form>

            <script>
                //form validation
                $(document).ready(function () {
                    $('#mapForm').validate({ // initialize the plugin
                        rules: {
                            
                            "pactivity[]":{
                                required: true
                            }
                        }
                    });
                });
            </script>

            <?php
            }                  

            ?>
             <h3 style="margin-top: 30px">Already Mapped Activities</h3>
<?php
       // $mactivitiesids = array();
        
        if($recQ || $recA)

        {
            ?>
            <table class="generaltable" style="margin-top: 25px">
                <tr class="table-head">
                    <th> Activities </th>
                    <th>Edit</th>
                </tr>
            <?php

                foreach($recQ as $records)
                        {
                            $qid = $records->id;
                           // echo "$qid<br/>";
                            $childid = $qid;
                            $qname = $records->name;
                            $module = $records->module;
                           // array_push($mactivitiesids,"Q".$qid);
                            ?>
                            <tr>
                            <?php
                            //Flag to check mapped activites
                            $flagQ = $DB->get_records_sql("SELECT * FROM mdl_parent_mapping WHERE childid =? AND module = ?",array($qid,$module));
                            
                            if ($flagQ)
                            { // echo "$qid";
                                $qid = "Q".$qid;
                              ?>
                                
                                 <td>  <?php echo "$qname<br/>"; ?> </td>
                                <td> 
                                    <a href="./edit_manual_mapping.php?id=<?php echo $qid; ?>&course=<?php echo $course_id; ?>&mod=<?php echo $module; ?>" title='Edit'> <i class='icon fa fa-pencil text-info' aria-hidden='true' title='Edit' aria-label='Edit'></i></a>
                                </td>
                          
                        <?php
                          } 
                          ?>
                      </tr>
                <?php
                        }

                        foreach($recA as $records)
                        {
                            $aid = $records->id;
                            $childid = $aid;
                            $aname = $records->name;
                            $module = $records->module;
                            //array_push($mactivitiesids,"A".$aid);
                                  ?>
                            <tr>
                            <?php
                            $flagA = $DB->get_records_sql("SELECT * FROM mdl_parent_mapping WHERE childid =? AND module = ?",array($aid,$module));
                            if ($flagA)
                             {
                                //var_dump($flagA);
                                $aid = "A".$aid;
                              ?>
                                
                                 <td>  <?php echo "$aname<br/>"; ?> </td>
                                <td> 
                                    <a href="./edit_manual_mapping.php?id=<?php echo $aid; ?>&course=<?php echo $course_id; ?>&mod=<?php echo $module; ?>" title='Edit'> <i class='icon fa fa-pencil text-info' aria-hidden='true' title='Edit' aria-label='Edit'></i></a>
                                </td>
                          
                        <?php
                          } 
                          ?>
                      </tr>
                <?php


                        }


                        foreach($recO as $records)
                        {
                            $oid = $records->id;
                            $childid = $oid;
                            $aname = $records->name;
                            $module = $records->module;
                            //array_push($mactivitiesids,"O".$oid);
                                  ?>
                            <tr>
                            <?php
                            $flagO = $DB->get_records_sql("SELECT * FROM mdl_parent_mapping WHERE childid =? AND module = ?",array($oid,$module));
                            if ($flagO)
                             {
                                //var_dump($flagA);
                                $oid = "O".$oid;
                              ?>
                                
                                 <td>  <?php echo "$aname<br/>"; ?> </td>
                                <td> 
                                    <a href="./edit_manual_mapping.php?id=<?php echo $oid; ?>&course=<?php echo $course_id; ?>&mod=<?php echo $module; ?>" title='Edit'> <i class='icon fa fa-pencil text-info' aria-hidden='true' title='Edit' aria-label='Edit'></i></a>
                                </td>
                          
                        <?php
                          } 
                          ?>
                      </tr>
                <?php


                        }

                        ?>

            </table>

<?php
        }
       

    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./teacher_courses.php">Back</a>
    <?php
    }
    echo $OUTPUT->footer();
?>
