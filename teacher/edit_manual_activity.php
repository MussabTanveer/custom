 <script src="../script/jquery/jquery-3.2.1.js"></script>
<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Edit Activity");
    $PAGE->set_heading("Edit an Activity");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/edit_manual_activity.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();


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

        //var_dump($closid);


         $sql =$DB->get_records_sql('SELECT * FROM mdl_manual_quiz WHERE id = ?',array($Id));
        if($sql)
        {
            foreach ($sql as $rec) 
            {
                $name = $rec->name;
                $desc = strip_tags($rec->description);

            }

        }

        $quesNamesArray = array();
        $quesTextArray = array();
        $maxmarkArray = array();
        $cloidArray = array();
        $quesIdsArray = array ();

        $sql =$DB->get_records_sql('SELECT * FROM mdl_manual_quiz_question WHERE mquizid = ?',array($Id));
        if($sql)
        {
            foreach ($sql as $rec) 
            {
                $quesName = $rec->quesname;
                $quesText = $rec->questext;
                $maxmark = $rec->maxmark;
                $cloid = $rec->cloid;
                $quesId = $rec->id;

                array_push($quesNamesArray, $quesName);
                array_push($quesTextArray, $quesText);
                array_push($maxmarkArray, $maxmark);
                array_push($cloidArray,$cloid);
                array_push($quesIdsArray, $quesId);


            }

        }
       /* var_dump($quesNamesArray);
        echo "<br/>";
        var_dump($quesTextArray);
        echo "<br/>";
        var_dump($maxmarkArray);
        echo "<br/>";
        var_dump($cloidArray);*/


      /*  $sql =$DB->get_records_sql('SELECT maxmark FROM mdl_manual_quiz_question WHERE id = ?',array($qId));
        if($sql)
        {
            foreach ($sql as $rec) 
            {
                $maxmark = $rec->maxmark;

            }

        }*/
        $temp = array();
        $editor = \editors_get_preferred_editor();
        $editor->use_editor("id_description",$temp);
        ?>
        <form method='post' action="" class="mform" id="quizForm" enctype="multipart/form-data">
<?php
             if($type == "quiz"){
                ?>
                <h3>Quiz</h3>
                <?php
            }
            elseif($type == "midterm"){
                ?>
                <h3>Midterm</h3>
                <?php
            }
            elseif($type == "finalexam"){
                ?>
                <h3>Final Exam</h3>
                <?php
            }
            
            ?>

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
                            <textarea id="id_description" name="description" class="form-control" rows="4" cols="80" spellcheck="true" maxlength="100"></textarea>
                        </div>
                    </div>
                    <div class="form-control-feedback" id="id_error_description"  style="display: none;">
                    </div>
                </div>
            </div>

            <?php
            $index = 0;
            for ($i = 0 ; $i<count($quesTextArray); $i++)
            { 
                ?>

                <h3 style="margin-top: 40px">Map Question to CLO</h3>
                <div class="form-group row fitem ">
                        <div class="col-md-3">
                            <span class="pull-xs-right text-nowrap">
                                <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                            </span>
                            <label class="col-form-label d-inline" for="id_quesname0">
                                Name
                            </label>
                        </div>
                        <div class="col-md-5 form-inline felement" data-fieldtype="text">
                            <input type="text"
                                    class="form-control"
                                    name="quesname[]"
                                    id="id_quesname<?php echo $i; ?>"
                                    size=""
                                    required
                                    maxlength="100">
                            <div class="form-control-feedback" id="id_error_quesname">
                            </div>
                        </div>
                        <!--<div class="col-md-4">
                            <i id="cross0" class="fa fa-times" style="font-size:28px;color:red;cursor:pointer" title="Remove"></i>
                        </div> -->
                    </div>


                    <div class="form-group row fitem">
                        <div class="col-md-3">
                            <span class="pull-xs-right text-nowrap">
                                
                            </span>
                            <label class="col-form-label d-inline" for="id_ques_text0">
                                Text
                            </label>
                        </div>
                        <div class="col-md-9 form-inline felement" data-fieldtype="editor">
                            <div>
                                <div>
                                    <textarea id="id_ques_text<?php echo $i; ?>" name="ques_text[]" class="form-control" rows="4" cols="80" spellcheck="true" maxlength="800"></textarea>
                                </div>
                            </div>
                            <div class="form-control-feedback" id="id_error_ques_text"  style="display: none;">
                            </div>
                        </div>
                    </div>


                        <div class="form-group row fitem ">
                        <div class="col-md-3">
                            <span class="pull-xs-right text-nowrap">
                                <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                            </span>
                            <label class="col-form-label d-inline" for="id_maxmark0">
                                Max Marks
                            </label>
                        </div>
                        <div class="col-md-9 form-inline felement" data-fieldtype="number">
                            <input type="number"
                                    class="form-control"
                                    name="maxmark[]"
                                    id="id_maxmark<?php echo $i; ?>"
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
                            <select  onChange="dropdownTip(this.value, <?php echo $i; ?>)" name="clo[]" class="select custom-select" id="clo<?php echo $i; ?>">
                                <option value=''>Choose..</option>
                                <?php

                                foreach ($courseclos as $recC) {
                                $cid =  $recC->cloid;
                                $cname = $recC->cloname;
                                $plname = $recC->ploname;
                                $pename = $recC->peoname;
                               
                                if ($cid == $cloidArray[$index])
                                {
                                        ?>
                                        <option selected value='<?php echo $cid; ?>'><?php echo $cname; ?>
                                            
                                        </option>
                                    <?php
                                }
                                else
                                {?>
                                    <option value='<?php echo $cid; ?>'><?php echo $cname; ?>
                                            
                                        </option>
                                <?php
                                }
                                }
                                $index++;
                                ?>
                            </select>
                            <span id="plo<?php echo $i; ?>"></span>
                            <span id="tax<?php echo $i; ?>"></span>
                            <div class="form-control-feedback" id="id_error_clo">
                            </div>
                        </div>
                    </div>
                <?php
            } ?>



            <input class="btn btn-info" type="submit" name="save" value="Save"/>
            


        </form>


