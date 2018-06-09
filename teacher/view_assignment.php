<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Assignment Results");
    $PAGE->set_heading("Assignment Results");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/view_assignment.php');

    header('Content-Type: text/plain');
   
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
    ?>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <script src="../script/table2excel/jquery.table2excel.min.js"></script>
    <?php

    if(!empty($_GET['assignid']) && !empty($_GET['courseid']) && !empty($_GET['type']))
    {
    $assign_id=$_GET['assignid'];
    //echo $assign_id;
    $courseid=$_GET['courseid'];

    //$id=$_POST['id'];
    //echo $id;
    $rec1=$DB->get_recordset_sql('SELECT ma.name,ma.maxmark,comp.idnumber,ma.cloid, comp.id from mdl_manual_assign_pro ma,mdl_competency comp WHERE comp.id=ma.cloid AND ma.id=?',array($assign_id));

    if($rec1){

        foreach ($rec1 as $records) {
            $name = $records->name;
            $clo=$records->idnumber;
            $maxmark=$records->maxmark;
        }

        echo "<h3>".$name." "."(".$clo.")"."</h3>";

        echo "<h3>"."Max Marks:"." ".$maxmark."</h3>";
    }
    else{
	    echo "No record present!";
    }
    $rec=$DB->get_recordset_sql(
        'SELECT substring(us.username,4,8) AS seatorder,us.username,us.id,maa.obtmark, ma.id,maa.id from mdl_manual_assign_pro_attempt maa , mdl_manual_assign_pro ma, mdl_user us where us.id=maa.userid AND ma.id=maa.assignproid  AND ma.id= ? AND ma.module=? ORDER BY seatorder ',array($assign_id,'-4'));

    if($rec){

 
        $serialno = 0;
        $table = new html_table();
        $table->id = "mytable";
        $table->head = array('S. No.', 'Seat No.', 'Marks Obtained','Delete');

        foreach ($rec as $records) {
            $serialno++;
            $marksid=$records->id;
            $userid = $records->username;
            $obtmark = $records->obtmark;
            //"<a href='edit_assignment_marks.php?edit=$marksid' title='Edit'><img src='../img/icons/edit.png' /></a>";
            $table->data[] = array($serialno,strtoupper($userid),$obtmark, "<a href='delete_assignment_marks.php?delete=$marksid&courseid=$courseid&assignid=$assign_id'><i class='icon fa fa-trash text-danger' aria-hidden='true' title='Delete'onClick=\"return confirm('Are you sure you want to delete the marks of assigment for the Roll no. $userid?')\"  aria-label='Delete'></i></a><br></a>");

        }
        echo html_writer::table($table);
    ?>
    <button id="myButton" class="btn btn-success">Export to Excel</button>
    <!-- Export html Table to xls -->
    <script type="text/javascript" >
        $(document).ready(function(e){
            $("#myButton").click(function(e){ 
                $("#mytable").table2excel({
                    name: "file name",
                    filename: "assignment_result",
                    fileext: ".xls"
                });
            });
        });
    </script>
    <?php
    }
    else{
        echo "<h3>No students have attempted Assignment yet!</h3>";
    }
    ?>
    <a class="btn btn-default" href="./select_assignment?type=assign&course=<?php echo $courseid ?>">Go Back</a>
    <?php
}
    echo $OUTPUT->footer();
?>
