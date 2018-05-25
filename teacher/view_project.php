<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Projects Results");
    $PAGE->set_heading("Projects Results");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/view_project.php');

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
    if(isset($_GET['projectid']))
    {
        $project_id=$_GET['projectid'];
        //echo $assign_id;


        //$id=$_POST['id'];
        //echo $id;
        $rec1=$DB->get_recordset_sql('SELECT ma.name,ma.maxmark,comp.idnumber,ma.cloid, comp.id from mdl_manual_assign_pro ma,mdl_competency comp WHERE comp.id=ma.cloid AND ma.id=?',array($project_id));

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
        'SELECT substring(us.username,4,8) AS seatorder,us.username,us.id,maa.obtmark, ma.id,maa.id from mdl_manual_assign_pro_attempt maa , mdl_manual_assign_pro ma, mdl_user us where us.id=maa.userid AND ma.id=maa.assignproid AND ma.id= ? AND ma.module=? ORDER BY seatorder ',array($project_id,'-5'));

if($rec){

 
              $serialno = 0;
            $table = new html_table();
            $table->id = "mytable";
     $table->head = array('S. No.', 'Seat No.', 'Marks Obtained','Edit');



     foreach ($rec as $records) {
                $serialno++;
                $userid = $records->username;
                $obtmark = $records->obtmark;
                 $marksid=$records->id;

$table->data[] = array($serialno,$userid,$obtmark,"<a href='edit_project_marks.php?edit=$marksid&userid=$userid&projectid=$project_id' title='Edit'><img src='../img/icons/edit.png' /></a>");

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
                filename: "project_result",
                fileext: ".xls"
            });
        });
    });
</script>
<?php
}

  else{
            echo "<h3>No students have submitted project yet!</h3>";
        }



}





?>


 <?php 
        echo $OUTPUT->footer();
    ?>
