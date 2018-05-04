<script src="../script/jquery/jquery-3.2.1.js"></script>

<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Select Other");
    $PAGE->set_heading("Select Other");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/select_other.php');


   

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



        $rec=$DB->get_records_sql('SELECT * FROM  `mdl_manual_other` WHERE courseid = ? AND module= ? AND id IN (SELECT otherid FROM `mdl_manual_other_attempt`)', array($course_id,'-6'));

if($rec){
            ?>


           

                <?php
              $serialno = 0;
            $table = new html_table();
     $table->head = array('S. No.', 'Name');



     foreach ($rec as $records) {
                $serialno++;
                $id = $records->id;
                $courseid = $records->courseid;
                $name = $records->name;
                

$table->data[] = array($serialno,"<a href='./view_other2.php?otherid=$id&courseid=$course_id'>$name</a>");

}

echo html_writer::table($table);
?>


            </form>
            <br />
            <p id="msg"></p>



            <script>
            $('#form_check').on('submit', function (e) {
                if ($("input[type=radio]:checked").length === 0) {
                    e.preventDefault();
                    $("#msg").html("<font color='red'>Select any one activity!</font>");
                    return false;
                }
            });
            </script>

<?php            
}


 else{
            echo "<h3>No other activity found!</h3>";
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