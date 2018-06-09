<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Edit Activity");
    $PAGE->set_heading("Edit an Activity");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/edit_manual_activity3.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
?>
<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/validation/jquery.validate.js"></script>
<script src="../script/validation/additional-methods.min.js"></script>
<style>
    input[type='number'] {
        -moz-appearance:textfield;
    }
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
    }
    label.error {
        color: red;
    }
</style>
<link rel="stylesheet" href="../css/datepicker/wbn-datepicker.css">
<?php

if(isset($_GET['id']) && isset($_GET['course']))
    {
        
        $Id = $_GET['id'];
        $courseId = $_GET['course'];
        $type = $_GET['type'];


        //Get course clo with its level, plo and peo
        $courseclos=$DB->get_records_sql(
        "SELECT clo.id AS cloid, clo.shortname AS cloname, plo.shortname AS ploname, peo.shortname AS peoname, levels.name AS lname, levels.level AS lvl
    
        FROM mdl_competency_coursecomp cc, mdl_competency clo, mdl_competency plo, mdl_competency peo, mdl_taxonomy_levels levels, mdl_taxonomy_clo_level clolevel

        WHERE cc.courseid = ? AND cc.competencyid=clo.id  AND peo.id=plo.parentid AND plo.id=clo.parentid AND 
        clo.id=clolevel.cloid AND levels.id=clolevel.levelid",
        
        array($courseId));
        
        $clonames = array(); $closid = array(); $plos = array(); $peos = array(); $levels = array(); $lvlno = array();
        foreach ($courseclos as $recC) {
            $cid = $recC->cloid;
            $clo = $recC->cloname;
            $plo = $recC->ploname;
            $peo = $recC->peoname;
            $lname = $recC->lname;
            $lvl = $recC->lvl;
            array_push($closid, $cid); // array of clo ids
            array_push($clonames, $clo); // array of clo names
            array_push($plos, $plo); // array of plos
            array_push($peos, $peo); // array of peos
            array_push($levels, $lname); // array of levels
            array_push($lvlno, $lvl); // array of level nos
        }





        $sql =$DB->get_records_sql('SELECT * FROM mdl_manual_other WHERE id = ?',array($Id));
        if($sql)
        {
            foreach ($sql as $rec) 
            {
                $name = $rec->name;
                $desc = strip_tags($rec->description);
                $maxmark = $rec->maxmark;
                $cloid = $rec->cloid;

            }

        }
?>
        <form method='post' action="" class="mform" id="assproForm" enctype="multipart/form-data">
            <h3>Other</h3>
            <div class="form-group row fitem ">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                    </span>
                    <label class="col-form-label d-inline" for="id_name">
                        Name
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
                            class="form-control"
                            name="name"
                            id="id_name"
                            size=""
                            required
                            maxlength="100">
                    <div class="form-control-feedback" id="id_error_name">
                    </div>
                </div>
            </div>
          
            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                    </span>
                    <label class="col-form-label d-inline" for="id_description">
                        Description
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="editor">
                    <div>
                        <div>
                            <textarea id="id_description" name="description" class="form-control" rows="4" cols="80" spellcheck="true" maxlength="500"></textarea>
                        </div>
                    </div>
                    <div class="form-control-feedback" id="id_error_description"  style="display: none;">
                    </div>
                </div>
            </div>
           
            

            <div class="form-group row fitem ">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                    </span>
                    <label class="col-form-label d-inline" for="id_maxmark">
                        Max Marks
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="number">
                    <input type="number"
                            class="form-control"
                            name="maxmark"
                            id="id_maxmark"
                            maxlength="10"
                            size=""
                            required
                            step="0.001"
                            min="0" max="100">
                    <div class="form-control-feedback" id="id_error_maxmark">
                    </div>
                </div>
            </div>

                <div class="form-group row fitem ">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                    </span>
                    <label class="col-form-label d-inline" for="id_clo">
                        CLO
                    </label>
                </div>
                <div class="col-md-9 form-inline felement">
                    <select required onChange="dropdownTip(this.value, 0)" name="clo" class="select custom-select" id="selectclo">
                        <option value=''>Choose..</option>
                        <?php
                        foreach ($courseclos as $recC) {
                        $cid =  $recC->cloid;
                        $cname = $recC->cloname;
                        $plname = $recC->ploname;
                        $pename = $recC->peoname;
                        ?>
                        <option value='<?php echo $cid; ?>'><?php echo $cname; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <span id="plo0"></span>
                    <span id="tax0"></span>
                    <div class="form-control-feedback" id="id_error_clo">
                    </div>
                </div>
            </div>

            


            <button class="btn btn-info" type="submit"  name="save" id="button" /> Save </button>
            <a class="btn btn-default" href="./view_other1.php?type=other&course=<?php echo $courseId ?>">Cancel</a>
            </form>
        <?php 
    }

    ?>


    <script>
        var closid = <?php echo json_encode($closid); ?>;
        var plos = <?php echo json_encode($plos); ?>;
        //var peos = <?php echo json_encode($peos); ?>;
        var levels = <?php echo json_encode($levels); ?>;
        var levelnos = <?php echo json_encode($lvlno); ?>;

        function dropdownTip(value,id){
            var plo = "plo" + id;
            //var peo = "peo" + id;
            var tax = "tax" + id;
            if(value == 'NULL'){
                document.getElementById(plo).innerHTML = "";
                //document.getElementById(peo).innerHTML = "";
                document.getElementById(tax).innerHTML = "";
            }
            else{
                for(var i=0; i<closid.length ; i++){
                    if(closid[i] == value){
                        document.getElementById(plo).innerHTML = "PLO: " + plos[i];
                        //document.getElementById(peo).innerHTML = peos[i];
                        document.getElementById(tax).innerHTML = "LEVEL: " + levels[i] + " (" + levelnos[i] + ")";
                        break;
                    }
                }
            }
        }
        
    </script>

    <script type="text/javascript">
        //alert("Im working");
        document.getElementById("id_description").value = <?php echo json_encode("$desc"); ?>;
        document.getElementById("id_name").value = <?php echo json_encode("$name"); ?>;
        document.getElementById("id_maxmark").value = <?php echo json_encode("$maxmark"); ?>;
        document.getElementById("selectclo").value = <?php echo json_encode("$cloid"); ?>;
    </script>

  

        <script>
            //form validation
            $(document).ready(function () {
                $('#assproForm').validate({ // initialize the plugin
                    rules: {
                        "name": {
                            required: true,
                            minlength: 1,
                            maxlength: 100
                        },
                        "description": {
                            maxlength: 500
                        },
                        "maxmark": {
                            number: true,
                            required: true,
                            step: 0.001,
                            range: [0, 100],
                            min: 0,
                            max: 100,
                            minlength: 1,
                            maxlength: 7
                        },
                        "clo": {
                            required: true
                        }
                    },
                    messages: {
                        "name": {
                            required: "Please enter name.",
                            minlength: "Please enter more than 1 characters.",
                            maxlength: "Please enter no more than 100 characters."
                        },
                        "description": {
                            maxlength: "Please enter no more than 500 characters."
                        },
                        "maxmark": {
                            number: "Only numeric values are allowed.",
                            required: "Please enter max marks.",
                            step: "Please enter nearest max marks value.",
                            range: "Please enter max marks between 0 and 100%.",
                            min: "Please enter max marks greater than or equal to 0.",
                            max: "Please enter max marks less than or equal to 100.",
                            minlength: "Please enter more than 1 numbers.",
                            maxlength: "Please enter no more than 6 numbers (including decimal part)."
                        },
                        "clo": {
                            required: "Please select CLO."
                        }
                    }
                });
            });
        </script>


    <?php

    $redirect = "view_other1.php?type=$type&course=$courseId";
    if(isset($_POST['save']))
    {
        $n = trim($_POST['name']);
        $description = trim($_POST['description']);
        $maxmark = trim($_POST['maxmark']);
        $acloid = trim($_POST['clo']);
        
   

       // echo "$name<br>$description<br>$maxmark<br>$acloid<br>$startdate<br>$enddate";

        $sql ="UPDATE mdl_manual_other SET name = '$n',  description = '$description' ,
        maxmark = '$maxmark', cloid = '$acloid' WHERE id = $Id";
        
        $DB->execute($sql);
        redirect($redirect);
    }

    echo $OUTPUT->footer();
 
    ?>
    