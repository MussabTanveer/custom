<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/formcache/formcache.min.js"></script>
<?php 
   require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Online Grading Form");
    $PAGE->set_heading("Online Grading Form");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/grading_form_other.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
?>
<style>
	input[type='number'] {
		-moz-appearance:textfield;
        max-width: 50px;
        border: none;
	}
    input[type='number']:focus {
        outline: none;
        border: none;
	}
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
	}
</style>
<?php
if(!empty($_GET['other']) && !empty($_GET['courseid']))
{
    $course_id=$_GET['courseid'];
    $coursecontext = context_course::instance($course_id);
    is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());

    $other= $_GET['other'];
    $stdids=array();
    $maxmarks=0;
    $otherDetail=$DB->get_records_sql("SELECT * FROM mdl_manual_other WHERE id=$other");

    if($otherDetail)
    {
        foreach ($otherDetail as $ot) {
            $maxmarks = $ot->maxmark;
        }
    }
    
    ?>
    <form method="post" action="insert_result_other.php" id="myForm">
        <table border='10' cellpadding='8' id ="mytable">
        <tr>
            <th> Seat No. </th>
            <th> Marks </th>
        </tr>
        <?php
        $users=$DB->get_records_sql("SELECT u.id AS sid,substring(u.username,4,8) AS seatorder ,u.username AS seatnum, u.firstname, u.lastname
        FROM mdl_role_assignments ra, mdl_user u, mdl_course c, mdl_context cxt
        WHERE ra.userid = u.id
        AND ra.contextid = cxt.id
        AND cxt.contextlevel = 50
        AND cxt.instanceid = c.id
        AND c.id = $course_id
        AND (roleid=5) ORDER BY seatorder");
        
        if($users)
        {
            foreach ($users as $user ) {
            ?>
            <tr>
                <td>
                    <?php echo $user->seatnum; array_push ($stdids,$user->sid); ?>
                </td>
                <td style="background-color: #ECEEEF;">
                    <input type="number" name="marks[]" step="0.001" min="0" max="<?php echo $maxmarks; ?>" required />
                </td >
            </tr>
            <?php
            }
        }
        ?>
        </table>
        <input type="hidden" value='<?php echo $other; ?>' name="other_id">
        <?php
        foreach($stdids as $sid)
        {
        echo '<input type="hidden" name="studid[]" value="'. $sid. '">';
        }
        ?>
        <br />
        <input type="submit" value="Submit Result" name="submit" class="btn btn-primary">
    </form>

    <script>
        //<!-- Cache form data -->
        var value = 'other' + <?php echo json_encode($other); ?>; // other id is the form key
        //alert(value);
        $("#myForm").formcache({key:value});
    </script>
    
    <?php
    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./teacher_courses.php">Back</a>
    <?php
    }
    echo $OUTPUT->footer();
    ?>
