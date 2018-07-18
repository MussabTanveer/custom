<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Activity Results");
    $PAGE->set_heading("Activity Results");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/display_assign_grid.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <script src="../script/table2excel/jquery.table2excel.min.js"></script>
    <?php

    if(!empty($_GET['course']) && !empty($_GET['assignid']))
    {
        $course_id=$_GET['course'];
        $coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
        $assign_id=$_GET['assignid'];

        // Report Header (Uni. name, Dept. name, course code and title)
        $un=$DB->get_records_sql('SELECT * FROM  `mdl_vision_mission` WHERE idnumber = ?', array("un"));
        if($un){
            foreach($un as $u){
                $uniName = $u->description;
            }
            $uniName = strip_tags($uniName); 
            echo "<h3 style='text-align:center'>".strtoupper($uniName)."</h3>";         
        }
        $dn=$DB->get_records_sql('SELECT * FROM  `mdl_vision_mission` WHERE idnumber = ?', array("dn"));
        if($dn){
            foreach($dn as $d){
                $deptName = $d->description;
            }
            $deptName = strip_tags($deptName); 
            echo "<h3 style='text-align:center'>".strtoupper($deptName)."</h3>";         
        }
        $course = $DB->get_record('course',array('id' => $course_id));
        echo "<h4 style='text-align:center'>Course Code: <u>".($course->idnumber)."</u>,";
        echo " Course Title: <u>".($course->fullname)." (".($course->shortname).")</u></h4>";
        echo "<h4 style='text-align:center'>Activity Detailed Report</h4>";


        //echo "Assign ID : $assign_id";
        
        //Get assign comp
        $recordsComp=$DB->get_records_sql("SELECT DISTINCT c.id, c.shortname, a.name, a.grade
        
        FROM mdl_competency c, mdl_assign a, mdl_course_modules cm, mdl_competency_modulecomp cmc

        WHERE a.id=? AND cm.course=? AND cm.module=? AND a.id=cm.instance AND cm.id=cmc.cmid AND cmc.competencyid=c.id
        
        ORDER BY cmc.competencyid",
        
        array($assign_id,$course_id,1));

        // Display Assign Info
        echo "<h3>";
        foreach ($recordsComp as $recC) {
            $name = $recC->name;
            echo "$name";
            $comp = $recC->shortname;
            echo " ($comp)";
            $maxmarks = $recC->grade;
            echo "<br>Max Marks: $maxmarks";
        }
        echo "</h3>";
            
        $rec=$DB->get_recordset_sql(
            'SELECT
            ag.userid,
            u.idnumber AS std_id,
            u.username AS seat_no,
            CONCAT(u.firstname, " ", u.lastname) AS std_name,
            a.grade AS maxmark,
            ag.grade AS marksobtained
            FROM
                mdl_assign a,
                mdl_assign_grades ag,
                mdl_user u
            WHERE
                a.id=? AND ag.userid=u.id AND ag.grade != ? AND a.id=ag.assignment
            ORDER BY ag.userid',
            
        array($assign_id,-1));

        if($rec){
            ?>
            <table class="generaltable">
                <tr class="table-head">
                    <th> Seat No. </th>
                    <th> Max Marks </th>
                    <th> Marks Obtained </th>
                </tr>
                <?php
                foreach ($rec as $records){
                    //$serialno++;
                    $seat = $records->seat_no;
                    //$uname = $records->std_name;
                    $max = $records->maxmark;
                    $obt = $records->marksobtained; $obt = number_format($obt, 2);
                    //$result = ($obt/$max)*100;
                    ?>
                    <tr>
                    <td><?php echo $seat;?></td>
                    <td><?php echo $max;?></td>
                    <td><?php echo $obt;?></td>
                    </tr>
                    <?php
                }    
                ?>
                </table>
                <button id="myButton" class="btn btn-primary">Export to Excel</button>

            <!-- Export html Table to xls -->
            <script type="text/javascript" >
                $(document).ready(function(e){
                    $("#myButton").click(function(e){ 
                        $(".generaltable").table2excel({
                            name: "file name",
                            filename: "assignment-report",
                            fileext: ".xls"
                        });
                    });
                });
            </script>
            <?php
        }
        else{
            echo "<h3>No students have attempted this assignment!</h3>";
        }

        ?>

        <?php
        echo $OUTPUT->footer();
    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./teacher_courses.php">Back</a>
    <?php 
        echo $OUTPUT->footer();
    }?>
