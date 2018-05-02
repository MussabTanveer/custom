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

         $sql =$DB->get_records_sql('SELECT * FROM mdl_manual_quiz WHERE id = ?',array($Id));
        if($sql)
        {
            foreach ($sql as $rec) 
            {
                $name = $rec->name;
                $desc = strip_tags($rec->description);

            }

        }


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

            <input class="btn btn-info" type="submit" name="save" value="Save"/>
            


        </form>


<?php


    }

    ?>
<script type="text/javascript">
    //alert("Im working");
    document.getElementById("id_description").value = <?php echo json_encode("$desc"); ?>;
    document.getElementById("id_name").value = <?php echo json_encode("$name"); ?>;

</script>

<?php
  if(isset($_POST['save']))
    {
        $n = $_POST['name'];
        $description = $_POST['description'];
        //echo $newObtMark;
        $sql ="UPDATE mdl_manual_quiz SET name = '$n' WHERE id = $Id";
        $DB->execute($sql);
        
         $sql ="UPDATE mdl_manual_quiz SET description = '$description' WHERE id = $Id";
      $DB->execute($sql);

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