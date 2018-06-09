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

    ?>

    <style>
        h3 {
            text-decoration: underline;
        }
    </style>
    
    <?php

    if(!empty($_GET['course'])){

        $course_id=$_GET['course'];
        //echo $course_id;

        $coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
        
        $coursecodesql = $DB->get_records_sql('SELECT idnumber FROM mdl_course WHERE id=?', array($course_id));

        $course_code = "";
        foreach($coursecodesql as $cc){
            $course_code = $cc->idnumber;
        }

        $rec=$DB->get_records_sql('SELECT * FROM mdl_course_info WHERE coursecode= ?',array($course_code));

        $recclos=$DB->get_recordset_sql('SELECT
        clo.shortname,
        clo.idnumber,
        clo.description,
        plo.shortname as ploname,
        plo.idnumber,
        taxlvl.name,
        taxlvl.level,
        taxdom.name as taxname
    
        FROM 
        mdl_competency clo,
        mdl_competency plo, 
        mdl_competency_coursecomp compcour,
        mdl_taxonomy_clo_level taxclolvl,
        mdl_taxonomy_levels taxlvl,
        mdl_taxonomy_domain taxdom
        WHERE clo.id=compcour.competencyid and clo.id=taxclolvl.cloid and taxclolvl.levelid=taxlvl.id and taxlvl.domainid=taxdom.id and plo.id=clo.parentid and courseid=?'
        ,array($course_id));

        if($rec && $recclos && $course_code){

            foreach ($rec as $records) {
                $theorycredithours=$records->theorycredithours;
                $practicalcredithours=$records->practicalcredithours;
                $coursecontent=$records->coursecontent;
                $book=$records->book;
                $title=$records->coursename;
            }
            echo "<h3>Course Code: $course_code</h3>";
            echo "<br><br>";

            echo "<h3>Course Title: $title</h3>";
            echo "<br><br>";

            echo "<h3>Theory Credit Hours: $theorycredithours</h3>";
            echo "<br><br>";

            echo "<h3>Practical Credit Hours: $practicalcredithours</h3>";
            echo "<br><br>";

            echo "<h3>Course Content</h3>";
            echo $coursecontent;
            echo "<br><br>";
            
            
            if($recclos){
                $serial=0;
                $table = new html_table();
                $table->head = array('S.No','CLO Name','Description','Taxonomy level','PLO');
                foreach ($recclos as $records) {
                    $serial++;
                    $shortname = $records->shortname;
                    //$idnumber = $records->idnumber;
                    $description = $records->description;
                    $name=$records->name;
                    $name1=$records->taxname;
                    $level=$records->level;
                    $shortname1=$records->ploname;
                    $idnumber=$records->idnumber;
                    //$peo=$records->peo;
                    $table->data[] = array($serial,$shortname, $description,ucwords($name1)."<br>"."(".ucwords($level)." ".ucwords($name).")",strtoupper($idnumber)."<br>".$shortname1);
                }

                $recclos->close();

                if($serial){
                    echo "<h3>Course Learning Outcomes (CLOs)</h3>";
                    echo html_writer::table($table);
                    echo "<br><br>";
                }
                else
                    echo "<h5 style='color:red'> <br />Found no CLO of this Course! </h5>";
            }

            echo "<h3>Books</h3>";
            echo $book;
            echo "<br><br>";
        }

        else{
            echo "<h3 style='color:red'>Course profile has not been entered by the chairman yet!</h3>";
        }

        echo "<a class='btn btn-default' href='./report_teacher.php?course=$course_id'>Go Back</a>";

    }
    else {

    }

    echo $OUTPUT->footer();
?>
