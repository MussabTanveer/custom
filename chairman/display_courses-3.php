<script src="../script/jquery/jquery-3.2.1.js"></script>

<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Courses");
    $PAGE->set_heading("Select Course");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/display_courses-3.php');
    
    echo $OUTPUT->header();
    require_login();

    // Dispaly all courses
    //$rec=$DB->get_records_sql('SELECT * FROM  `mdl_course` WHERE startdate != ? AND visible = ?', array( 0 , 1 ));
    $rec=$DB->get_records_sql('SELECT c.id, c.fullname, c.shortname, c.idnumber
    
        FROM mdl_course c, mdl_user us, mdl_role r, mdl_role_assignments ra
    
        WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? AND c.id != ?', array('chairman',$USER->id,1));

    if($rec){
        ?>
        <form method="post" action="display_activities.php" id="form_check">
        <?php
        $serialno = 0;
        $table = new html_table();
        $table->head = array('S. No.','Full Name', 'Short Name' , 'Course Code', 'Select');
        foreach ($rec as $records) {
            $serialno++;
            $id = $records->id;
            $fname = $records->fullname;
            $sname = $records->shortname;
            $idnum = $records->idnumber;
            $table->data[] = array($serialno, $fname, $sname, $idnum, '<input type="radio" value="'.$id.'" name="courseid">');
        }
        if($serialno == 1){
            
            global $SESSION;
            $SESSION->cid3 = $id;
        
            redirect('display_activities.php');
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
                $("#msg").html("<font color='red'>Select any one course!</font>");
                return false;
            }
        });
        </script>
        <?php
        echo $OUTPUT->footer();
    }
    else{
        echo "<h3>No courses found!</h3>";
        echo $OUTPUT->footer();
    }
?>
