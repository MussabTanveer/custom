<script src="../script/jquery/jquery-3.2.1.js"></script>

<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("My Courses");
    $PAGE->set_heading("Courses");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/display_courses.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    // Dispaly all courses
    //$rec=$DB->get_records_sql('SELECT * FROM  `mdl_course` WHERE startdate != ? AND visible = ?', array( 0 , 1 ));
    $rec=$DB->get_records_sql('SELECT c.id, c.fullname, c.shortname, c.idnumber
    
        FROM mdl_course c
    
        INNER JOIN mdl_context cx ON c.id = cx.instanceid
    
        AND cx.contextlevel = ?
    
        INNER JOIN mdl_role_assignments ra ON cx.id = ra.contextid
    
        INNER JOIN mdl_role r ON ra.roleid = r.id
    
        INNER JOIN mdl_user usr ON ra.userid = usr.id
    
        WHERE r.shortname = ?
    
        AND usr.id = ?', array('50', 'editingteacher', $USER->id));
    if($rec){
        ?>
        <form method="post" action="display_quizzes.php" id="form_check">
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
            $SESSION->cid1 = $id;
        
            redirect('display_quizzes.php');
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
