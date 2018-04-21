<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Delete Rubric");
    $PAGE->set_heading("Delete a Rubric");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/delete_rubric.php');
    
    echo $OUTPUT->header();
	require_login();
	$rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());


    if(isset($_GET['id']))
    {
        
        $rubricId = $_GET['id'];
        $sql = "DELETE FROM mdl_rubric WHERE id = $rubricId";
        $DB->execute($sql);

        $sql = "DELETE FROM mdl_rubric_criterion WHERE rubric = $rubricId";
        $DB->execute($sql);

        $sql = "DELETE FROM mdl_rubric_scale WHERE  rubric = $rubricId";
        $DB->execute($sql);

        echo "<font color = green> Rubric has been Deleted Successfully </font><br>"; 
     
        ?>

         <a href="./select_rubric.php" > Go Back </a>
<?php


    }
    else
    {
        echo "<font color = red> Error </font><br>";
    ?>
        <a href="./select_rubric.php" > Go Back </a>
    <?php
    }




 echo $OUTPUT->footer();
