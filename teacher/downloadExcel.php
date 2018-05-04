<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Download Excel");
    $PAGE->set_heading("Download Excel");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/downloadExcel.php');

    
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }

     if(isset($_GET['id']) && isset($_GET['course']))
    {
        
        $Id = $_GET['id'];
        $courseId = $_GET['course'];
        $type = $_GET['type'];

        $data=array();
        $temp=array();
     $questions= $DB->get_records_sql("SELECT * FROM mdl_manual_quiz_question WHERE mquizid = ?",array($Id));
        
        $totalMarks=0;
            foreach ($questions as $ques) 
            {    
                
                $qmark = $ques->maxmark;
                $totalMarks += $qmark;
                $cloid = $ques->cloid;

                $qname = $ques->quesname;
                $qmark = $ques->maxmark;
                $qtext = $ques->questext;
                $qid   = $ques->id;
                   
                 $temp['Ques Name']=$qname;
                 $temp['Ques Text']=$qtext;
                 $temp['Marks']=$qmark;


                 $clos= $DB->get_records_sql("SELECT * FROM mdl_competency WHERE id = ?",array($cloid));
                 
             foreach ($clos as $clo) 
            {

                $shortname = $clo->shortname;
                $temp['CLO']=$shortname;
            }
            array_push($data, $temp);

            }

    function filterData(&$str)
    {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    }
    
    // file name for download
    $fileName = "$type" . date('dmY') . ".xls";
    
    // headers for download
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    header("Content-Type: application/vnd.ms-excel");
    
    $flag = false;
    foreach($data as $row) {
        if(!$flag) {
            // display column names as first row
            echo implode("\t", array_keys($row)) . "\n";
            $flag = true;
        }
        // filter data
        array_walk($row, 'filterData');
        echo implode("\t", array_values($row)) . "\n";

    }
    
    exit;
}
else
 echo "<font color = red> Error </font>";

?>