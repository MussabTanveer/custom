


<?php 
   require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Print Quiz");
    $PAGE->set_heading("Print Quiz");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/print_quiz_paper.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    
    if(isset($_GET['course']))
    {
        $course_id=$_GET['course'];
       // echo "$course_id";
         $quizzes= $DB->get_records_sql("SELECT * FROM mdl_manual_quiz WHERE courseid = ?",array($course_id));

        if($quizzes)
        {
            foreach ($quizzes as $quiz) 
            {
                # code...
                $qname = $quiz->name;
                $qdesc = $quiz->description;
                $qid   = $quiz->id;
            ?>
            <a <?php echo "href='./print_quiz.php?quiz=$qid&courseid=$course_id'" ?> > Print <?php echo $qname; ?> </a><br>
            
            <?php
            }

        }

        else
            echo "<font color = red> No Quiz Found!</font>";

    }


    
    
   

?>