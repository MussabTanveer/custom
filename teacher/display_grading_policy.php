<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Display Grading Policy");
    $PAGE->set_heading("Display Grading Policy");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/display_grading_policy.php');
    echo $OUTPUT->header();
    require_login();


?>
 
 
<?php
     
if(isset($_GET['course'])){

    

$course_id=$_GET['course'];
//echo $course_id;

echo "<h3>Grading policy View</h3>";

$rec=$DB->get_records_sql('SELECT name, percentage FROM mdl_grading_policy WHERE courseid=?',array($course_id));

if($rec){
            
             $serial=0;
            $table = new html_table();
            $table->head = array('S.No','Activity','Perecentage');
         foreach ($rec as $records) {

                $serial++;

                $name=$records->name;
                $percentage=$records->percentage;


                 $table->data[] = array($serial,strtoupper($name), $percentage.'%');
                   

}




 if($serial)
                echo html_writer::table($table);
            else
               echo "<h5 style='color:red'> <br />Found no Graded Activity of this Course! </h5>";

}

}



?>

<?php
echo $OUTPUT->footer();

?>









