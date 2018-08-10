<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Delete Batch");
    $PAGE->set_heading("Delete Batch");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/delete_batch.php');
    
    require_login();
    if($SESSION->oberole != "itm"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    if(!empty($_GET['batch']))
    {
        $batch_id = $_GET['batch'];
        $find_bat_sem = $DB->get_records_sql('SELECT * FROM  `mdl_semester` WHERE batchid = ?', array($batch_id));
        if ($find_bat_sem) {
            echo "<h4 style='color: red'> Cannot delete this batch as it contains semesters.<br>Either delete the batch semesters or change their batch. </h4><br>";
        }
        else
        {
            $sql = "DELETE FROM mdl_batch WHERE id = ?";
            $DB->execute($sql, array($batch_id));

            echo "<h4 style='color: green'> Batch has been deleted successfully! </h4><br>"; 
        }
    }
    else
    {
        echo "<h4 style='color: red'> Invalid Selection </h4><br>";
    }
    ?>
    <a class="btn btn-default" href="./view_batch.php"> Go Back </a>
    <?php
    echo $OUTPUT->footer();
    ?>
