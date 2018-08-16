<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Teacher Reports & Forms");
    $PAGE->set_heading("Teacher Reports & Forms");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/report_teacher.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();

    if(!empty($_GET['course']))
    {
        $course_id=$_GET['course'];
        $coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
        
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/cool-link/style.css" />

	<div>
        <!--<a <?php echo "href='./view_course_profile.php?course=$course_id'" ?> class="cool-link">View Course Profile</a><br><br>--> <!--For PDF Course Profile View-->

        <a <?php echo "href='./view_course_profileform.php?course=$course_id'" ?> class="cool-link">&#10070; View Course Profile</a><br><br>

        <a href="javascript:void(0)" id="gp_click" onclick="toggle_visibility('gp_click', 'gp');" class="cool-link"><span class="fa fa-chevron-down"></span> Grading Policy</a><br><br>
        <div id="gp" style="display: none">
            &nbsp;&nbsp;&nbsp;<a <?php echo "href='./grading_policy.php?course=$course_id'" ?>  class="cool-link">&#10070; Define Grading Policy</a><br>
            &nbsp;&nbsp;&nbsp;<a <?php echo "href='./display_grading_policy.php?course=$course_id'" ?>  class="cool-link">&#10070; Display Grading Policy</a><br><br>
        </div>

        <a href="javascript:void(0)" id="tools_click" onclick="toggle_visibility('tools_click', 'tools');" class="cool-link"><span class="fa fa-chevron-down"></span> Course Evaluation Tools &amp; Mappings</a><br><br>
        <div id="tools" style="display: none">
            &nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="manual_click" onclick="toggle_visibility('manual_click', 'manual');" class="cool-link"><span class="fa fa-chevron-down"></span>  Manual Evaluation</a><br><br>
            
            <div id="manual" style="display: none">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="manual_quiz_click" onclick="toggle_visibility('manual_quiz_click', 'manual_quiz');" class="cool-link"><span class="fa fa-chevron-down"></span> Quiz</a><br>
                <div id="manual_quiz" style="display: none">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./define_quiz.php?type=quiz&course=$course_id'" ?> class="cool-link">&#10070; Define Quiz</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./print_quiz_paper.php?type=quiz&course=$course_id'" ?> class="cool-link">&#10070; Print/Edit/Delete Quiz</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./grading_form_quiz_selection.php?type=quiz&course=$course_id'" ?> class="cool-link">&#10070; Online Grading Form </a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./print_grading_sheet_quiz.php?type=quiz&course=$course_id'" ?> class="cool-link">&#10070; Print Empty Grading Sheet</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./view_quiz.php?type=quiz&course=$course_id'" ?> class="cool-link">&#10070; Upload Result</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./view_quiz_result.php?type=quiz&course=$course_id'" ?> class="cool-link">&#10070; View/Delete Result</a><br>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./sample_paper_quiz_selection.php?type=quiz&course=$course_id&upload=1'" ?> class="cool-link">&#10070; Upload Sample Solution</a><br>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./sample_paper_quiz_selection.php?type=quiz&course=$course_id&upload=0'" ?> class="cool-link">&#10070; View/Edit/Delete Sample Solution</a><br>
                </div>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="manual_assign_click" onclick="toggle_visibility('manual_assign_click', 'manual_assign');" class="cool-link"><span class="fa fa-chevron-down"></span> Assignment</a><br>
                <div id="manual_assign" style="display: none">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./define_assign_pro.php?type=assign&course=$course_id'" ?> class="cool-link">&#10070; Define Assignment</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./print_assign_paper.php?type=assign&course=$course_id'" ?> class="cool-link">&#10070; Print/Edit/Delete Assignment</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./grading_form_assign_selection.php?type=assign&course=$course_id'" ?> class="cool-link">&#10070; Online Grading Form </a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./print_grading_sheet_assign_pro.php?type=assign&course=$course_id'" ?> class="cool-link">&#10070; Print Empty Grading Sheet</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./view_assign_pro.php?type=assign&course=$course_id'" ?> class="cool-link">&#10070; Upload Result</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./select_assignment?type=assign&course=$course_id'" ?> class="cool-link">&#10070; View/Delete Result</a><br>
                     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./sample_paper_assign_selection.php?type=assign&course=$course_id&upload=1'" ?> class="cool-link">&#10070; Upload Sample Solution</a><br>
                     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./sample_paper_assign_selection.php?type=quiz&course=$course_id&upload=0'" ?> class="cool-link">&#10070; View/Edit/Delete Sample Solution</a><br>
                </div>

                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="manual_pro_click" onclick="toggle_visibility('manual_pro_click', 'manual_pro');" class="cool-link"><span class="fa fa-chevron-down"></span> Project</a><br>
                <div id="manual_pro" style="display: none">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./define_assign_pro.php?type=project&course=$course_id'" ?> class="cool-link">&#10070; Define Project</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./print_project_paper.php?type=project&course=$course_id'" ?> class="cool-link">&#10070; Print/Edit/Delete Project</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./grading_form_pro_selection.php?type=project&course=$course_id'" ?> class="cool-link">&#10070; Online Grading Form </a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./print_grading_sheet_assign_pro.php?type=project&course=$course_id'" ?> class="cool-link">&#10070; Print Empty Grading Sheet</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./view_assign_pro.php?type=project&course=$course_id'" ?> class="cool-link">&#10070; Upload Result</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./select_project.php?type=project&course=$course_id'" ?> class="cool-link">&#10070; View/Delete Result</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./sample_paper_pro_selection.php?type=project&course=$course_id&upload=1'" ?> class="cool-link">&#10070; Upload Sample Solution</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./sample_paper_pro_selection.php?type=project&course=$course_id&upload=0'" ?> class="cool-link">&#10070; View/Edit/Delete Sample Solution</a><br>
                </div>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="manual_mt_click" onclick="toggle_visibility('manual_mt_click', 'manual_mt');" class="cool-link"><span class="fa fa-chevron-down"></span> Midterm</a><br>
                <div id="manual_mt" style="display: none">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./define_quiz.php?type=midterm&course=$course_id'" ?> class="cool-link">&#10070; Define Midterm</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./print_mid_paper.php?type=midterm&course=$course_id'" ?> class="cool-link">&#10070; Print/Edit/Delete Midterm</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./grading_form_midterm_selection.php?type=midterm&course=$course_id'" ?> class="cool-link">&#10070; Online Grading Form</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./print_grading_sheet_quiz.php?type=midterm&course=$course_id'" ?> class="cool-link">&#10070; Print Empty Grading Sheet</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./view_quiz.php?type=midterm&course=$course_id'" ?> class="cool-link">&#10070; Upload Result</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./view_mid_result.php?type=quiz&course=$course_id'" ?> class="cool-link">&#10070; View/Delete Result</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./sample_paper_midterm_selection.php?type=midterm&course=$course_id&upload=1'" ?> class="cool-link">&#10070; Upload Sample Solution</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./sample_paper_midterm_selection.php?type=midterm&course=$course_id&upload=0'" ?> class="cool-link">&#10070; View/Edit/Delete Sample Solution</a><br>
                </div>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="manual_fe_click" onclick="toggle_visibility('manual_fe_click', 'manual_fe');" class="cool-link"><span class="fa fa-chevron-down"></span> Final Exam</a><br>
                <div id="manual_fe" style="display: none">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./define_quiz.php?type=finalexam&course=$course_id'" ?> class="cool-link">&#10070; Define Final Exam</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./print_final_paper.php?type=finalexam&course=$course_id'" ?> class="cool-link">&#10070; Print/Edit/Delete Final Exam</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./grading_form_final_selection.php?type=finalexam&course=$course_id'" ?> class="cool-link">&#10070; Online Grading Form</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./print_grading_sheet_quiz.php?type=finalexam&course=$course_id'" ?> class="cool-link">&#10070; Print Empty Grading Sheet</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./view_quiz.php?type=finalexam&course=$course_id'" ?> class="cool-link">&#10070; Upload Result</a></a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./view_final_result.php?type=quiz&course=$course_id'" ?> class="cool-link">&#10070; View/Delete Result</a><br>
                    <!--
                     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php //echo "href='./sample_paper_final_selection.php?type=finalexam&course=$course_id&upload=1'" ?> class="cool-link">&#10070; Upload Sample Solution</a><br>
                     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php// echo "href='./sample_paper_final_selection.php?type=finalexam&course=$course_id&upload=0'" ?> class="cool-link">&#10070; View/Edit/Delete Sample Solution</a><br> -->
                </div>
           
                 
               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="other_click" onclick="toggle_visibility('other_click', 'other');" class="cool-link"><span class="fa fa-chevron-down"></span> Other</a><br>
                <div id="other" style="display: none">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./define_other.php?type=other&course=$course_id'" ?> class="cool-link">&#10070; Define Other</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./view_other1.php?type=other&course=$course_id'" ?> class="cool-link">&#10070; Edit/Delete Other</a><br>
                   
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./grading_form_other_selection.php?type=other&course=$course_id'" ?> class="cool-link">&#10070; Online Grading Form </a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./print_grading_sheet_other.php?type=other&course=$course_id'" ?> class="cool-link">&#10070; Print Empty Grading Sheet</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./view_other.php?type=other&course=$course_id'" ?> class="cool-link">&#10070; Upload Result</a><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./select_other.php?type=other&course=$course_id'" ?> class="cool-link">&#10070; View/Delete Result</a><br>
                     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./sample_paper_other_selection.php?type=other&course=$course_id&upload=1'" ?> class="cool-link">&#10070; Upload Sample Solution</a><br>

                     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./sample_paper_other_selection.php?type=other&course=$course_id&upload=0'" ?> class="cool-link">&#10070; View/Edit/Delete Sample Solution</a><br>
                </div>

                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="./map_manual_activity.php?course=<?php echo $course_id; ?>" class="cool-link">&#10070; Map Subactivities To Parent Activity</a><br>
               
            </div>

            <br>
            &nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="online_click" onclick="toggle_visibility('online_click', 'online');" class="cool-link"><span class="fa fa-chevron-down"></span> Online Evaluation</a><br><br>
            <div id="online" style="display: none">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='../../../course/modedit.php?add=quiz&type=&course=$course_id&section=0&return=0&sr=0'" ?> class="cool-link">&#10070; Define Quiz/Midterm</a><br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='../../../course/modedit.php?add=assign&type=&course=$course_id&section=0&return=0&sr=0'" ?> class="cool-link">&#10070; Define Assignment</a><br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./map_grading_item.php?course=$course_id'" ?> class="cool-link">&#10070; Map Activities to Grading items</a><br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a <?php echo "href='./display_quizzes-2.php?course=$course_id'" ?> class="cool-link">&#10070; Map Questions to CLOs</a><br><br>
            </div>
        </div>
        
        <!--<a "href='../../../question/edit.php?cmid=13'" class="cool-link">Add Questions to Assessment</a><br><br>

        <a href="./display_courses-4.php" class="cool-link">View Course CLOs Mapping to Levels &amp; PLOs</a><br><br>-->
        
        <a href="javascript:void(0)" id="clorep_click" onclick="toggle_visibility('clorep_click', 'clorep');" class="cool-link"><span class="fa fa-chevron-down"></span> CLO Reports</a><br><br>
        <div id="clorep" style="display: none">
            &nbsp;&nbsp;&nbsp;<a <?php echo "href='./display_activities-2.php?course=$course_id'" ?> class="cool-link">&#10070; Activity Detailed Report</a><br>
            &nbsp;&nbsp;&nbsp;<a <?php echo "href='./course_report.php?course=$course_id'" ?> class="cool-link">&#10070; Course Report</a><br>
            &nbsp;&nbsp;&nbsp;<a <?php echo "href='./display_activities.php?course=$course_id'" ?> class="cool-link">&#10070; Activity CLO Report</a><br>
            &nbsp;&nbsp;&nbsp;<a <?php echo "href='./clo_wise_report.php?course=$course_id'" ?> class="cool-link">&#10070; Student CLO-wise Report</a><br><br>
        </div>
        
    </div>

    <script type="text/javascript">
        function toggle_visibility(id_click, id) {
            var e = document.getElementById(id);
            var e_click = document.getElementById(id_click).getElementsByTagName("span")[0];
            if(e.style.display == 'block') {
                e.style.display = 'none';
                e_click.className = "fa fa-chevron-down";
            }
            else {
                e.style.display = 'block';
                e_click.className = "fa fa-chevron-up";
            }
        }
    </script>
    <?php
    }
    else
    {?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="./teacher_courses.php">Back</a>
    <?php
    }
    echo $OUTPUT->footer();
?>
