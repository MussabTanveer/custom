<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Assign Weightage");
    $PAGE->set_heading("Assign Weightage");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/grading_policy_full.php');
    echo $OUTPUT->header();
	require_login();
if($SESSION->oberole != "chairman"){
        header('Location: ../index.php');
	}

echo "<font color = green>Weightage is already 100% for Courses<br />Cannot add  more.<br />Either <a href=delete_grading_policy.php?>delete</a> or <a href=edit_grading_policy.php?>edit</a> grading policy.</font>";



    ?>



    <?php

echo $OUTPUT->footer();

    ?>