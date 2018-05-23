<?php
	require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Define Parent Activity");
    $PAGE->set_heading("Define Parent Activity");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/define_parent_activity.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();



     if(isset($_GET['save']))
    {
        $name = $_GET['name'];
        $desc = $_GET['description'];
        $course_id = $_GET['course_id'];
        $flag = $_GET['flag'];
        //echo "$flag";
        

        $sql="INSERT INTO mdl_parent_activity (name,description,courseid) VALUES 
                        ('$name','$desc','$course_id')";
        
         if ($DB->execute($sql))
         {
            echo "<font color = green >Parent Activity has been defined successfully.</font>";
         }

         if ($flag == 0)
            $redirect="./map_grading_item.php?course=$course_id";
        else 
            $redirect="./map_manual_activity.php?course=$course_id";

       redirect($redirect);


    }


    $temp = array();
    $editor = \editors_get_preferred_editor();
    $editor->use_editor("id_description",$temp);

    if(isset($_GET['course']))
    {
        $course_id=$_GET['course'];
        $flag = $_GET['flag'];

?>
                    
    <form action="" method="get" class="mform" >

        <h3>Parent Activity</h3>
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

            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
            <input type="hidden" name="flag" value="<?php echo $flag; ?>">

        <button class="btn btn-info" type="submit"  name="save" id="button" /> Save </button>
        <a class="btn btn-default" type="submit" href="./map_grading_item.php?course=<?php echo $course_id ?>">Cancel</a>
        <br /><br />
    </form>

<?php 
    }
    else
        echo "<font color=red size =20px> Error </font>";



?>



<?php
echo $OUTPUT->footer();
?>