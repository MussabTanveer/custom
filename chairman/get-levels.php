<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Get Levels");
    $PAGE->set_heading("Get Levels");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/admin/get-levels.php');


    require_login();
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());

    if(isset($_POST['d_id'])) {
        $domid = $_POST['d_id'];
        $res=$DB->get_records_sql("SELECT * FROM `mdl_taxonomy_levels` WHERE `domainid`=?", array($domid));
        if(count($res) > 0) {
            echo "<option value=''>Choose..</option>";
            foreach ($res as $row) {
            echo "<option value='".$row->id."'>".substr($row->level,1)."</option>";
            }
        }
    }

?>