<?php


    }

    ?>
<script>
   
    $(document).ready(function () {

       // alert("It Works");
        var quesTextArray = <?php echo json_encode($quesTextArray); ?>;
        var quesNamesArray = <?php echo json_encode($quesNamesArray); ?>;
        var maxmarkArray  = <?php echo json_encode($maxmarkArray); ?>;
               
        console.log(quesTextArray);
        console.log(quesNamesArray);
        console.log(maxmarkArray);
                
        for(var i=0 ; i<quesTextArray.length; i++)
        {
            $("#id_quesname"+i).val(quesNamesArray[i]);
            $("#id_ques_text"+i).val(quesTextArray[i]);
            $("#id_maxmark"+i).val(maxmarkArray[i]);          

        }

    });
        
</script>

<script type="text/javascript">
    
   // alert("Im working");

    document.getElementById("id_description").value = <?php echo json_encode("$desc"); ?>;
    document.getElementById("id_name").value = <?php echo json_encode("$name"); ?>;

    
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



<?php
  if(isset($_POST['save']))
    {
        $n = $_POST['name'];
        $description = $_POST['description'];
        //echo $newObtMark;

        $sql_update="UPDATE mdl_manual_quiz SET name =?, description = ? WHERE id=?";
        $DB->execute($sql_update, array($n, $description, $Id));

        $quesNames = $_POST['quesname'];
        $quesTexts = $_POST['ques_text'];
        $maxMarks = $_POST['maxmark'];
        $clos = $_POST['clo'];

        /*var_dump($quesTexts);
        echo "<br/>";
        var_dump($maxMarks);
        echo "<br/>";
        var_dump($quesIdsArray);
        echo "<br/>";
        var_dump($clos);*/
        
        for ($i=0; $i<count($quesNames) ; $i++)
        {
            $sql_update="UPDATE mdl_manual_quiz_question SET quesname =?, questext = ?, maxmark = ?, cloid = ? WHERE id=?";
            $DB->execute($sql_update, array($quesNames[$i], $quesTexts[$i],$maxMarks[$i],  $clos[$i],$quesIdsArray[$i]));
          
        }

        echo "<font color = green> Details Updated Successfully </font>";
        if ($type == "quiz")
            $redirect = "print_quiz_paper.php?type=$type&course=$courseId";
        elseif ($type == "midterm")
            $redirect = "print_mid_paper.php?type=$type&course=$courseId";
        elseif ($type == "finalexam")
             $redirect = "print_final_paper.php?type=$type&course=$courseId";
        
        redirect($redirect);
    }
    echo $OUTPUT->footer();
    ?>