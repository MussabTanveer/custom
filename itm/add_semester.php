<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Add Semester");
    $PAGE->set_heading("Add Semester");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/itm/add_semester.php');
    
    require_login();
    if($SESSION->oberole != "itm"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    ?>
    <link rel="stylesheet" href="../css/datepicker/wbn-datepicker.css">
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <?php

    if(isset($_POST['save']) || isset($_POST['return'])){
        $name=trim($_POST['name']);
        $year=trim($_POST['year']);
        $startdate=strtotime($_POST['startdate']);
        $enddate=strtotime($_POST['enddate']);
        
        if(empty($name) || empty($year))
        {
            if(empty($name))
            {
                $msg1="<font color='red'>-Please select semester</font>";
            }
            if(empty($year))
            {
                $msg2="<font color='red'>-Please enter year</font>";
            }
        }
        else{
            try {
                $transaction = $DB->start_delegated_transaction();
            
                $record = new stdClass();
                $record->name = $name;
                $record->year = $year;
                $record->startdate = $startdate;
                $record->enddate = $enddate;
                
                $semesterid = $DB->insert_record('semester', $record);
                $transaction->allow_commit();
                if (isset($_POST['return'])){
                    $redirect_page1='./report_itm.php';
                    redirect($redirect_page1);
                }
                
            } catch(Exception $e) {
                $msg4 = "<br><font color='red'><b> Semester creation failed! Please create semester again. </b></font>";
                $transaction->rollback($e);
                $msg4 = "<br><font color='red'><b> Semester creation failed! Please create semester again. </b></font>";
            }
        }
    }
    if(isset($msg4)){
        echo $msg4;
    }
    ?>
    <br />
    <h3>Add New Semester</h3>
    <form method='post' action="" class="mform">

        <div class="form-group row fitem">
            <div class="col-md-3">
                <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                </span>
                <label class="col-form-label d-inline" for="id_name">
                    Select Semester
                </label>
            </div>
            <div class="col-md-9 form-inline felement" data-fieldtype="text">
                <select required name="name" class="select custom-select" id="id_name">
                    <option value=''>Select..</option>
                    <option value='Spring'>Spring</option>
                    <option value='Fall'>Fall</option>
                    <option value='Summer'>Summer</option>
                </select>
                <div class="form-control-feedback" id="id_error_name">
                <?php
                if(isset($msg1)){
                    echo $msg1;
                }
                ?>
                </div>
            </div>
        </div>
        
        <div class="form-group row fitem">
            <div class="col-md-3">
                <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                </span>
                <label class="col-form-label d-inline" for="id_year">
                    Semester Year
                </label>
            </div>
            <div class="col-md-9 form-inline felement" data-fieldtype="text">
                <input type="text"
                        class="form-control "
                        name="year"
                        id="id_year"
                        pattern="^[0-9-]+$"
                        title="eg. 17-18"
                        value=""
                        required
                        placeholder="eg. 17-18"
                        size="10"
                        maxlength="10" >
                <div class="form-control-feedback" id="id_error_year">
                <?php
                if(isset($msg2)){
                    echo $msg2;
                }
                ?>
                </div>
            </div>
        </div>

        <div class="form-group row fitem">
            <div class="col-md-3">
                <span class="pull-xs-right text-nowrap">
                <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                </span>
                <label for="id_startdate">
                    Semester Start Date
                </label>
            </div>
            <div class="col-md-9 form-inline felement" data-fieldtype="text">
                <input type="text"
                    required
                    class="form-control wbn-datepicker"
                    name="startdate"
                    id="id_startdate"
                    size="27"
                    maxlength="100" >
                <div class="form-control-feedback" id="id_error_startdate">
                </div>
            </div>
        </div>

        <div class="form-group row fitem">
            <div class="col-md-3">
                <span class="pull-xs-right text-nowrap">
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                </span>
                <label for="id_enddate">
                    Semester End Date
                </label>
            </div>
            <div class="col-md-9 form-inline felement" data-fieldtype="text">
                <input type="text"
                    required
                    class="form-control wbn-datepicker"
                    name="enddate"
                    id="id_enddate"
                    data-start-src="id_startdate"
                    size="27"
                    maxlength="100" >
                <div class="form-control-feedback" id="id_error_enddate">
                </div>
            </div>
        </div>
        
        <input class="btn btn-info" type="submit" name="save" value="Save and continue"/>
        <input class="btn btn-info" type="submit" name="return" value="Save and return"/>
        <a class="btn btn-default" type="submit" href="./report_itm.php">Cancel</a>

    </form>
    <?php
    if(isset($_POST['save']) && !isset($msg4)){
    ?>
    <script>
        document.getElementById("id_name").value = <?php echo json_encode($name); ?>;
        document.getElementById("id_year").value = <?php echo json_encode($year); ?>;
    </script>
    <?php
    }
    ?>
    <br />
    <div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>
    
    <script src="../script/datepicker/wbn-datepicker.min.js"></script>
    <script type="text/javascript">
        $(function () {
        $('.wbn-datepicker').datepicker()
    
        var $jsDatepicker = $('#value-specified-js').datepicker()
        $jsDatepicker.val('2017-05-30')
        })
    </script>
    <?php
    echo $OUTPUT->footer();
    ?>
