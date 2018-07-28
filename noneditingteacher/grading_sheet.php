<?php 
    require_once('../../../config.php');
    require_once('../script/spout-2.4.3/spout-2.4.3/src/Spout/Autoloader/autoload.php');
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/noneditingteacher/grading_sheet.php');
    use Box\Spout\Writer\WriterFactory;
    use Box\Spout\Common\Type;
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    
    if(!empty($_GET['assessmentid']) && !empty($_GET['course']))
    {
        $course_id=$_GET['course'];
       // $quiz_id=$_GET['id'];



        $rec=$DB->get_recordset_sql('SELECT
            clo.id AS cloid,
            clo.shortname,
            clo.idnumber,
            clo.description,
            plo.shortname as ploname,
            plo.idnumber,
            taxlvl.id AS lvlid,
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
            $cloid = 0; $levelid = 0;
            $flag=0;
            foreach ($rec as $records) {
                $flag++;
                $cloid = $records->cloid;
                $levelid = $records->lvlid;
                if($levelid>=7) // level belongs to psychomotor or affective domain
                    break;
            }

            $rec->close();

            if($flag){
                //echo "CLOID: $cloid LEVELID: $levelid";
                $flagR=0; $rubric_id=0;
                $recR=$DB->get_recordset_sql('SELECT rubric FROM mdl_clo_rubric WHERE cloid=?', array($cloid));
                foreach ($recR as $R) {
                    $flagR++;
                    $rubric_id = $R->rubric;
                }
            }
        }






        // Get all students of course
        $recStudents=$DB->get_records_sql("SELECT u.id AS sid,substring(u.username,4,8) AS seatorder ,u.username AS seatnum, u.firstname, u.lastname
        FROM mdl_role_assignments ra, mdl_user u, mdl_course c, mdl_context cxt
        WHERE ra.userid = u.id
        AND ra.contextid = cxt.id
        AND cxt.contextlevel = ?
        AND cxt.instanceid = c.id
        AND c.id = ?
        AND (roleid=5) ORDER BY seatorder", array(50, $course_id));

        // push student ids and seat nums to array
        $stdids = array();
        $seatnos = array();
        foreach($recStudents as $records){
            $id = $records->sid;
            $seatno = $records->seatnum ;
            array_push($stdids,$id);
            array_push($seatnos,$seatno);
        }
        // Get all Criterion of Rubric
        $recCriterion=$DB->get_records_sql('SELECT * FROM mdl_rubric_criterion WHERE rubric = ?', array($rubric_id));

        // push question ids and names to array
        $CriterionIds = array();
        $CriterionDesc = array();
        foreach($recCriterion as $records){
            $id = $records->id;
            $desc = $records->description ;
            array_push($CriterionIds,$id);
            array_push($CriterionDesc,$desc);
        }
        //exporting data to file
        $add=1;
        $arr=array();
        $arr[0]="Seatno";
        foreach ($CriterionDesc as $desc){
            $arr[$add]=$desc;
            $add++;
        }

        //Creating file type
        $inputFileName = "Grading_sheet.CSV";
        
        // for CSV files
        $writer = WriterFactory::create(Type::CSV);
        //$writer = WriterFactory::create(Type::CSV); // for CSV files
        //$writer = WriterFactory::create(Type::ODS); // for ODS files
        $tempfile=$writer->openToFile($inputFileName); // write data to a file or to a PHP stream
        //$writer->openToBrowser($fileName); // stream data directly to the browser
        $writer->addRow($arr);
        foreach($seatnos as $seat)       
            $writer->addRow([$seat]); // add a row at a time
        //$writer->addRows($multipleRows); // add multiple rows at a time
        //  $writer->setTempFolder('E:/');
        $writer->close();  
        header('Content-Type:Grading_sheet/CSV');
        header('Content-disposition: attachment; filename="'.$inputFileName.'";');
        readfile("Grading_sheet.CSV");  
    }
?>
