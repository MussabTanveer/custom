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
    


if(isset($_POST['quizid'])) {

$quizid=$_POST['quizid'];

//echo $quizid;
$rec=$DB->get_recordset_sql(
        'SELECT 
            qa.userid,
            us.idnumber,
            us.username,
            qa.attempt,
            qu.name,
            c.shortname,
            qu.questiontext,
            qua.rightanswer,
            qua.responsesummary,
            qua.maxmark,
            qua.maxmark*COALESCE(qas.fraction, 0) AS marksobtained,
            qc.name AS category
        FROM
            mdl_quiz q,
            mdl_quiz_slots qs,
            mdl_user us,
            mdl_question qu,
            mdl_question_categories qc,
            mdl_quiz_attempts qa,
            mdl_question_attempts qua,
            mdl_competency c,
            mdl_question_attempt_steps qas
        WHERE
            q.id=? AND qa.attempt=? AND qa.userid= ? AND q.id=qs.quizid AND qu.id=qs.questionid AND us.id=qa.userid   AND qu.category=qc.id AND q.id=qa.quiz AND c.id=qu.competencyid
            AND qa.uniqueid=qua.questionusageid AND qu.id=qua.questionid AND qua.id=qas.questionattemptid AND qas.state IN ("gradedright", "gradedwrong", "gaveup", "gradedpartial")
        ORDER BY  qu.id',
        
        array($quizid, 1,$USER->id));


if($rec){

       
        $table = new html_table();
        $table->head = array('Question #', 'Question' , 'CLO', 'Max Marks', 'Marks obtained');
        foreach ($rec as $records) {
                               $qname = $records->name;
                                $qtext = $records->questiontext;
                                $competency=$records->shortname;
        
                     $qmax = $records->maxmark; 
                     $qmax = number_format($qmax, 2);
                     $mobtained = $records->marksobtained; 
                     $mobtained = number_format($mobtained, 2);
$table->data[] = array($qname, $qtext,$competency, $qmax,$mobtained);


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
