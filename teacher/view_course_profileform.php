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

$coursecodesql = $DB->get_records_sql('SELECT idnumber FROM mdl_course WHERE id=?', array($course_id));

$course_code = "";
foreach($coursecodesql as $cc){
    $course_code = $cc->idnumber;
}

$rec=$DB->get_records_sql('SELECT * FROM mdl_course_info WHERE coursecode= ?',array($course_code));

if($rec && $course_code){

    foreach ($rec as $records) {
        $theorycredithours=$records->theorycredithours;
        $practicalcredithours=$records->practicalcredithours;
        $coursecontent=$records->coursecontent;
        $book=$records->book;

        echo "<h3>Theory Credit Hours</h3>";
        echo $theorycredithours;
        echo "<br><br>";

        echo "<h3>Practical Credit Hours</h3>";
        echo $practicalcredithours;
        echo "<br><br>";

        echo "<h3>Course Content</h3>";
        echo $coursecontent;
        echo "<br><br>";

        echo "<h3>Books</h3>";
        echo $book;
        echo "<br><br>";

    }

}

else{

echo "<font color=red size=5>Course profile hasn't been entered yet!</font>";

}


}

?>

<?php 
        echo $OUTPUT->footer();
    ?>

