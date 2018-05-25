<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Edit Assignment Marks");
    $PAGE->set_heading("Edit Assignment Marks");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/edit_assignment_marks.php');
        
	require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

?>
<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/validation/jquery.validate.js"></script>
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
    label.error {
        color: red;
    }
    input:invalid {
        border: 1px solid red;
    }
    input:valid {
        border: 1px solid grey;
    }
</style>
<?php
    if(!empty($_GET['edit']) && !empty($_GET['userid']) && !empty($_GET['assignid']))
    {
        $id=$_GET['edit'];
        $userid=$_GET['userid'];
        $assign_id=$_GET['assignid'];
        // echo $assign_id;

        $recofmaxmark=$DB->get_records_sql('SELECT maxmark FROM mdl_manual_assign_pro WHERE id = ?',array($assign_id));
        
        if($recofmaxmark){
            foreach ($recofmaxmark as $records){
                $maxmark=$records->maxmark;
            }

            //echo $maxmark;
            //echo $recofmaxmark;

            if(isset($_POST['save'])) {
                $newmarks=$_POST['newmarks'];

                if($newmarks > $maxmark){
                    echo "<font color='red'><b>Obtained Marks cannot be greater than Maxmarks!</b></font><br />";
                }
                else{
                    $sql_update="UPDATE mdl_manual_assign_pro_attempt SET obtmark=? WHERE id=?";
                    //echo $id;
                    $DB->execute($sql_update, array($newmarks, $id));
                    $msg = "<font color='green'><b>Marks successfully updated!</b></font><br />";
                    $redirect = "view_assignment.php?assignid=$assign_id";
                    redirect($redirect);
                }
            }
            if(isset($msg)){
                echo $msg;
                //goto label;
            }
        }
    }
?>

    <h3>Edit Assignment Marks For <?php echo $userid; ?></h3>
    <form method='post' action="" class="mform" id="fwForm">
        <div class="form-group row fitem">
            <div class="col-md-3">
                <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                </span>
                <label class="col-form-label d-inline" for="id_idnumber">
                 New Marks
                </label>
            </div>
            <div class="col-md-9 form-inline felement" data-fieldtype="text">
                <input type="number"
                        class="form-control "
                        name="newmarks"
                        id="id_obtmark"
                        size=""
                        required
                        placeholder="eg 10"
                        step="0.001"
                        min="0" max="<?php echo $maxmark; ?>">
                <div class="form-control-feedback" id="id_error_idnumber"> <?php echo "Maxmarks: $maxmark"; ?>
                </div>
            </div>
        </div>
        <input class="btn btn-info" type="submit" name="save" value="Save"/>
    </form>

    <script>
        //form validation
        $(document).ready(function () {
            $('#fwForm').validate({ // initialize the plugin
                rules: {
                    "newmarks": {
                        required: true,
                    }
                },
                messages: {
                    "newmarks": {
                        required: "Please enter new marks!."
                    }
                }

            });
        });
    </script>


<?php
    if(isset($_GET['edit'])){
       $id=$_GET['edit'];
        $rec=$DB->get_records_sql('SELECT obtmark FROM mdl_manual_assign_pro_attempt WHERE id=?',array($id));

        if($rec){
            foreach ($rec as $records){
                $obtmark=$records->obtmark;
            }
        }
?>

<script>
        document.getElementById("id_obtmark").value = <?php echo json_encode($obtmark); ?>;
</script>
<?php
    echo "<a href='view_assignment.php?assignid=$assign_id' > Back </a>";
}
echo $OUTPUT->footer();
 ?>
