<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Course Profile");
    $PAGE->set_heading("Course Profile");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/view_course_profileform.php');

       header('Content-Type: text/plain');
   

      require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

if(isset($_GET['course'])  ){


 $course_id=$_GET['course'];
    //echo $course_id;
//$coursecode=$_GET['coursecode'];
//echo $coursecode;

$rec=$DB->get_recordset_sql('SELECT ci.coursecode,ci.theorycredithours,ci.practicalcredithours,ci.coursecontent,ci.book FROM mdl_course_info ci, mdl_course c WHERE c.id= ?',array($course_id));

if($rec){

 
            $table = new html_table();
     $table->head = array( 'Theory credithours','Practical credithours','Course Content','Book');

      foreach ($rec as $records) {

           // $coursecode = $records->coursecode;
            $theorycredithours=$records->theorycredithours;
             $practicalcredithours=$records->practicalcredithours;
             $coursecontent=$records->coursecontent;
             $book=$records->book;


             $table->data[] = array($theorycredithours,$practicalcredithours,$coursecontent,$book);


      }

if(!empty($book) && !empty($theorycredithours) &&  !empty($practicalcredithours) && !empty($coursecontent)){

echo html_writer::table($table);

}
else{

echo "<font color=red size=5>Course profile hasn't been entered yet!</font>";

}

}






}

?>

<?php 
        echo $OUTPUT->footer();
    ?>

