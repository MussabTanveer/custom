<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/validation/jquery.validate.js"></script>
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

?>

<style>
    label.error {
        color: red;
    }
</style>
<?php
echo $OUTPUT->header();

if(!empty($_GET['edit']) && !empty($_GET['userid']))
    {
        $id=$_GET['edit'];
        $userid=$_GET['userid'];
         $assign_id=$_GET['assignid'];
        // echo $assign_id;
if(isset($_POST['save']))
        {

         $newmarks=$_POST['newmarks'];

        

$sql_update="UPDATE mdl_manual_assign_pro_attempt SET obtmark='$newmarks' WHERE id='$id'";
//echo $id;
$DB->execute($sql_update);
                $msg = "<font color='green'><b>Marks successfully updated!</b></font><br />";


}
    }
if(isset($msg)){
            echo $msg;
            //goto label;
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
                <input type="text"
                        class="form-control "
                        name="newmarks"
                        id="id_obtmark"
                        size=""
                        required
                        maxlength="20" type="text" >
                <div class="form-control-feedback" id="id_error_idnumber">
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
                        minlength: 1,
                        maxlength: 20
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
}
echo $OUTPUT->footer();
 
    ?>