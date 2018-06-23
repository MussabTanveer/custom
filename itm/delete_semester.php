<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Delete Activity");
    $PAGE->set_heading("Delete a Activity");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/delete_semester.php');
    
    require_login();
    if($SESSION->oberole != "itm"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    if(!empty($_GET['semester']))
    {
        $semester_id = $_GET['semester'];
        $find_sem_courses = $DB->get_records_sql('SELECT * FROM  `mdl_course` WHERE semesterid = ?', array($semester_id));
        if ($find_sem_courses) {
            echo "<h4 style='color: red'> Cannot delete this semester as it contains courses.<br>Either delete the semester courses or change their semester. </h4><br>";
        }
        else
        {
            $sql = "DELETE FROM mdl_semester WHERE id = ?";
            $DB->execute($sql, array($semester_id));

            echo "<h4 style='color: green'> Semester has been deleted successfully! </h4><br>"; 
        }
    }
    else
    {
        echo "<h4 style='color: red'> Invalid Selection </h4><br>";
    }
    ?>
    <a class="btn btn-default" href="./view_semester.php"> Go Back </a>
    <?php
    echo $OUTPUT->footer();
    ?>
