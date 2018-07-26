<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("View Result");
    $PAGE->set_heading("View Result");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/view_activity_result1.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <script src="../script/table2excel/jquery.table2excel.min.js"></script>
    <?php
    if(!empty($_GET['quiz']) && !empty($_GET['courseid']))
    {
    $course_id=$_GET['courseid'];
    $course_id = (int)$course_id; // convert course id from string to int
    $coursecontext = context_course::instance($course_id);
    is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
    $qids = array();
    $quizId = $_GET['quiz'];
    $type = $_GET['type'];

    // Report Header (Uni. name, Dept. name, course code and title)
    $un=$DB->get_records_sql('SELECT * FROM  `mdl_vision_mission` WHERE idnumber = ?', array("un"));
    if($un){
        foreach($un as $u){
            $uniName = $u->description;
        }
        $uniName = strip_tags($uniName); 
        echo "<h3 style='text-align:center'>".strtoupper($uniName)."</h3>";         
    }
    $dn=$DB->get_records_sql('SELECT * FROM  `mdl_vision_mission` WHERE idnumber = ?', array("dn"));
    if($dn){
        foreach($dn as $d){
            $deptName = $d->description;
        }
        $deptName = strip_tags($deptName); 
        echo "<h3 style='text-align:center'>".strtoupper($deptName)."</h3>";         
    }
    $course = $DB->get_record('course',array('id' => $course_id));
    echo "<h4 style='text-align:center'>Course Code: <u>".($course->idnumber)."</u>,";
    echo " Course Title: <u>".($course->fullname)." (".($course->shortname).")</u></h4>";
    echo "<h4 style='text-align:center'>Activity Detailed Report</h4>";

    //echo "$quizId";

    $rec1=$DB->get_recordset_sql('SELECT mq.name FROM mdl_manual_quiz mq WHERE mq.id=?',array($quizId));

    if($rec1){

        foreach ($rec1 as $r) {
            $name = $r->name;
        }

        echo "<h3>".$name."</h3>";
    }

    $quesnames=array(); 
    $quesmarks=array(); 
        
    $ques=$DB->get_records_sql("SELECT qq.id, qq.quesname, qq.maxmark, qq.cloid, c.shortname FROM mdl_manual_quiz_question qq, mdl_competency c WHERE mquizid=$quizId AND qq.cloid=c.id ORDER BY id");
    $obtMarksq=$DB->get_records_sql("SELECT qat.id, qat.obtmark, substring(u.username,4,8) AS seatorder,u.username ,qat.userid FROM mdl_manual_quiz_attempt qat, mdl_user u WHERE quizid=$quizId AND qat.userid=u.id ORDER BY seatorder, qat.questionid");
    $obtMarks=array();
    $userNames = array();
    //$cloids=array();
    $cloShortNames=array();
    $userIds = array();
    if($obtMarksq)
    {
        foreach ($obtMarksq as $omark) {
            $username = $omark->username;
            $obtmark = $omark->obtmark;
            $userId = $omark->userid;
            array_push($userNames,$username);
            array_push ($obtMarks,$obtmark);
            array_push($userIds, $userId);
        }
    }
    else
    {
    echo "<font color=red>The selected activity has not been graded yet!</font>";
        goto down;
    }
    //var_dump($obtMarks);
    if($ques)
    {
        $quizmaxmark = 0; // maxmarks of quiz
        foreach ($ques as $q) {
            $qname = $q->quesname;
            $id = $q->id;
            $maxmark=$q->maxmark; 
            //$cloid=$q->cloid;
            $shortname=$q->shortname;
            $quizmaxmark += $maxmark;
            array_push ($cloShortNames,$shortname);
            //array_push ($cloids,$cloid);
            array_push ($quesnames,$qname);
            array_push ($qids,$id);
            array_push ($quesmarks,$maxmark);
        }
        echo "<h3>"."Max Marks:"." ".$quizmaxmark."</h3>";
    }
    //var_dump($quesnames);
    //var_dump($qids);
    //var_dump($quesmarks);
    //var_dump($cloids);
    //$cloShortNames=array();
    
    ?>
    <table class="generaltable" id ="mytable">
    <tr>
    <th> Seat No. </th>
    <?php
    $marksIndex=0;
    foreach ($quesnames as $qname){
        ?><th> <?php echo $qname ." [$quesmarks[$marksIndex]]"; echo "<br>"; echo $cloShortNames[$marksIndex] ; 
        $marksIndex++ ?> </th>
        <?php
    }
    ?>
    <th> Delete </th>
    </tr>
    <?php
    $i = 0;
    foreach ($userNames as $un) {
        if($i == count($obtMarks)) // obt marks array exhausted
            break;
        ?>
        <tr>
            <td>
                <?php echo strtoupper($userNames[$i]); // display username once every record ?>
            </td><?php
            foreach ($qids as $qid){?>
            <td ><?php echo $obtMarks[$i]; $i++; ?></td>
            <?php
            }?>
            <td><?php $u = $i-1; echo"<a href='delete_quiz_marks.php?quizid=$quizId&userId=$userIds[$u]&courseid=$course_id'><i class='icon fa fa-trash text-danger' aria-hidden='true' title='Delete'onClick=\"return confirm('Are you sure you want to delete the marks of all questions for the following Roll no?')\" aria-label='Delete'></i></a><br>"; ?></td>
        </tr>
        <?php
    }
    ?>
    
    </table>
    <br />
    <button id="myButton" class="btn btn-primary">Export to Excel</button>
    <?php require '../templates/print_template.html'; ?>
    <!-- Export html Table to xls -->
    <script type="text/javascript" >
        $(document).ready(function(e){
            $("#myButton").click(function(e){ 
                $("#mytable").table2excel({
                    name: "file name",
                    filename: "quiz_result",
                    fileext: ".xls"
                });
            });
        });
    </script>

    <?php
    if($type == "quiz"){
    ?>
    <a class="btn btn-default" href="./view_quiz_result.php?type=<?php echo $type ?>&course=<?php echo $course_id ?>">Go Back</a>
    <?php
    }
    elseif($type == "midterm"){
    ?>
    <a class="btn btn-default" href="./view_mid_result.php?type=<?php echo $type ?>&course=<?php echo $course_id ?>">Go Back</a>
    <?php
    }
    elseif($type == "finalexam"){
    ?>
    <a class="btn btn-default" href="./view_final_result.php?type=<?php echo $type ?>&course=<?php echo $course_id ?>">Go Back</a>
    <?php
    }
    ?>

    <?php
    }
    else{
        ?>
        <h2 style="color:red;"> Invalid Selection </h2>
        <a href="../teacher/teacher_courses.php">Back</a>
    <?php
    }
down:
    echo $OUTPUT->footer();