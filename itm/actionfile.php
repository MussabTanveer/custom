<script src="../script/jquery/jquery-3.2.1.js"></script>
<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Courses");
    $PAGE->set_heading("Select Courses");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/itm/actionfile.php');
    
    require_login();
    if($SESSION->oberole != "itm"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    if((!empty($_GET['date']) || !empty($_GET['inputtext'])) && !empty($_GET['dropdown'])){
        $name=$_GET['inputtext'];
        $date=$_GET['date'];
        $dropdown=$_GET['dropdown'];
        
        if($dropdown=='coursecode'){
            $rec=$DB->get_records_sql('SELECT * FROM `mdl_course` WHERE idnumber = "'.$name.'" ORDER BY id DESC');
            $rec||die('<h3> No data available or wrong entry <h3>'.$OUTPUT->footer());
        }
        if($dropdown=='coursename'){
            $rec=$DB->get_records_sql('SELECT * FROM `mdl_course` WHERE fullname = "'.$name.'" ORDER BY id DESC');    
            $rec||die('<h3> No data available or wrong entry <h3>'.$OUTPUT->footer());
        }
        if($dropdown=='shortname'){
            $rec=$DB->get_records_sql('SELECT * FROM `mdl_course` WHERE shortname = "'.$name.'" ORDER BY id DESC');
            $rec||die('<h3> No data available or wrong entry <h3>'.$OUTPUT->footer());
        }
        if($dropdown == 'startdate'){
            $date = strtotime($date);
            $rec=$DB->get_records_sql('SELECT * FROM `mdl_course` WHERE startdate = "'.$date.'" ORDER BY id DESC');
            $rec||die('<h3> No data available <h3>'.$OUTPUT->footer());
        }
        if($dropdown == 'enddate'){
            $date = strtotime($date);
            $rec=$DB->get_records_sql('SELECT * FROM `mdl_course` WHERE enddate = "'.$date.'" ORDER BY id DESC');
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
    }
    else {
        ?>
        <h2 style="color:red;"> Invalid Search </h2>
        <a href="./select_course_enrol.php">Back</a>
    <?php
    }
    echo $OUTPUT->footer();
?>