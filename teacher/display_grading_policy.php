<script src="../script/sweet-alert/sweetalert.min.js"></script>
<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Display Grading Policy");
    $PAGE->set_heading("Display Grading Policy");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/display_grading_policy.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
     
    if(isset($_GET['course'])){
        $course_id=$_GET['course'];
        //echo $course_id;

        if(isset($_GET['delete'])){
            $id_d=$_GET['delete'];
            $sql_delete="DELETE FROM mdl_grading_policy WHERE id=$id_d";
            $DB->execute($sql_delete);
            ?>
            <script>
            swal("Grading Policy item has been deleted!", {
                    icon: "success",
                    });
            </script>
            <?php
        }

        $rec=$DB->get_records_sql('SELECT id, name, percentage FROM mdl_grading_policy WHERE courseid=?',array($course_id));

        if($rec){
            $serial=0;
            $sum=0;
            $table = new html_table();
            $table->head = array('S. No.', 'Activity', 'Percentage', 'Actions');
            foreach ($rec as $records) {
                $serial++;
                $id=$records->id;
                $name=$records->name;
                $percentage=$records->percentage;
                $sum+=$percentage;
                $table->data[] = array($serial,strtoupper($name), $percentage.'%', "<a href='edit_grading_policy.php?course=$course_id&edit=$id' title='Edit'><img src='../img/icons/edit.png' /></a> <a href='display_grading_policy.php?course=$course_id&delete=$id' onClick=\"return confirm('Delete grading policy of $name?')\" title='Delete'><img src='../img/icons/delete.png' /></a>");
            }
            $table->data[] = array("<b>Total:</b>", "", $sum.'%', "");
            if($serial){
                if($sum != 100)
                    echo "<h5 style='color:red'>Grading Policy is not 100%<h5><a href=grading_policy.php?course=$course_id>Add a grading policy item</a>.<br /><br />";
                echo html_writer::table($table);
            }
        }
        else
            echo "<h5 style='color:red'> <br />Found no Graded Activity of this Course! </h5>";
    }
    else{
        ?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="../index.php">Back</a>
        <?php
    }
    echo $OUTPUT->footer();
?>
