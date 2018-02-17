<script src="../script/jquery/jquery-3.2.1.js"></script>
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
        <h3>Add New Assessment</h3><br>
        <a <?php echo "href='./add_assessment.php?course=$course_id'" ?> class="cool-link">Add New Assessment</a><br><br> </div>
    
<?php
    
 if(empty($rec)){


    echo "<h4>You have yet to add an Assessment!</h4>";
 }
elseif(!empty($rec)){

    echo "<h4>View Already Made Assessments:</h4>";
        // $serialno = 0;
        //$table = new html_table();
        //$table->head = array('S. No.','Assessment');
       foreach ($rec as $record) {

           // $serialno++;
            
            $assessment = $record->assessment;
            //echo "hello";
?>

         <link rel="stylesheet" type="text/css" href="../css/cool-link/style.css" />
    <div>
     <a href="javascript:void(0)" onclick="toggle_visibility('as');" class="cool-link"><?php echo $assessment ?></a><br><br>
        <div id="as" style="display: none">
            &nbsp;&nbsp;&nbsp;<a <?php echo "href='./print_grading_sheet.php?course=$course_id'" ?>  class="cool-link">&#10070; Print empty Grading Sheet </a><br>
            
            &nbsp;&nbsp;&nbsp;<a <?php echo "href='./upload_result.php?course=$course_id'" ?>  class="cool-link">&#10070; Upload Result</a><br>
            
            &nbsp;&nbsp;&nbsp;<a <?php echo "href='./view result.php?course=$course_id'" ?>  class="cool-link">&#10070; View Result</a><br><br>
           
        </div>



<?php
       }


   }

?>
       <script type="text/javascript">
        function toggle_visibility(id) {
        var e = document.getElementById(id);
        if(e.style.display == 'block')
            e.style.display = 'none';
        else
            e.style.display = 'block';
        }
    </script>






<?php

echo $OUTPUT->footer();
?>