
<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/validation/jquery.validate.js"></script>
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

        // Get Grading Items
        $rec=$DB->get_records_sql("SELECT * FROM mdl_grading_policy WHERE courseid = ? ORDER BY id", array($course_id));

        $ParentActivites = $DB->get_records_sql("SELECT * FROM mdl_parent_activity WHERE courseid =?",array($course_id));
       

        
            $recQ=$DB->get_records_sql('SELECT * FROM  `mdl_manual_quiz` WHERE courseid = ?', array($course_id));
            $recA=$DB->get_records_sql('SELECT * FROM `mdl_manual_assign_pro` WHERE courseid = ?', array($course_id));
            
            if($recQ || $recA){
            $i = 0;
            $activityids = array();
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
                        array_push($activityids,"Q".$qid);
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
                        foreach($recA as $records)
                        {
                            $aid = $records->id;
                            $childid = $aid;
                            $aname = $records->name;
                            array_push($activityids,"A".$aid);
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
                        global $SESSION;
                        $SESSION->activityids = $activityids;
                        
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
       

    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./teacher_courses.php">Back</a>
    <?php
    }
    echo $OUTPUT->footer();
?>
