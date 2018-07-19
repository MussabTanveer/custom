<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Activities");
    $PAGE->set_heading("Select Activity");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/consolidated_report_selection.php');
    
    echo $OUTPUT->header();
    require_login();
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <?php
    if(isset( $_GET['course']))
    {	
        $course_id=$_GET['course'];
        
         require '../templates/consolidated_report_selection_template.php';
       
    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./display_courses-3.php">Back</a>
    <?php
        echo $OUTPUT->footer();
    }?>
