<?php
	require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Edit Mapping");
    $PAGE->set_heading("Edit Mapping");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/confirm_edit_mapping.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
   
    if(!empty($_POST['submit']))
    {
    

        $gradingItem = $_POST['gitem'];
        $parentActivity = $_POST['pactivity'];
        $activityid = $_POST['actid'];
        $courseId = $_POST['courseid'];

        //echo "gradingItem : $gradingItem <br> parentActivity : $parentActivity <br>";
        //echo "activityid : $activityid <Br> courseId: $courseId";

        if(substr($activityid,0,1)=="Q")
        {
            $mod =16;

            //echo "in Quiz";
        }
        elseif (substr($activityid,0,1)=="A")
         {
            $mod = 1;
           // echo "In assign";
        }
        
        $instance = substr($activityid, 1);
    
        $sql_update="UPDATE mdl_grading_mapping SET gradingitem =? WHERE instance=? AND module=?";
        $DB->execute($sql_update, array($gradingItem, $instance, $mod));


        $sql_update="UPDATE mdl_parent_mapping SET parentid =? WHERE childid=? AND module=?";
        $DB->execute($sql_update, array($parentActivity, $instance, $mod));
           

         echo "<font color='green'><b>Mapping successfully updated!</b></font><br />";

         ?>
         <a href="./map_grading_item?course=<?php echo $courseId; ?>"> Back </a>
         <?php

       
echo $OUTPUT->footer();
    }