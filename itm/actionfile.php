<script src="../script/jquery/jquery-3.2.1.js"></script>
<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Courses");
    $PAGE->set_heading("Select Courses");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/itm/actionfile.php');
    echo $OUTPUT->header();
    require_login();
    ?>
    <?php
    if ((isset($_GET['id_enddate']) || isset($_GET['inputtext'])) && isset($_GET['dropdown'])){
        $name=$_GET['inputtext'];
        $date=$_GET['id_enddate'];
        $dropdown=$_GET['dropdown'];
        $bat=substr("$name",2,2);
    }
    if($dropdown=='coursecode'){
    $rec=$DB->get_records_sql('SELECT * FROM `mdl_course` WHERE idnumber = "'.$name.'" ');
    $rec||die('<h3> No data available or wrong entry <h3>'.$OUTPUT->footer());
    }
    if($dropdown=='coursename'){
    $rec=$DB->get_records_sql('SELECT * FROM `mdl_course` WHERE fullname = "'.$name.'" ');    
    $rec||die('<h3> No data available or wrong entry <h3>'.$OUTPUT->footer());
    }
    if($dropdown=='shortname'){
    $rec=$DB->get_records_sql('SELECT * FROM `mdl_course` WHERE shortname = "'.$name.'" ');
    $rec||die('<h3> No data available or wrong entry <h3>'.$OUTPUT->footer());
    }
    if($dropdown == 'startdate'){
    $courses=$DB->get_records_sql('SELECT * FROM `mdl_course` WHERE id != ?', array(1));
    if($courses)
    {
        foreach ($courses as $course)
        {
            $dates=$course->startdate;
            $datevalue=date('Y-m-d',$dates);
            if ($datevalue == $date)
            {break;}
        }
    }
    $rec=$DB->get_records_sql('SELECT * FROM `mdl_course` WHERE startdate = "'.$dates.'" ');
     $rec||die('<h3> No data available <h3>'.$OUTPUT->footer());
     }
     if($dropdown == 'enddate'){
        $courses=$DB->get_records_sql('SELECT * FROM `mdl_course` WHERE id != ?', array(1));
        if($courses)
        {
        foreach ($courses as $course)
        {
            $dates=$course->enddate;
            $datevalue=date('Y-m-d',$dates);
            if ($datevalue == $date)
            {break;}
        }
        }
        $rec=$DB->get_records_sql('SELECT * FROM `mdl_course` WHERE enddate = "'.$dates.'" ');
        $rec||die('<h3> No data available <h3>'.$OUTPUT->footer());
         }
    if($rec){
        $i = 1;
        foreach ($rec as $records){
            $fullname = $records->fullname;
            $shortname = $records->shortname;
            $idnumber = $records->idnumber;
            $id=$records->id;
            echo "<h4><a href='../../../enrol/users.php?id=$id' title='Enrol Users'>$i. $fullname ($shortname $idnumber)</a></h4><br />";
            $i++;
        }
    }
    else{
        echo "<h3>No courses found!</h3>";
    }
    echo $OUTPUT->footer();
?>