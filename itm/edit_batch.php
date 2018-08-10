<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Edit Batch");
    $PAGE->set_heading("Edit Batch");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/itm/edit_batch.php');
    
    require_login();
    if($SESSION->oberole != "itm"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <?php

    if(!empty($_GET['batch'])){
        $batch_id = $_GET['batch'];
        if(isset($_POST['save'])){
            $name=trim($_POST['name']);
            
            if(empty($name))
            {
                if(empty($name))
                {
                    $msg1="<font color='red'>-Please enter batch</font>";
                }
            }
            else{
                try {
                    $transaction = $DB->start_delegated_transaction();

                    $sql_update1="UPDATE mdl_batch SET name=? WHERE id=?";
                    $DB->execute($sql_update1, array($name, $batch_id));

                    $transaction->allow_commit();
                    $msg4 = "<br><h4 style='color: green'>Batch information updated!</h4>";
                } catch(Exception $e) {
                    $msg4 = "<br><h4 style='color: red'>Batch information update failed!</h4>";
                    $transaction->rollback($e);
                    $msg4 = "<br><h4 style='color: red'>Batch information update failed!</h4>";
                }
            }
        }

        $batches=$DB->get_records_sql("SELECT * FROM mdl_batch ORDER BY id DESC LIMIT 12");
        if(isset($msg4)){
			echo $msg4;
			goto label;
		}
        ?>
        <br />
        <form method='post' action="" class="mform">
            
            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                    </span>
                    <label class="col-form-label d-inline" for="id_name">
                        Batch
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
                            class="form-control "
                            name="name"
                            id="id_name"
                            pattern="^[0-9-]+$"
                            title="eg. 2014-15"
                            value=""
                            required
                            placeholder="eg. 2014-15"
                            size="10"
                            maxlength="10" >
                    <div class="form-control-feedback" id="id_error_name">
                    <?php
                    if(isset($msg1)){
                        echo $msg1;
                    }
                    ?>
                    </div>
                </div>
            </div>

            <input class="btn btn-info" type="submit" name="save" value="Save"/>
            <a class="btn btn-default" href="./view_batch.php">Cancel</a>

        </form>
    <?php
    if(!empty($_GET['batch']) && !isset($_POST['save'])){
        $batch = $DB->get_records_sql('SELECT * FROM  `mdl_batch` WHERE id = ?', array($batch_id));
        if($batch){
            foreach($batch as $b) {
                $name = $b->name;
            }
        }
    }
    ?>
    <script>
        document.getElementById("id_name").value = <?php echo json_encode($name); ?>;
    </script>
    <br />
        <div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>
    <?php
    }
    else
    {
        echo "<h4 style='color: red'> Invalid Selection </h4><br>";
    }
    label:
    ?>
    <br>
    <a class="btn btn-default" href="./view_batch.php">Go Back</a>
    <?php
    echo $OUTPUT->footer();
    ?>
