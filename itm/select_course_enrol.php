<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Choose Course");
    $PAGE->set_heading("Choose Course for User Enrolment");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/itm/select_course_enrol.php');
    
    require_login();
    if($SESSION->oberole != "itm"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    if(isset($_POST['inputtext'])){
        $nam=$_POST['inputtext'];
    }
?>

    <form action="actionfile.php" class="form-inline">
        <h5 autofocus style="text-align:center;">Filter Records By</h5>
        <p style="text-align:center;">
        <select autofocus id="Filtercheck" name="dropdown"  onChange="datepicker()" class="select custom-select">
        <option value="coursecode">Course Code</option>
        <option value="coursename">Course Name</option>
        <option value="shortname">Short Name</option>
        <option value="startdate">Start Date</option>
        <option value="enddate">End Date</option>
        </select>
        <input type="date" name="id_enddate" id="id_enddate"  >
        <input id="inputcheck" name="inputtext" type="text" placeholder="Enter Value" class="form-control">
        <input id="buttoncheck" type="submit" class="btn btn-info">
        <body onload="datepicker()">
        
        </p>
    </form>
    <br />
    <script>

var a=document.getElementById('Filtercheck').value;
document.getElementById('id_enddate').style.display="none";
    function datepicker(){
        var a=document.getElementById('Filtercheck').value;
        if (a=='startdate' || a=='enddate'){
            document.getElementById('inputcheck').style.display="none";
            document.getElementById('id_enddate').style.display="inline";
        }
        else{
            document.getElementById('inputcheck').style.display="inline";
            document.getElementById('id_enddate').style.display="none";
        }
    }
    </script>
<?php

	//Displaying all courses
	$courses=$DB->get_records_sql('SELECT * FROM `mdl_course` WHERE id != ?', array(1));
    
    if($courses){
        $i = 1;
        foreach ($courses as $records){
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
