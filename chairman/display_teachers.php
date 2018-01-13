<script src="../script/jquery/jquery-3.2.1.js"></script>

<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Teachers");
    $PAGE->set_heading("Select Teacher");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/display_techers.php');
    
    echo $OUTPUT->header();
    require_login();

    //query to show all teachers
    $rec=$DB->get_records_sql('SELECT distinct u.id, CONCAT( u.firstname, " ", u.lastname ) AS teacher FROM mdl_course as c, mdl_role_assignments AS ra, mdl_user AS u, mdl_context AS ct WHERE c.id = ct.instanceid AND ra.roleid =3 AND ra.userid = u.id AND ct.id = ra.contextid');
    
    $serialno=0;

    if($rec)//executing query to display all teachers
    {
        foreach ($rec as $records)
        {
            $teacherName=$records->teacher;
            $tid=$records->id;
            $serialno++;
            echo "<h4>$serialno. <a href='display_courses-3.php?tid=$tid'>$teacherName</a></h4>";
            echo "<br>";
        }
    }
    echo $OUTPUT->footer();
?>
