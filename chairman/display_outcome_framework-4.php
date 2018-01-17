<script src="../script/jquery/jquery-3.2.1.js"></script>

<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("OBE Frameworks");
    $PAGE->set_heading("OBE Framework Selection");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/display_outcome_framework-4.php');
    
    echo $OUTPUT->header();
    require_login();
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());

    // Dispaly all frameworks
    $rec=$DB->get_records_sql('SELECT * FROM mdl_competency_framework');
    if($rec){
        ?>
        <form method="post" action="display_plo_domain_clo_level.php" id="form_check">
        <?php
        $serialno = 0;
        $table = new html_table();
        $table->head = array('S. No.','Framework', 'Select');
        foreach ($rec as $records) {
            $serialno++;
            $id = $records->id;
            $sname = $records->shortname;
            $table->data[] = array($serialno, $sname, '<input type="radio" value="'.$id.'" name="fwid">');
        }
        if($serialno == 1){
            
            global $SESSION;
            $SESSION->fid7 = $id;
        
            redirect('display_plo_domain_clo_level.php');
        }
        echo html_writer::table($table);
        ?>
        <input type='submit' value='NEXT' name='submit' class="btn btn-primary">
        </form>
        <br />
        <p id="msg"></p>

        <script>
        $('#form_check').on('submit', function (e) {
            if ($("input[type=radio]:checked").length === 0) {
                e.preventDefault();
                $("#msg").html("<font color='red'>Select any one framework!</font>");
                return false;
            }
        });
        </script>
        <?php
        echo $OUTPUT->footer();
    }
    else{
        echo "<h3>No framework found!</h3>";
        echo $OUTPUT->footer();
    }
?>
