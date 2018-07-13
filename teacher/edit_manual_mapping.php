<?php
	require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Edit Mapping");
    $PAGE->set_heading("Edit Mapping");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/edit_manual_mapping.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <script src="../script/validation/jquery.validate.js"></script>
    <?php
   
    if(!empty($_GET['course']))
    {
        $course_id=$_GET['course'];
        $Id = $_GET['id'];
        $module = $_GET['mod'];
        $coursecontext = context_course::instance($course_id);
		is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
       // echo "Course ID : $course_id Activity id : $ActivityId";

        //echo "$Id<br>";
        $ActivityId = substr($Id, 1);
        //echo "Activity ID :$ActivityId <br>";
       // echo "$Id<br>";
        $flag = substr($Id, 0,1);
       // echo "Flag: $flag<br>";


        $ParentActivites = $DB->get_records_sql("SELECT * FROM mdl_parent_activity WHERE courseid =?",array($course_id));
       
?>
    <form action="confirm_edit_manual_mapping.php" method="post" id="">
         <table class="generaltable" >
            <tr class="table-head">
                <th>Activity</th>
                
                <th>Parent Activity</th>
            </tr>

       
<?php
             
              if ($flag == "A")
              {
                //echo "Assignment";

                 $getParent = $DB->get_records_sql("SELECT * FROM mdl_parent_mapping WHERE childid =? AND module = ?",array($ActivityId,$module));


                 $data = $DB->get_records_sql("SELECT * FROM mdl_manual_assign_pro WHERE id =?",array($ActivityId));



                 if($data)
                 { 
                    ?>
                    <tr>
                    <?php

                    foreach($data as $q)
                    {

                        $name = $q->name;
                        $id = $q->id;
                        $childid = "A".$id;
                        ?>
                        <td>
                            <?php echo "$name"; ?>
                        </td>

                         <?php

                            ?>
                                <td>
                                    <select class="select custom-select" name="pactivity" id="pactivity">
                                        <option value=''>Choose..</option>

                                        <?php

                                        foreach ($getParent as $gp)
                                            {
                                                $pid = $gp->parentid;
                                           }

                                        foreach ($ParentActivites as $pa) {
                                            # code...
                                            $id = $pa->id;
                                            $name = $pa->name;

                                            

                                            if ($pid == $id) {
                                        ?>
                                       
                                        <option selected value="<?php echo $id; ?>">
                                                        <?php echo $name; ?>
                                                   
                                         </option>

                                          <?php
                                                }
                                                else
                                                    {?>

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

                    }

                 }


              }
              elseif ($flag == "Q") 
              {
                  # code...
                //echo "Quiz";

                 $getParent = $DB->get_records_sql("SELECT * FROM mdl_parent_mapping WHERE childid =? AND module = ?",array($ActivityId,$module));

                 $data = $DB->get_records_sql("SELECT * FROM mdl_manual_quiz WHERE id =?",array($ActivityId));



                 if($data)
                 {
                    ?>
                    <tr>
                    <?php

                    foreach($data as $q)
                    {
                        $name = $q->name;
                        $id = $q->id;
                        $childid = "Q".$id;
                        ?>
                        <td>
                            <?php echo "$name"; ?>
                        </td>

                        
                         <?php

                            ?>
                                <td>
                                    <select class="select custom-select" name="pactivity" id="pactivity">
                                        <option value=''>Choose..</option>

                                        <?php

                                        foreach ($getParent as $gp)
                                            {
                                                $pid = $gp->parentid;
                                           }

                                        foreach ($ParentActivites as $pa) {
                                            # code...
                                            $id = $pa->id;
                                            $name = $pa->name;

                                            

                                            if ($pid == $id) {
                                        ?>
                                       
                                        <option selected value="<?php echo $id; ?>">
                                                        <?php echo $name; ?>
                                                   
                                         </option>

                                          <?php
                                                }
                                                else
                                                    {?>

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

                    }

                 }



             }
             elseif ($flag == "O") {
              
                $getParent = $DB->get_records_sql("SELECT * FROM mdl_parent_mapping WHERE childid =? AND module = ?",array($ActivityId,$module));

                 $data = $DB->get_records_sql("SELECT * FROM mdl_manual_other WHERE id =?",array($ActivityId));



                 if($data)
                 {
                    ?>
                    <tr>
                    <?php

                    foreach($data as $q)
                    {
                        $name = $q->name;
                        $id = $q->id;
                        $childid = "O".$id;
                        ?>
                        <td>
                            <?php echo "$name"; ?>
                        </td>

                        
                         <?php

                            ?>
                                <td>
                                    <select class="select custom-select" name="pactivity" id="pactivity">
                                        <option value=''>Choose..</option>

                                        <?php

                                        foreach ($getParent as $gp)
                                            {
                                                $pid = $gp->parentid;
                                           }

                                        foreach ($ParentActivites as $pa) {
                                            # code...
                                            $id = $pa->id;
                                            $name = $pa->name;

                                            

                                            if ($pid == $id) {
                                        ?>
                                       
                                        <option selected value="<?php echo $id; ?>">
                                                        <?php echo $name; ?>
                                                   
                                         </option>

                                          <?php
                                                }
                                                else
                                                    {?>

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

                    }

                 }


             }

                 
              
              ?>
        </table>


        <input type="hidden" name="actid" value="<?php echo $childid ;?>" >
        <input type="hidden" name="courseid" value="<?php echo $course_id;?>" >
        <input type="hidden" name="mod" value="<?php echo $module; ?>">
        <input type="submit" value="NEXT" name="submit" class="btn btn-primary">
           
       </form>


       <?php


echo $OUTPUT->footer();
    }
