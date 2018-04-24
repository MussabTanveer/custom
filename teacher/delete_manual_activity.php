<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Delete Activity");
    $PAGE->set_heading("Delete a Activity");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/delete_manual_activity.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();


    if(isset($_GET['id']) && isset($_GET['course']))
    {
        
        $Id = $_GET['id'];
        $courseId = $_GET['course'];
        $type = $_GET['type'];
      //  echo "$Id";
        //echo "$courseId";
        $sql = "DELETE FROM mdl_manual_quiz WHERE id = $Id";
        $DB->execute($sql);

        $sql = "DELETE FROM mdl_manual_quiz_attempt WHERE quizid = $Id";
        $DB->execute($sql);

        $sql = "DELETE FROM mdl_manual_quiz_question WHERE  mquizid = $Id";
        $DB->execute($sql);

        echo "<font color = green> Activity has been Deleted Successfully </font><br>"; 
     
         if ($type == "quiz")
            {
           ?>
                <a href="./print_quiz_paper.php?type=<?php echo $type; ?>&course=<?php echo $courseId; ?> "> Go Back </a>
             <?php
            }      
    
        elseif ($type =="midterm")
         {

        ?>
    <a href="./print_mid_paper.php?type=<?php echo $type; ?>&course=<?php echo $courseId; ?> "> Go Back </a>

<?php
         }
         elseif ($type == "finalexam") { ?>

            <a href="./print_final_paper.php?type=<?php echo $type; ?>&course=<?php echo $courseId; ?> "> Go Back </a>
<?php   
      }


    }
    else
    {
        echo "<font color = red> Error </font><br>";
    ?>
        <a href="./print_quiz_paper.php" > Go Back </a>
    <?php
    }




 echo $OUTPUT->footer();
