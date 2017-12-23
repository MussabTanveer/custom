<script src="./script/jquery/jquery-3.2.1.js"></script>
<?php
    require_once('../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Select Course");
    $PAGE->set_heading("Select Course");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/admin/select_course.php');
    
    echo $OUTPUT->header();
	require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());
	
	$courses=$DB->get_records_sql('SELECT * FROM `mdl_course` WHERE id != ?', array(1));
    $i=0;
    if($courses){
        ?>
        <form method="post" action="select_framework_to_map_clo_course.php" id="form_check">
        <?php
        $table = new html_table();
        $table->head = array('S.No','Course Name' ,'ID Number','Select');
        foreach ($courses as $records){
            $i++;
            $fullname = $records->fullname;
            $shortname = $records->shortname;
            $idnumber = $records->idnumber;
            $id=$records->id;
            $table->data[] = array( $i,$fullname,$idnumber,'<input type="radio" value="'.$id.'" name="courseid">'); 
            
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
                $("#msg").html("<font color='red'>Please select a Course!</font>");
                return false;
            }
        });
        </script>

        <?php
    }
    else{
        echo "<h3>No courses found!</h3>";
    }
    echo $OUTPUT->footer();
	?>
