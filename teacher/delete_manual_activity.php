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
     
         if ($type == "quiz")
            {
                 $sql = "DELETE FROM mdl_manual_quiz WHERE id = $Id";
                 $DB->execute($sql);

                $sql = "DELETE FROM mdl_manual_quiz_attempt WHERE quizid = $Id";
                $DB->execute($sql);

                $sql = "DELETE FROM mdl_manual_quiz_question WHERE  mquizid = $Id";
                $DB->execute($sql);

                $sql = "DELETE FROM mdl_parent_mapping WHERE  childid = $Id AND module=-1";
                $DB->execute($sql);

                echo "<font color = green> Activity has been Deleted Successfully </font><br>"; 
           ?>
                <a href="./print_quiz_paper.php?type=<?php echo $type; ?>&course=<?php echo $courseId; ?> "> Go Back </a>
             <?php
            }      
    
        elseif ($type =="midterm")
         {

             $sql = "DELETE FROM mdl_manual_quiz WHERE id = $Id";
            $DB->execute($sql);

            $sql = "DELETE FROM mdl_manual_quiz_attempt WHERE quizid = $Id";
            $DB->execute($sql);

            $sql = "DELETE FROM mdl_manual_quiz_question WHERE  mquizid = $Id";
            $DB->execute($sql);

            $sql = "DELETE FROM mdl_parent_mapping WHERE  childid = $Id AND module=-2";
           $DB->execute($sql);

            echo "<font color = green> Activity has been Deleted Successfully </font><br>"; 

        ?>
    <a href="./print_mid_paper.php?type=<?php echo $type; ?>&course=<?php echo $courseId; ?> "> Go Back </a>

<?php
         }
         elseif ($type == "finalexam") { 

             $sql = "DELETE FROM mdl_manual_quiz WHERE id = $Id";
            $DB->execute($sql);

            $sql = "DELETE FROM mdl_manual_quiz_attempt WHERE quizid = $Id";
            $DB->execute($sql);

            $sql = "DELETE FROM mdl_manual_quiz_question WHERE  mquizid = $Id";
            $DB->execute($sql);

            $sql = "DELETE FROM mdl_parent_mapping WHERE  childid = $Id AND module=-3";
           $DB->execute($sql);

            echo "<font color = green> Activity has been Deleted Successfully </font><br>"; 

            ?>

            <a href="./print_final_paper.php?type=<?php echo $type; ?>&course=<?php echo $courseId; ?> "> Go Back </a>
<?php   
      }
      elseif ($type == "project") {

              $sql = "DELETE FROM mdl_manual_assign_pro WHERE id = $Id";
            $DB->execute($sql);

            $sql = "DELETE FROM mdl_manual_assign_pro_attempt WHERE assignproid = $Id";
            $DB->execute($sql); 

            $sql = "DELETE FROM mdl_parent_mapping WHERE  childid = $Id AND module=-5";
           $DB->execute($sql);

         echo "<font color = green> Activity has been Deleted Successfully </font><br>"; 
            ?>
            <a href="./print_project_paper.php?type=<?php echo $type; ?>&course=<?php echo $courseId; ?> "> Go Back </a>

     <?php

      }
      elseif ($type == "assign") {
          # code...
          $sql = "DELETE FROM mdl_manual_assign_pro WHERE id = $Id";
            $DB->execute($sql);

            $sql = "DELETE FROM mdl_manual_assign_pro_attempt WHERE assignproid = $Id";
            $DB->execute($sql); 

           $sql = "DELETE FROM mdl_parent_mapping WHERE  childid = $Id AND module=-4";
           $DB->execute($sql);

         echo "<font color = green> Activity has been Deleted Successfully </font><br>"; 
            ?>
            <a href="./print_assign_paper.php?type=<?php echo $type; ?>&course=<?php echo $courseId; ?> "> Go Back </a>

     <?php
      }

 elseif ($type == "other") {
          # code...
          $sql = "DELETE FROM mdl_manual_other WHERE id = $Id";
            $DB->execute($sql);

            $sql = "DELETE FROM mdl_manual_other_attempt WHERE otherid = $Id";
            $DB->execute($sql); 

            $sql = "DELETE FROM mdl_parent_mapping WHERE  childid = $Id AND module=-6";
           $DB->execute($sql);

         echo "<font color = green> Activity has been Deleted Successfully </font><br>"; 
            ?>
            <a href="./view_other1.php?type=<?php echo $type; ?>&course=<?php echo $courseId; ?> "> Go Back </a>
<?php

}

    }
    else
    {
        echo "<font color = red> Error </font><br>";


?>
        <a href="./view_other1.php" > Go Back </a>
    <?php


     echo $OUTPUT->footer();

     }
?>
    




