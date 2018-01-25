<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Course CLOs");
    $PAGE->set_heading("Course CLO Mapping");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/display_course_clos.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    
    if((isset($_POST['submit']) && isset( $_POST['courseid'])) || (isset($SESSION->cid4) && $SESSION->cid4 != "xyz"))
    {
        if(isset($SESSION->cid4) && $SESSION->cid4 != "xyz")
        {
            $course_id=$SESSION->cid4;
            $SESSION->cid4 = "xyz";
        }
        else
            $course_id=$_POST['courseid'];
    
        echo "<h3> Associated CLOs with Course and their Mapping </h3>" ;

        $rec=$DB->get_recordset_sql('SELECT

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
        
        if($rec){
            $serial=0;
            $table = new html_table();
            $table->head = array('S.No','CLO Name','Description','Taxonomy level','PLO');
            foreach ($rec as $records) {
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

            $rec->close();

            if($serial)
                echo html_writer::table($table);
            else
                echo "<h5 style='color:red'> <br />Found no CLO of this Course! </h5>";
        }

        echo $OUTPUT->footer();
    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./display_courses-4.php">Back</a>
    <?php
        echo $OUTPUT->footer();
    }?>
?>
