<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Edit Result");
    $PAGE->set_heading("Edit Result");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/edit_activity_marks.php');
    
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
    input:invalid {
        border: 1px solid red;
    }
    input:valid {
        border: 1px solid grey;
    }
</style>
<?php

    if(!empty($_GET['quizid']) && !empty($_GET['userId']) && !empty($_GET['qId'])  && !empty($_GET['courseid']))
    {
        $quizId = $_GET['quizid'];
        $userId = $_GET['userId'];
        $qId = $_GET['qId'];
        $courseid = $_GET['courseid'];

        $sql =$DB->get_records_sql('SELECT * FROM mdl_manual_quiz_attempt WHERE quizid = ? AND userid = ? AND questionid = ?',array($quizId,$userId,$qId));
        if($sql)
        {
            foreach ($sql as $rec) 
            {
                $obtmark = $rec->obtmark;
            }

        }

        $sql =$DB->get_records_sql('SELECT maxmark FROM mdl_manual_quiz_question WHERE id = ?',array($qId));
        if($sql)
        {
            foreach ($sql as $rec) 
            {
                $maxmark = $rec->maxmark;
            }

        }
       // echo $maxmark;
        ?>

        <form  method='post' action="" class="mform">
            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                    </span>
                    <label class="col-form-label d-inline" for="Mark">
                        Enter Marks
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="number">
                    <input type="number"
                            class="form-control"
                            name="mark"
                            id="mark"
                            size=""
                            required
                            placeholder="eg 10"
                            step="0.001"
                            min="0" max="<?php echo $maxmark; ?>">
                    <div class="form-control-feedback" id="id_error_scalescore">
                    </div>
                </div>
            </div>
            <input class="btn btn-info" type="submit" name="save" value="Save"/>
        </form>

    <?php
    }
    else
    {
        
    }

    ?>

<script type="text/javascript">
    //alert("Im working");
    document.getElementById("mark").value = <?php echo json_encode("$obtmark"); ?>;
</script>

<?php
    if(isset($_POST['save']))
    {
        $newObtMark = $_POST['mark'];

        if ($newObtMark <= $maxmark)
        {
            $sql ="UPDATE mdl_manual_quiz_attempt SET obtmark = ? WHERE quizid = ? AND userid = ? AND questionid = ?";
            $DB->execute($sql, array($newObtMark, $quizId, $userId, $qId));
            echo "<font color = green> Marks Updated Successfully </font>";
            $redirect = "view_activity_result1.php?quiz=$quizId&courseid=$courseid";
            redirect($redirect);
        }
        else
            echo "<font color='red'><b>Obtained Marks cannot be greater than Maxmarks. </b></font><br />";
    }

    echo $OUTPUT->footer();
?>
