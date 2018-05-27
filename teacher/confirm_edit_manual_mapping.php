<?php
	require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Edit Mapping");
    $PAGE->set_heading("Edit Mapping");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/confirm_edit_manual_mapping.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
   
    if(!empty($_POST['submit']))
    {
    
        $parentActivity = $_POST['pactivity'];
        $activityid = $_POST['actid'];
        $courseId = $_POST['courseid'];
        $module = $_POST['mod'];

      //  echo "Parent $parentActivity<br/>";
       // echo "activityid : $activityid <Br> courseId: $courseId<br/>";

       // echo "module: $module<br/>";
         
        $instance = substr($activityid, 1);

        //echo "instance: $instance";

        $sql_update="UPDATE mdl_parent_mapping SET parentid =? WHERE childid=? AND module=?";
        $DB->execute($sql_update, array($parentActivity, $instance, $module));
           

         echo "<font color='green'><b>Mapping successfully updated!</b></font><br />";

         ?>
         <a href="./map_manual_activity?course=<?php echo $courseId; ?>"> Back </a>
         <?php

       
echo $OUTPUT->footer();
    }