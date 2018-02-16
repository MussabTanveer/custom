<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Non-Editing Teacher");
    $PAGE->set_heading("Assessments");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/report_teacher_practical.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
 
    $course_id=$_GET['course'];

    //echo $course_id; 


    $rec=$DB->get_records_sql('SELECT assessment FROM mdl_practical_assessment WHERE courseid = ?',array($course_id));

    ?>
 <link rel="stylesheet" type="text/css" href="../css/cool-link/style.css" />

    <div>
        <h3>Click the links down below as per need </h3><br>
        <a <?php echo "href='./add_assessment.php?course=$course_id'" ?> class="cool-link">Add New Assessment</a><br><br> </div>
    
<?php
    
 if(empty($rec)){


    echo "<h4>You have yet to add an Assessment!</h4>";
 }
elseif(!empty($rec)){
         $serialno = 0;
        $table = new html_table();
        $table->head = array('S. No.','Assessment');
       foreach ($rec as $record) {

            $serialno++;
            $assessment = $record->assessment;
        
       }

       $table->data[] = array($serialno, "<a href='./assessment_display.php?course=$course_id'>$assessment </a>");

echo html_writer::table($table);
}    
 
?>




<?php

echo $OUTPUT->footer();
?>