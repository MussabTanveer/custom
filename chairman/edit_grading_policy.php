<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Edit Weightage");
    $PAGE->set_heading("Edit Weightage");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/edit_grading_policy.php');
    echo $OUTPUT->header();
	require_login();
if($SESSION->oberole != "chairman"){
        header('Location: ../index.php');
	}



if(isset($_POST['update'])){




$midterm = trim($_POST["midterm"]);
$final = trim($_POST["final"]);

$sql_update="UPDATE mdl_grading_policy SET percentage=$midterm WHERE name='mid term'";

 $DB->execute($sql_update);
$sql_update1="UPDATE mdl_grading_policy SET percentage=$final WHERE name='final exam'";
 $DB->execute($sql_update1);


$msg="<font color = green>Updated Successfully !</font><br />";

}


if(isset($msg)){


    echo $msg;
}



?>

<form method='post' action="" class="mform" id="cloForm">
     <div class="form-group row fitem ">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                    </span>
                    <label class="col-form-label d-inline" for="id_name">
                        Midterm
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
                            class="form-control"
                            name="midterm"
                            id="id_name"
                            size=""
                            required
                            maxlength="100">
                    <div class="form-control-feedback" id="id_error_name">
                    </div>
                </div>
            </div>

<div class="form-group row fitem ">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                    </span>
                    <label class="col-form-label d-inline" for="id_name">
                        Final
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
                            class="form-control"
                            name="final"
                            id="id_name"
                            size=""
                            required
                            maxlength="100">
                    <div class="form-control-feedback" id="id_error_name">
                    </div>
                </div>
            </div>
        









<input class="btn btn-info" type="submit" name="update" value="Update"/>
        <a class="btn btn-default" type="submit" <?php echo "href='./report_chairman.php'" ?>>Cancel</a>
        </form>


    <?php
echo $OUTPUT->footer();

?>