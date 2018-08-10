<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Add Batch");
    $PAGE->set_heading("Add Batch");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/itm/add_batch.php');
    
    require_login();
    if($SESSION->oberole != "itm"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <?php

    if(isset($_POST['save']) || isset($_POST['return'])){
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
            
                $record = new stdClass();
                $record->name = $name;
                
                $batchid = $DB->insert_record('batch', $record);
                $transaction->allow_commit();
                $msg2 = "<br><font color='green'><b> Batch created successfully!</b></font>";
                if (isset($_POST['return'])){
                    $redirect_page1='./report_itm.php';
                    redirect($redirect_page1);
                }
                
            } catch(Exception $e) {
                $msg2 = "<br><font color='red'><b> Batch creation failed! Please create batch again. </b></font>";
                $transaction->rollback($e);
                $msg2 = "<br><font color='red'><b> Batch creation failed! Please create batch again. </b></font>";
            }
        }
    }
    if(isset($msg2)){
        echo $msg2;
    }
    ?>
    <br />
    <h3>Add New Batch</h3>
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
        
        <input class="btn btn-info" type="submit" name="save" value="Save and continue"/>
        <input class="btn btn-info" type="submit" name="return" value="Save and return"/>
        <a class="btn btn-default" type="submit" href="./report_itm.php">Cancel</a>

    </form>
    <?php
    if(isset($_POST['save']) && !isset($msg2)){
    ?>
    <script>
        document.getElementById("id_name").value = <?php echo json_encode($name); ?>;
    </script>
    <?php
    }
    ?>
    <br />
    <div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>
    
    <?php
    echo $OUTPUT->footer();
    ?>
