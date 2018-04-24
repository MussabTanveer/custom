<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Delete Rubric Criterion/Scale");
    $PAGE->set_heading("Delete Rubric Criterion/Scale");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/delete_rubric_criterion_scale.php');
    
    echo $OUTPUT->header();
	require_login();
	$rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());

    // Delete Scale
    if(!empty($_GET['rubric']) && !empty($_GET['scale']))
    {
        $rubricId = $_GET['rubric'];
        $scaleId = $_GET['scale'];

        try {
            $transaction = $DB->start_delegated_transaction();

            $sql = "DELETE FROM mdl_rubric_scale WHERE id = ?";
            $DB->execute($sql, array($scaleId));

            $transaction->allow_commit();
        } catch(Exception $e) {
            $transaction->rollback($e);
        }

        echo "<font color = green> Scale has been deleted successfully! </font><br>"; 
     
        ?>
        <a href="./view_rubric.php?rubric=<?php echo $rubricId ?>"> Go Back </a>
    <?php
    }
    // Delete Criterion and its Scales
    elseif(!empty($_GET['rubric']) && !empty($_GET['criterion']))
    {
        $rubricId = $_GET['rubric'];
        $criterionId = $_GET['criterion'];
        
        try {
            $transaction = $DB->start_delegated_transaction();

            $sql = "DELETE FROM mdl_rubric_scale WHERE criterion = ?";
            $DB->execute($sql, array($criterionId));

            $sql = "DELETE FROM mdl_rubric_criterion WHERE id = ?";
            $DB->execute($sql, array($criterionId));

            $transaction->allow_commit();
        } catch(Exception $e) {
            $transaction->rollback($e);
        }

        echo "<font color = green> Criterion has been deleted successfully! </font><br>"; 
     
        ?>
        <a href="./view_rubric.php?rubric=<?php echo $rubricId ?>"> Go Back </a>
    <?php
    }
    // Error
    else
    {
        echo "<font color = red> Error </font><br>";
    ?>
        <a href="./select_rubric.php" > Go Back </a>
    <?php
    }

    echo $OUTPUT->footer();
