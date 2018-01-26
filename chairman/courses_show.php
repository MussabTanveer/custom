<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Course View");
    $PAGE->set_heading("Course View");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/courses_show.php');
    echo $OUTPUT->header();
	require_login();
?>
<?php
$date=date("Y/m/d");

//echo $date;
$time=time();

//echo $time;


$sql=$DB->get_records_sql('Select fullname as "Present Courses",shortname,idnumber  from mdl_course where enddate > $time and fullname NOT LIKE "CIS" ');
echo "hello";
if($sql){

  $table = new html_table();
  $table->head = array('S.no','Fullname','Shortname','Idnumber');



}



?>


