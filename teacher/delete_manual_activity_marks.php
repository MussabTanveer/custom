<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Delete Marks");
    $PAGE->set_heading("Delete Marks");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/delete_manual_activity_marks.php');
    
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
     
         if ($type == "quiz")
            {
            
                $sql = "DELETE FROM mdl_manual_quiz_attempt WHERE quizid = ?";
                $DB->execute($sql, array($Id)); 

                echo "<font color = green> Marks have been Deleted Successfully </font><br>"; 
           ?>
                <a href="./view_quiz_result.php?type=<?php echo $type; ?>&course=<?php echo $courseId; ?> "> Go Back </a>
             <?php
            }      
    
        elseif ($type =="midterm")
         {

            $sql = "DELETE FROM mdl_manual_quiz_attempt WHERE quizid = ?";
            $DB->execute($sql, array($Id));

            echo "<font color = green> Marks have been Deleted Successfully </font><br>"; 

        ?>
    <a href="./view_mid_result.php?type=<?php echo $type; ?>&course=<?php echo $courseId; ?> "> Go Back </a>

<?php
         }
         elseif ($type == "finalexam") { 

            $sql = "DELETE FROM mdl_manual_quiz_attempt WHERE quizid = ?";
            $DB->execute($sql, array($Id));

            echo "<font color = green> Marks have been Deleted Successfully </font><br>"; 

            ?>

            <a href="./view_final_result.php?type=<?php echo $type; ?>&course=<?php echo $courseId; ?> "> Go Back </a>
<?php   
      }
      elseif ($type == "project") {

           
            $sql = "DELETE FROM mdl_manual_assign_pro_attempt WHERE assignproid = ?";
            $DB->execute($sql, array($Id)); 
            
            echo "<font color = green> Marks have been Deleted Successfully </font><br>"; 
            ?>
            <a href="./select_project.php?type=<?php echo $type; ?>&course=<?php echo $courseId; ?> "> Go Back </a>

     <?php

      }
      elseif ($type == "assign") {
            # code...
           
            $sql = "DELETE FROM mdl_manual_assign_pro_attempt WHERE assignproid = ?";
            $DB->execute($sql, array($Id)); 

             echo "<font color = green> Marks have been Deleted Successfully </font><br>"; 
            ?>
            <a href="./select_assignment.php?type=<?php echo $type; ?>&course=<?php echo $courseId; ?> "> Go Back </a>

     <?php
      }

 elseif ($type == "other") {
        # code...
        

        $sql = "DELETE FROM mdl_manual_other_attempt WHERE otherid = ?";
        $DB->execute($sql, array($Id));


         echo "<font color = green> Marks have been Deleted Successfully </font><br>"; 
            ?>
            <a href="./select_other.php?type=<?php echo $type; ?>&course=<?php echo $courseId; ?> "> Go Back </a>
<?php

}

    }
    else
    {
        echo "<font color = red> Error </font><br>";


     echo $OUTPUT->footer();

     }

      echo $OUTPUT->footer();

?>
    




