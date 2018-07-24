<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("View Samples");
    $PAGE->set_heading("View Samples");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/view_samples.php');
   
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }

    echo $OUTPUT->header();


    if(!empty($_GET['instance']) && !empty($_GET['courseid']) && !empty($_GET['type']))
    {
        $course_id=$_GET['courseid'];
        $coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die($OUTPUT->header().'<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());

        $type = $_GET['type'];
        //echo $type;
        $instance = $_GET['instance'];

        if ($type == "quiz")
        {
            $samples=$DB->get_records_sql('SELECT * FROM mdl_sample_solution 
	 	     WHERE instance = ? AND module = -1 ', array($instance));
            $back="sample_paper_quiz_selection.php?type=quiz&course=$course_id&upload=0";
        }
        elseif ($type == "assign")

        {
            
            $samples=$DB->get_records_sql('SELECT * FROM mdl_sample_solution 
             WHERE instance = ? AND module = -4 ', array($instance));
            $back="sample_paper_assign_selection.php?type=assign&course=$course_id&upload=0";
        }
        elseif ($type == "project")

        {
            $samples=$DB->get_records_sql('SELECT * FROM mdl_sample_solution 
             WHERE instance = ? AND module = -5 ', array($instance));
            $back="sample_paper_pro_selection.php?type=project&course=$course_id&upload=0";
        }
         elseif ($type == "midterm")

        {
            $samples=$DB->get_records_sql('SELECT * FROM mdl_sample_solution 
             WHERE instance = ? AND module = -2 ', array($instance));
            $back="sample_paper_midterm_selection.php?type=project&course=$course_id&upload=0";
        }
         elseif ($type == "finalexam")

        {
            $samples=$DB->get_records_sql('SELECT * FROM mdl_sample_solution 
             WHERE instance = ? AND module = -3 ', array($instance));
            $back="sample_paper_final_selection.php?type=project&course=$course_id&upload=0";
        }

         elseif ($type == "other")

        {
            $samples=$DB->get_records_sql('SELECT * FROM mdl_sample_solution 
             WHERE instance = ? AND module = -6 ', array($instance));
            $back="sample_paper_other_selection.php?type=project&course=$course_id&upload=0";
        }


         if($samples)
        {
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Name','Description','Edit','Delete');
            foreach ($samples as $records) {
                $serialno++;
                $id = $records->id;
                $name = $records->name;
                $desc = $records->description;
                
                $table->data[] = array($serialno,"<a href='./display_samples.php?id=$id'>$name</a>",$desc,"<a href='./edit_sample_paper.php?id=$id&courseid=$course_id&type=$type' title='Edit') ><i class='icon fa fa-pencil text-info' aria-hidden='true' title='Edit' aria-label='Edit'></i></a>","<a href='./delete_sample_paper.php?id=$id&courseid=$course_id&type=$type' title='Delete' onClick=\"return confirm('Are you sure you want to delete this sample paper?')\" ><i class='icon fa fa-trash text-danger' aria-hidden='true' title='Delete' aria-label='Delete'></i></a>");
            
            }
            echo html_writer::table($table);
            echo "<br />";
        }
        else
            echo "<font color=red size=5>No Samples Found!<br/></font>";

?>
        <a class="btn btn-default" href="<?php echo $back; ?>">Go Back</a>

<?php
		echo $OUTPUT->footer();

		}
		else
        {
            echo $OUTPUT->header();
			echo "<font color=red size=5>ERROR!</font>";
            echo $OUTPUT->footer();
        }
		
    

