<?php 
    require_once('../../../config.php');
    require_once('../script/spout-2.4.3/spout-2.4.3/src/Spout/Autoloader/autoload.php');
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/print_assignment_grading_sheet.php');
    use Box\Spout\Writer\WriterFactory;
    use Box\Spout\Common\Type;
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }

    if(!empty($_GET['id']) && !empty($_GET['course']))
    {
        $course_id=$_GET['course'];
        $quiz_id=$_GET['id'];

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
        $add=1;
        $arr=array();
        $arr=["Seatno","Marks"];

        //Creating file type
        $inputFileName = "Other_activity_Grading_sheet.xlsx";
        
        // for XLSX files
        $writer = WriterFactory::create(Type::XLSX);
        //$writer = WriterFactory::create(Type::CSV); // for CSV files
        //$writer = WriterFactory::create(Type::ODS); // for ODS files
        $tempfile=$writer->openToFile($inputFileName); // write data to a file or to a PHP stream
        //$writer->openToBrowser($fileName); // stream data directly to the browser
        $writer->addRow($arr);
        foreach($seatnos as $seat)
            $writer->addRow([$seat]); // add a row at a time
        //$writer->addRows($multipleRows); // add multiple rows at a time
        //$writer->setTempFolder('E:/');
        $writer->close();
        header('Content-Type:Other_activity_Grading_sheet/xlsx');
        header('Content-disposition: attachment; filename="'.$inputFileName.'";');
        readfile("Other_activity_Grading_sheet.xlsx"); 
}
?>
