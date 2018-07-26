<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("View Weightage");
    $PAGE->set_heading("View Weightage");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/view_weightage.php');
    
	require_login();
    if($SESSION->oberole != "chairman"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    $names = array(); $percents = array();
    $records=$DB->get_records_sql('SELECT * FROM `mdl_grading_policy_chairman` ORDER BY revision DESC LIMIT 3');
    if($records){
        foreach ($records as $rec){
            $name = $rec->name;
            $percent = $rec->percentage;
            array_push($names, $name);
            array_push($percents, $percent);
        }
    }
    
    /*for($i=0; $i<count($records); $i++){
        echo $names[$i]."<br>";
        echo $percents[$i]."%<br>";
    }*/

    $m_pos = array_search("mid term",$names);
    $a_pos = array_search("activities",$names);
    $f_pos = array_search("final exam",$names);

    if($a_pos >= 0){
        echo "<h4>Quiz, Assignment, Project, Other: $percents[$a_pos]%</h4><br>";
    }
    if($m_pos >= 0){
        echo "<h4>Midterm: $percents[$m_pos]%</h4><br>";
    }
    if($f_pos >= 0){
        echo "<h4>Final Exam: $percents[$f_pos]%</h4><br>";
    }

    echo "<br><a href='./assign_weightage.php'>Revise Weightage</a><br><br>";
    require '../templates/print_template.html';
    echo '<a class="btn btn-default" href="./report_chairman.php">Go Back</a>';

    echo $OUTPUT->footer();
?>
