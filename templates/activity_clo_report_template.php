
     <?php 

      //  echo "<h4 style='color:navy'>Note: Only VIEWED activities can be added to the consolidated report.</h4><br>";

        // Dispaly all quizzes
        $recQ=$DB->get_records_sql('SELECT * FROM  `mdl_quiz` WHERE course = ? AND id IN (SELECT quiz FROM `mdl_quiz_attempts`)', array($course_id));
        $recA=$DB->get_records_sql('SELECT * FROM `mdl_assign` WHERE course = ? AND id IN (SELECT assignment FROM `mdl_assign_grades`)', array($course_id));
        $statusQuery=$DB->get_records_sql('SELECT id, instance, module FROM `mdl_consolidated_report` WHERE course = ? AND form = ?', array($course_id,"online"));

        $statusArray = array();
        $modArray = array();

        foreach ($statusQuery as $state) {
           
            $sta = $state->instance;
            $mod = $state ->module;
           
            array_push($statusArray, $sta);
            array_push($modArray, $mod);
        }
                
        if($recQ || $recA){
            ?>
            <!--<form method='post' action='activity_comp_report.php' id="form_check">-->
            <?php
            $serialno = 0;
            $table = new html_table();
            echo "<h3>Online Activities</h3>";
            $table->head = array('S. No.', 'Name', 'Intro');
            foreach ($recQ as $records) {
                $serialno++;
               // $Status='<span style="color: red;">NOT VIEWED</span>';
                $id = $records->id;

                /*for ($i=0; $i< sizeof($statusArray); $i++ )
                {

                      if($id == $statusArray[$i] && $modArray[$i] == 16)
                        {
                            $Status='<span style="color: #006400;">VIEWED</span>';
                            break;
                        }

                }*/
                
                $id = 'Q'.$records->id;
                $courseid = $records->course;
                $name = $records->name;
                $intro = $records->intro;
                
                $table->data[] = array($serialno, "<a href='./activity_comp_report.php?course=$course_id&activityid=$id'>$name</a>", "<a href='./activity_comp_report.php?course=$course_id&activityid=$id'>$intro</a>");//, $Status);
            }
            foreach ($recA as $records) {
                $serialno++;
               // $Status='<span style="color: red;">NOT VIEWED</span>';
                $id = $records->id;
/*
                for ($i=0; $i< sizeof($statusArray); $i++ )
                {

                      if($id == $statusArray[$i] && $modArray[$i] == 1)
                        {
                            $Status='<span style="color: #006400;">VIEWED</span>';
                            break;
                        }

                }*/
                $id = 'A'.$records->id;
                $courseid = $records->course;
                $name = $records->name;
                $intro = $records->intro;
                $table->data[] = array($serialno, "<a href='./activity_comp_report.php?course=$course_id&activityid=$id'>$name</a>", "<a href='./activity_comp_report.php?course=$course_id&activityid=$id'>$intro</a>");//, $Status);
            }
            
            echo html_writer::table($table);
            ?>
            <!--<input type="hidden" value='<?php echo $course_id; ?>' name="courseid">
            <input type="submit" value="NEXT" name="submit" class="btn btn-primary">
            </form>-->
            <br />
            <p id="msg"></p>
            <br />
            

            <script>
            $('#form_check').on('submit', function (e) {
                if ($("input[type=radio]:checked").length === 0) {
                    e.preventDefault();
                    $("#msg").html("<font color='red'>Select any one activity!</font>");
                    return false;
                }
            });
            </script>

            <?php
        }
        else{
            echo "<h3>No Online activity found!</h3>";
        }

        $statusQuery=$DB->get_records_sql('SELECT id, instance, module FROM `mdl_consolidated_report` WHERE course = ? AND module = ?', array($course_id, -1));
        $mstatusarray = array();
        $mmodArray = array();

        foreach ($statusQuery as $state) {
           
            $sta = $state->instance;
            $mod = $state ->module;
           
            array_push($mstatusarray, $sta);
            array_push($mmodArray, $mod);
        }
        // Dispaly all Manual Quizzes
        $recMQ = $DB->get_records_sql("SELECT * FROM mdl_manual_quiz WHERE courseid = ? AND module = ? AND id IN (SELECT quizid FROM `mdl_manual_quiz_attempt`)",array($course_id,-1));
        if($recMQ){
            echo "<h3>Manual Quizzes</h3>";
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Name', 'Intro');
            foreach ($recMQ as $records) {
                $serialno++;
                $id = $records->id;
               // $Status='<span style="color: red;">NOT VIEWED</span>';
               /* for ($i=0; $i< sizeof($mstatusarray); $i++ )
                {
                    if($id == $mstatusarray[$i] && $mmodArray[$i] == -1)
                    {
                        $Status='<span style="color: #006400;">VIEWED</span>';
                        break;
                    }
                }*/
                //$courseid = $records->course;
                $id = 'Q'.$records->id;
                $name = $records->name;
                $intro = $records->description;
                $table->data[] = array($serialno, "<a href='./manual_activity_comp_report.php?course=$course_id&activityid=$id&module=-1'>$name</a>", "<a href='./manual_activity_comp_report.php?course=$course_id&activityid=$id&module=-1'>$intro</a>");//, $Status);
            }
            echo html_writer::table($table);
            ?>
            <br />
            <?php
        }
        $statusQuery=$DB->get_records_sql('SELECT id, instance, module FROM `mdl_consolidated_report` WHERE course = ? AND module = ?', array($course_id, -4));
        $mstatusarray = array();
        $mmodArray = array();

        foreach ($statusQuery as $state) {
           
            $sta = $state->instance;
            $mod = $state ->module;
           
            array_push($mstatusarray, $sta);
            array_push($mmodArray, $mod);
        }
        // Dispaly all Manual Assignments
        $recMA=$DB->get_records_sql('SELECT * FROM  `mdl_manual_assign_pro` WHERE courseid = ? AND module= ? AND id IN (SELECT assignproid FROM `mdl_manual_assign_pro_attempt`)', array($course_id,'-4'));
        if($recMA){
            echo "<h3>Manual Assignments</h3>";
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Name', 'Intro');
            foreach ($recMA as $records) {
                $serialno++;
                $id = $records->id;
                //$Status='<span style="color: red;">NOT VIEWED</span>';
               /* for ($i=0; $i< sizeof($mstatusarray); $i++ )
                {
                    if($id == $mstatusarray[$i] && $mmodArray[$i] == -4)
                    {
                        $Status='<span style="color: #006400;">VIEWED</span>';
                        break;
                    }
                }*/
                $id = 'A'.$records->id;
                $name = $records->name;
                $intro = $records->description;
                $table->data[] = array($serialno, "<a href='./manual_activity_comp_report.php?course=$course_id&activityid=$id&module=-4'>$name</a>", "<a href='./manual_activity_comp_report.php?course=$course_id&activityid=$id&module=-4'>$intro</a>");//, $Status);
            }
            echo html_writer::table($table);
            ?>
            <br />
            <?php
        }
        $statusQuery=$DB->get_records_sql('SELECT id, instance, module FROM `mdl_consolidated_report` WHERE course = ? AND module = ?', array($course_id, -5));
        $mstatusarray = array();
        $mmodArray = array();

        foreach ($statusQuery as $state) {
           
            $sta = $state->instance;
            $mod = $state ->module;
           
            array_push($mstatusarray, $sta);
            array_push($mmodArray, $mod);
        }
        // Dispaly all Manual Projects
        $recMP=$DB->get_records_sql('SELECT * FROM  `mdl_manual_assign_pro` WHERE courseid = ? AND module= ? AND id IN (SELECT assignproid FROM `mdl_manual_assign_pro_attempt`)', array($course_id,'-5'));
        if($recMP){
            echo "<h3>Manual Projects</h3>";
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Name', 'Intro');
            foreach ($recMP as $records) {
                $serialno++;
                $id = $records->id;
                /*$Status='<span style="color: red;">NOT VIEWED</span>';
                for ($i=0; $i< sizeof($mstatusarray); $i++ )
                {
                    if($id == $mstatusarray[$i] && $mmodArray[$i] == -5)
                    {
                        $Status='<span style="color: #006400;">VIEWED</span>';
                        break;
                    }
                }*/
                $id = 'A'.$records->id;
                $name = $records->name;
                $intro = $records->description;
                $table->data[] = array($serialno, "<a href='./manual_activity_comp_report.php?course=$course_id&activityid=$id&module=-5'>$name</a>", "<a href='./manual_activity_comp_report.php?course=$course_id&activityid=$id&module=-5'>$intro</a>");//, $Status);
            }
            echo html_writer::table($table);
            ?>
            <br />
            <?php
        }
        $statusQuery=$DB->get_records_sql('SELECT id, instance, module FROM `mdl_consolidated_report` WHERE course = ? AND module = ?', array($course_id, -2));
        $mstatusarray = array();
        $mmodArray = array();

        foreach ($statusQuery as $state) {
           
            $sta = $state->instance;
            $mod = $state ->module;
           
            array_push($mstatusarray, $sta);
            array_push($mmodArray, $mod);
        }
        // Dispaly all Manual Midterm
        $recMM = $DB->get_records_sql("SELECT * FROM mdl_manual_quiz WHERE courseid = ? AND module = ? AND id IN (SELECT quizid FROM `mdl_manual_quiz_attempt`)",array($course_id,-2));
        if($recMM){
            echo "<h3>Manual Midterm</h3>";
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Name', 'Intro');
            foreach ($recMM as $records) {
                $serialno++;
                $id = $records->id;
                /*$Status='<span style="color: red;">NOT VIEWED</span>';
                for ($i=0; $i< sizeof($mstatusarray); $i++ )
                {
                    if($id == $mstatusarray[$i] && $mmodArray[$i] == -2)
                    {
                        $Status='<span style="color: #006400;">VIEWED</span>';
                        break;
                    }
                }*/
                //$courseid = $records->course;
                $id = 'Q'.$records->id;
                $name = $records->name;
                $intro = $records->description;
                $table->data[] = array($serialno, "<a href='./manual_activity_comp_report.php?course=$course_id&activityid=$id&module=-2'>$name</a>", "<a href='./manual_activity_comp_report.php?course=$course_id&activityid=$id&module=-2'>$intro</a>");//, $Status);
            }
            echo html_writer::table($table);
            ?>
            <br />
            <?php
        }
        $statusQuery=$DB->get_records_sql('SELECT id, instance, module FROM `mdl_consolidated_report` WHERE course = ? AND module = ?', array($course_id, -3));
        $mstatusarray = array();
        $mmodArray = array();

        foreach ($statusQuery as $state) {
           
            $sta = $state->instance;
            $mod = $state ->module;
           
            array_push($mstatusarray, $sta);
            array_push($mmodArray, $mod);
        }
        // Dispaly all Manual Final
        $recMF = $DB->get_records_sql("SELECT * FROM mdl_manual_quiz WHERE courseid = ? AND module = ? AND id IN (SELECT quizid FROM `mdl_manual_quiz_attempt`)",array($course_id,-3));
        if($recMF){
            echo "<h3>Manual Final Exam</h3>";
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Name', 'Intro');
            foreach ($recMF as $records) {
                $serialno++;
                $id = $records->id;
                /*$Status='<span style="color: red;">NOT VIEWED</span>';
                for ($i=0; $i< sizeof($mstatusarray); $i++ )
                {
                    if($id == $mstatusarray[$i] && $mmodArray[$i] == -3)
                    {
                        $Status='<span style="color: #006400;">VIEWED</span>';
                        break;
                    }
                }*/
                //$courseid = $records->course;
                $id = 'Q'.$records->id;
                $name = $records->name;
                $intro = $records->description;
                $table->data[] = array($serialno, "<a href='./manual_activity_comp_report.php?course=$course_id&activityid=$id&module=-3'>$name</a>", "<a href='./manual_activity_comp_report.php?course=$course_id&activityid=$id&module=-3'>$intro</a>");//, $Status);
            }
            echo html_writer::table($table);
            ?>
            <br />
            <?php
        }


        $statusQuery=$DB->get_records_sql('SELECT id, instance, module FROM `mdl_consolidated_report` WHERE course = ? AND module = ?', array($course_id, -6));
        $mstatusarray = array();
        $mmodArray = array();

        foreach ($statusQuery as $state) {
           
            $sta = $state->instance;
            $mod = $state ->module;
           
            array_push($mstatusarray, $sta);
            array_push($mmodArray, $mod);
        }
        // Dispaly all Manual Other
        $recMO = $DB->get_records_sql("SELECT * FROM mdl_manual_other WHERE courseid = ? AND module = ? AND id IN (SELECT otherid FROM `mdl_manual_other_attempt`)",array($course_id,-6));
        if($recMO){
            echo "<h3>Manual Other</h3>";
            $serialno = 0;
            $table = new html_table();
            $table->head = array('S. No.', 'Name', 'Intro');
            foreach ($recMO as $records) {
                $serialno++;
                $id = $records->id;
                /*$Status='<span style="color: red;">NOT VIEWED</span>';
                for ($i=0; $i< sizeof($mstatusarray); $i++ )
                {
                    if($id == $mstatusarray[$i] && $mmodArray[$i] == -6)
                    {
                        $Status='<span style="color: #006400;">VIEWED</span>';
                        break;
                    }
                }*/
                //$courseid = $records->course;
                $id = 'O'.$records->id;
                $name = $records->name;
                $intro = $records->description;
                $table->data[] = array($serialno, "<a href='./manual_activity_comp_report.php?course=$course_id&activityid=$id&module=-6'>$name</a>", "<a href='./manual_activity_comp_report.php?course=$course_id&activityid=$id&module=-6'>$intro</a>");//, $Status);
            }
            echo html_writer::table($table);
            ?>
            <br />
            <?php
        }




        /*$recQ=$DB->get_records_sql('SELECT * FROM  `mdl_manual_quiz` WHERE courseid = ? AND id IN (SELECT quizid FROM `mdl_manual_quiz_attempt`)', array($course_id));
        $recA=$DB->get_records_sql('SELECT * FROM `mdl_manual_assign_pro` WHERE courseid = ? AND id IN (SELECT assignproid FROM `mdl_manual_assign_pro_attempt`)', array($course_id));
        $statusQuery=$DB->get_records_sql('SELECT id, instance, module FROM `mdl_consolidated_report` WHERE course = ? AND form = ?', array($course_id,"manual"));

        $mstatusarray = array();
        $mmodArray = array();

        foreach ($statusQuery as $state) {
           
            $sta = $state->instance;
            $mod = $state ->module;
           
            array_push($mstatusarray, $sta);
            array_push($mmodArray, $mod);
        }
                
        if($recQ || $recA){
            ?>
            <!--<form method='post' action='manual_activity_comp_report.php' id="form_check2">-->
            <?php
            $serialno = 0;
            $table = new html_table();
            echo "<h3>Manual Activities</h3>";
            $table->head = array('S. No.', 'Name', 'Intro', 'Status');
            foreach ($recQ as $records) {
                $serialno++;
                $Status='<span style="color: red;">NOT VIEWED</span>';
                $id = $records->id;

                for ($i=0; $i< sizeof($mstatusarray); $i++ )
                {

                      if($id == $mstatusarray[$i] && $mmodArray[$i] == 16)
                        {
                            $Status='<span style="color: #006400;">VIEWED</span>';
                            break;
                        }

                }
                
                $id = 'Q'.$records->id;
                $courseid = $records->courseid;
                $name = $records->name;
                $intro = $records->description;
                
                $table->data[] = array($serialno, "<a href='./manual_activity_comp_report.php?course=$course_id&activityid=$id'>$name</a>", "<a href='./manual_activity_comp_report.php?course=$course_id&activityid=$id'>$intro</a>", $Status);
            }
            foreach ($recA as $records) {
                $serialno++;
                $Status='<span style="color: red;">NOT VIEWED</span>';
                $id = $records->id;

                for ($i=0; $i< sizeof($mstatusarray); $i++ )
                {

                      if($id == $mstatusarray[$i] && $mmodArray[$i] == 1)
                        {
                            $Status='<span style="color: #006400;">VIEWED</span>';
                            break;
                        }

                }
                $id = 'A'.$records->id;
                $courseid = $records->courseid;
                $name = $records->name;
                $intro = $records->description;
                $table->data[] = array($serialno, "<a href='./manual_activity_comp_report.php?course=$course_id&activityid=$id'>$name</a>", "<a href='./manual_activity_comp_report.php?course=$course_id&activityid=$id'>$intro</a>", $Status);
            }
            
            echo html_writer::table($table);
            ?>
            <!--<input type="hidden" value='<?php echo $course_id; ?>' name="courseid">
            <input type="submit" value="NEXT" name="submit" class="btn btn-primary">
            </form>-->
            <br />
            <p id="msg2"></p>
            <br />
            <!--<form method='post' action='consolidated_report_selection.php'>
                <input type="hidden" value='<?php echo $course_id; ?>' name="courseid">
                <input type="submit" value="View Consolidated Report" name="view_consolidated" class="btn btn-secondary">
            </form>-->
            

            <script>
            $('#form_check2').on('submit', function (e) {
                if ($("input[type=radio]:checked").length === 0) {
                    e.preventDefault();
                    $("#msg2").html("<font color='red'>Select any one activity!</font>");
                    return false;
                }
            });
            </script>

            <?php
        }
        else{
            echo "<h3>No Manual activity found!</h3>";
        }
        */
        ?>

        <!--<br><br><a href="consolidated_report_selection.php?course=<?php// echo $course_id; ?>" class="btn btn-success">View Consolidated Report</a> -->

    