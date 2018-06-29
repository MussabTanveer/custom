<?php
    $rec=$DB->get_records_sql('SELECT c.id, c.fullname, c.shortname, c.idnumber
        
        FROM mdl_course c

        INNER JOIN mdl_context cx ON c.id = cx.instanceid

        AND cx.contextlevel = ?

        INNER JOIN mdl_role_assignments ra ON cx.id = ra.contextid

        INNER JOIN mdl_role r ON ra.roleid = r.id

        INNER JOIN mdl_user usr ON ra.userid = usr.id

        WHERE r.shortname = ?

        AND usr.id = ? AND c.semesterid = ?', array('50', 'student', $USER->id, $semester_id));
        
    if($rec){
        ?>
        <form method="post" action="<?php echo $action ?>" id="form_check">
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
            $SESSION->cid4 = $id;
        
            redirect('display_activities_student.php');
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
    }
    else{
        echo "<h3>No courses found!</h3>";
    }
?>