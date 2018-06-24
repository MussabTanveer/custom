<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Activity Results");
    $PAGE->set_heading("Activity Results");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/display_quiz_grid.php');
    
    require_login();
    if($SESSION->oberole != "student"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    


if(isset($_POST['assignid'])) {

$assignid=$_POST['assignid'];


$recordsComp=$DB->get_records_sql("SELECT DISTINCT c.id, c.shortname
        
        FROM mdl_competency c, mdl_assign a, mdl_course_modules cm, mdl_competency_modulecomp cmc

        WHERE a.id=? AND cm.module=? AND a.id=cm.instance AND cm.id=cmc.cmid AND cmc.competencyid=c.id
        
        ORDER BY cmc.competencyid",
        
        array($assignid,1));

        // Display Assign Info
        echo "<h3>Assignment ";
        foreach ($recordsComp as $recC) {
            $comp = $recC->shortname;
            echo "$comp";
        }
        echo "</h3>";

//echo $assignid;
 $rec=$DB->get_recordset_sql(
            'SELECT
            ag.userid,
           
            
            a.grade AS maxmark,
            ag.grade AS marksobtained
            FROM
                mdl_assign a,
                mdl_assign_grades ag
                
            WHERE
                a.id=? AND ag.userid=? AND ag.grade != ? AND a.id=ag.assignment
            ',
            
        array($assignid,$USER->id,-1));


if($rec){

       
        $table = new html_table();
        $table->head = array( 'Max Marks', 'Marks obtained');
        foreach ($rec as $records) {
                               
        
                     $qmax = $records->maxmark; 
                     $qmax = number_format($qmax, 2);
                     $mobtained = $records->marksobtained; 
                     $mobtained = number_format($mobtained, 2);
$table->data[] = array( $qmax,$mobtained);


}

echo html_writer::table($table);


}
else{

echo "Error";

}

?>








     <?php
        }
    
    echo $OUTPUT->footer();
    ?>
