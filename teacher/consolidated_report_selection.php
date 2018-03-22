<script src="../script/jquery/jquery-3.2.1.js"></script>

<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("My Activities");
    $PAGE->set_heading("Activities");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/consolidated_report_selection.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    if(isset($_POST['view_consolidated']) && isset( $_POST['courseid']))
    {	
        $course_id=$_POST['courseid'];
        
        // Dispaly all quizzes
        $recQ=$DB->get_records_sql('SELECT * FROM  `mdl_quiz` WHERE course = ? AND id IN (SELECT quiz FROM `mdl_quiz_attempts`)', array($course_id));
        $recA=$DB->get_records_sql('SELECT * FROM `mdl_assign` WHERE course = ? AND id IN (SELECT assignment FROM `mdl_assign_grades`)', array($course_id));
        $statusQuery=$DB->get_records_sql('SELECT id, instance ,module FROM  `mdl_consolidated_report` WHERE course = ? ', array($course_id));

        $statusArray = array();
        $modArray = array();

        foreach ($statusQuery as $state) {
           
            $sta = $state->instance;
            $mod = $state ->module;
           
            array_push($statusArray, $sta);
            array_push($modArray, $mod);
        }
                
        if($recQ || $recA){
            ?>
            <form method='post' action='consolidated_report.php' id="form_check">
            <?php
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Name', 'Intro', "<input type=\"checkbox\" id=\"select_all\" onChange=\"selectAll(this,'chkbox')\"> Select All",'Status');
            foreach ($recQ as $records) {
                $serialno++;
                $Status='<span style="color: red;">PENDING</span>';
                $id = $records->id;

                for ($i=0; $i< sizeof($statusArray); $i++ )
                {

                      if($id == $statusArray[$i] && $modArray[$i] == 16)
                        {
                            $Status='<span style="color: #006400;">VIEWED</span>';
                            $id = 'Q'.$records->id;
                            $courseid = $records->course;
                            $name = $records->name;
                            $intro = $records->intro;
                            
                            $table->data[] = array($serialno, $name, $intro, '<input type="checkbox" value="'.$id.'" name="activityid[]" id="'.$id.'" class="chkbox">',$Status);
                            break;
                        }
                }
            }
            foreach ($recA as $records) {
                $serialno++;
                $Status='<span style="color: red;">PENDING</span>';
                $id = $records->id;

                for ($i=0; $i< sizeof($statusArray); $i++ )
                {

                      if($id == $statusArray[$i] && $modArray[$i] == 1)
                        {
                            $Status='<span style="color: #006400;">VIEWED</span>';
                            $id = 'A'.$records->id;
                            $courseid = $records->course;
                            $name = $records->name;
                            $intro = $records->intro;
                            $table->data[] = array($serialno, $name, $intro, '<input type="checkbox" value="'.$id.'" name="activityid[]" id="'.$id.'" class="chkbox">',$Status);
                            break;
                        }
                }
                
            }
			
            echo html_writer::table($table);
            ?>
            <br />
            <input type="hidden" value='<?php echo $course_id; ?>' name="courseid">
			<input type="submit" value="Generate Report" name="view_consolidated" class="btn btn-primary">
            </form>
            <br />
            <p id="msg"></p>

            <script>
            function selectAll(master,group){
                var cbarray = document.getElementsByClassName(group);
                for(var i = 0; i < cbarray.length; i++){
                    var cb = document.getElementById(cbarray[i].id);
                    cb.checked = master.checked;
                }
            }
            </script>

            <script>
            $('#form_check').on('submit', function (e) {
                if ($("input[type=checkbox]:checked").length === 0) {
                    e.preventDefault();
                    $("#msg").html("<font color='red'>Select at least one activity!</font>");
                    return false;
                }
            });
            </script>

            <?php
            echo $OUTPUT->footer();
        }
        else{
            echo "<h3>No quizzes found!</h3>";
            echo $OUTPUT->footer();
        }

    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./display_courses-3.php">Back</a>
    <?php
        echo $OUTPUT->footer();
    }?>
