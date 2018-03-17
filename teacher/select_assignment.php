<script src="../script/jquery/jquery-3.2.1.js"></script>

<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Select Assignment");
    $PAGE->set_heading("Select Assignment");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/display_quiz_grid.php');


   

      require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

if(isset($_GET['type']) && isset( $_GET['course']))
    {
$course_id=$_GET['course'];
 $coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());



        $rec=$DB->get_records_sql('SELECT * FROM  `mdl_manual_assign_pro` WHERE courseid = ? AND module= ? AND id IN (SELECT assignproid FROM `mdl_manual_assign_pro_attempt`)', array($course_id,'-4'));

if($rec){
            ?>


            <form method='post' action='view_assignment.php' id="form_check">

                <?php
              $serialno = 0;
            $table = new html_table();
     $table->head = array('S. No.', 'Name', 'Description', 'Select');



     foreach ($rec as $records) {
                $serialno++;
                $id = $records->id;
                $courseid = $records->courseid;
                $name = $records->name;
                $description = $records->description;

$table->data[] = array($serialno, $name, $description, '<input type="radio" value="'.$id.'" name="assignid" >');

}

echo html_writer::table($table);
?>

<input type="submit" value="NEXT" name="submit" class="btn btn-primary">
            </form>
            <br />
            <p id="msg"></p>



            <script>
            $('#form_check').on('submit', function (e) {
                if ($("input[type=radio]:checked").length === 0) {
                    e.preventDefault();
                    $("#msg").html("<font color='red'>Select any one assignment!</font>");
                    return false;
                }
            });
            </script>

<?php            
}


 else{
            echo "<h3>No Assignments found!</h3>";
            echo $OUTPUT->footer();
        }


}
 else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./teacher_courses.php">Back</a>
    <?php
        echo $OUTPUT->footer();
    }?>

<?php
echo $OUTPUT->footer();?>