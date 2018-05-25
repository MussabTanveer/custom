<?php 
   require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Online Grading Form");
    $PAGE->set_heading("Online Grading Form");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/grading_form.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
?>
<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/formcache/formcache.min.js"></script>
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
    input:invalid {
        border: 1px solid red;
    }
</style>
<?php
if(!empty($_GET['quiz']) && !empty($_GET['courseid']))
{
    $course_id=$_GET['courseid'];
    $coursecontext = context_course::instance($course_id);
    is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());

    $quizId= $_GET['quiz'];
    $stdids=array();
    $qids = array();
    $quesnames=array();
    $maxmarks=array();
    $ques=$DB->get_records_sql("SELECT * FROM mdl_manual_quiz_question  WHERE mquizid=$quizId");
    $qcount = 0;
    if($ques)
    {
        foreach ($ques as $q) {
            $qname = $q->quesname;
            $id = $q->id;
            $max = $q->maxmark;
            array_push ($quesnames,$qname);
            array_push ($qids,$id);
            array_push ($maxmarks,$max);
            $qcount++;
        }
    }
    
    ?>
    <form method="post" action="insert_result.php" id="myForm">
        <table border='10' cellpadding='8' id ="mytable">
        <tr>
        <th> Seat No. </th>
        <?php
        foreach ($quesnames as $qname){
            ?><th> <?php echo $qname ; ?> </th>
            <?php
        }
        ?>
        </tr>
        <?php
    
        $users=$DB->get_records_sql("SELECT u.id AS sid,substring(u.username,4,8) AS seatorder, u.username AS seatnum, u.firstname, u.lastname
        FROM mdl_role_assignments ra, mdl_user u, mdl_course c, mdl_context cxt
        WHERE ra.userid = u.id
        AND ra.contextid = cxt.id
        AND cxt.contextlevel = 50
        AND cxt.instanceid = c.id
        AND c.id = $course_id
        AND (roleid=5) ORDER BY seatorder");
        
        if($users)
        {
            $indx = 0;
            foreach ($users as $user ) {
            ?>
            <tr>
                <td>
                    <?php echo $user->seatnum; array_push ($stdids,$user->sid); ?>
                </td>
                <?php
                $i=0;
                foreach ($quesnames as $qname){
                ?>
                <td style="background-color: #ECEEEF;">
                    <input type="number" name="marks[<?php echo $indx; ?>]" step="0.001" min="0" max="<?php echo $maxmarks[$i]; ?>" />
                </td>
                <?php
                $i++; $indx++;
                }
                ?>
            </tr>
            <?php
          }
        }
        ?>
        </table>
        <input type="hidden" value='<?php echo $qcount; ?>' name="quescount">
        <input type="hidden" value='<?php echo $quizId; ?>' name="quizid">
        <?php
        foreach($qids as $key => $qid)
        {
            echo '<input type="hidden" name="quesid['.$key.']" value="'.$qid.'">';
        }
        foreach($stdids as $key => $sid)
        {
            echo '<input type="hidden" name="studid['.$key.']" value="'.$sid.'">';
        }
        ?>
        <br />
        <input type="submit" value="Submit Result" name="submit" class="btn btn-primary">
        <button type="reset" value="Reset" class="btn btn-warning">Clear Form</button>
    </form>

    <script>
        //<!-- Cache form data -->
        var value = 'quiz' + <?php echo json_encode($quizId); ?>; // quiz id is the form key
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
